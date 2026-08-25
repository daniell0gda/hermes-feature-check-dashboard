<?php
/**
 * REST surface.
 *
 * Writes require the worker token; reads are public. Media is uploaded one file
 * per request as a raw binary body, which keeps publishes well inside the usual
 * post_max_size and avoids base64 inflating every screenshot by a third.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Rest
{
    public const NAMESPACE = 'hfcd/v1';

    public static function init(): void
    {
        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    public static function register_routes(): void
    {
        $token = [self::class, 'authorize'];
        $public = '__return_true';
        $run_id_arg = [
            'run_id' => [
                'required' => true,
                'type' => 'string',
            ],
        ];

        register_rest_route(self::NAMESPACE, '/runs', [
            [
                'methods' => 'POST',
                'permission_callback' => $token,
                'callback' => [self::class, 'publish'],
            ],
            [
                'methods' => 'GET',
                'permission_callback' => $public,
                'callback' => [self::class, 'list_runs'],
            ],
        ]);

        register_rest_route(self::NAMESPACE, '/runs/(?P<run_id>[A-Za-z0-9._-]+)', [
            [
                'methods' => 'GET',
                'permission_callback' => $public,
                'callback' => [self::class, 'get_run'],
                'args' => $run_id_arg,
            ],
            [
                'methods' => 'DELETE',
                'permission_callback' => $token,
                'callback' => [self::class, 'delete_run'],
                'args' => $run_id_arg,
            ],
        ]);

        register_rest_route(self::NAMESPACE, '/runs/(?P<run_id>[A-Za-z0-9._-]+)/status', [
            'methods' => 'POST',
            'permission_callback' => $token,
            'callback' => [self::class, 'patch_status'],
            'args' => $run_id_arg,
        ]);

        register_rest_route(self::NAMESPACE, '/runs/(?P<run_id>[A-Za-z0-9._-]+)/heartbeat', [
            'methods' => 'POST',
            'permission_callback' => $token,
            'callback' => [self::class, 'heartbeat'],
            'args' => $run_id_arg,
        ]);

        register_rest_route(self::NAMESPACE, '/runs/(?P<run_id>[A-Za-z0-9._-]+)/media', [
            [
                'methods' => 'GET',
                'permission_callback' => $public,
                'callback' => [self::class, 'list_media'],
                'args' => $run_id_arg,
            ],
            [
                'methods' => 'POST',
                'permission_callback' => $token,
                'callback' => [self::class, 'upload_media'],
                'args' => $run_id_arg,
            ],
        ]);

        register_rest_route(self::NAMESPACE, '/summary', [
            'methods' => 'GET',
            'permission_callback' => $public,
            'callback' => [self::class, 'summary'],
        ]);
    }

    /**
     * @return true|WP_Error
     */
    public static function authorize(WP_REST_Request $request)
    {
        return HFCD_Auth::check_request($request);
    }

    public static function publish(WP_REST_Request $request)
    {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new WP_Error('hfcd_bad_body', 'Expected a JSON object.', ['status' => 400]);
        }

        $run_id = HFCD_Ingest::sanitize_run_id((string) ($body['run_id'] ?? ($body['status']['run_id'] ?? '')));
        if (is_wp_error($run_id)) {
            return $run_id;
        }

        return rest_ensure_response(HFCD_Ingest::publish($run_id, $body));
    }

    public static function patch_status(WP_REST_Request $request)
    {
        $run_id = HFCD_Ingest::sanitize_run_id((string) $request['run_id']);
        if (is_wp_error($run_id)) {
            return $run_id;
        }

        $body = $request->get_json_params();
        $patched = HFCD_Ingest::patch($run_id, is_array($body) ? $body : []);
        if (is_wp_error($patched)) {
            return $patched;
        }

        return rest_ensure_response([
            'ok' => true,
            'run_id' => $run_id,
            'status' => $patched['status'],
            'phase' => $patched['phase'],
        ]);
    }

    public static function heartbeat(WP_REST_Request $request)
    {
        $run_id = HFCD_Ingest::sanitize_run_id((string) $request['run_id']);
        if (is_wp_error($run_id)) {
            return $run_id;
        }
        if (!HFCD_Run_Store::exists($run_id)) {
            return new WP_Error('hfcd_not_found', 'Unknown run id: ' . $run_id, ['status' => 404]);
        }

        HFCD_Run_Store::touch_heartbeat($run_id);

        return rest_ensure_response(['ok' => true, 'run_id' => $run_id]);
    }

    public static function delete_run(WP_REST_Request $request)
    {
        $run_id = HFCD_Ingest::sanitize_run_id((string) $request['run_id']);
        if (is_wp_error($run_id)) {
            return $run_id;
        }
        if (!HFCD_Run_Store::exists($run_id)) {
            return new WP_Error('hfcd_not_found', 'Unknown run id: ' . $run_id, ['status' => 404]);
        }

        HFCD_Media_Store::delete_run($run_id);
        HFCD_Run_Store::delete($run_id);

        return rest_ensure_response(['ok' => true, 'run_id' => $run_id, 'deleted' => true]);
    }

    public static function list_media(WP_REST_Request $request)
    {
        $run_id = HFCD_Ingest::sanitize_run_id((string) $request['run_id']);
        if (is_wp_error($run_id)) {
            return $run_id;
        }

        return self::with_cors(rest_ensure_response([
            'run_id' => $run_id,
            'files' => HFCD_Media_Store::manifest($run_id),
        ]));
    }

    public static function upload_media(WP_REST_Request $request)
    {
        $run_id = HFCD_Ingest::sanitize_run_id((string) $request['run_id']);
        if (is_wp_error($run_id)) {
            return $run_id;
        }
        if (!HFCD_Run_Store::exists($run_id)) {
            return new WP_Error('hfcd_not_found', 'Publish the run before uploading its media.', ['status' => 404]);
        }

        $stored = HFCD_Media_Store::store($run_id, (string) $request->get_param('path'), (string) $request->get_body());
        if (is_wp_error($stored)) {
            return $stored;
        }

        return rest_ensure_response([
            'ok' => true,
            'run_id' => $run_id,
            'path' => $stored['rel_path'],
            'bytes' => (int) $stored['bytes'],
            'animated' => (bool) $stored['animated'],
            'checksum' => $stored['checksum'],
        ]);
    }

    public static function list_runs(WP_REST_Request $request)
    {
        $result = HFCD_Run_Store::query([
            'status' => (string) $request->get_param('status'),
            'feature' => (string) $request->get_param('feature'),
            'q' => (string) $request->get_param('q'),
            'page' => (int) ($request->get_param('page') ?: 1),
            'per_page' => (int) ($request->get_param('per_page') ?: 50),
        ]);

        $response = rest_ensure_response([
            'runs' => array_map([self::class, 'shape_run'], $result['rows']),
            'total' => $result['total'],
            'page' => $result['page'],
            'pages' => $result['pages'],
        ]);

        return self::with_cors($response);
    }

    public static function get_run(WP_REST_Request $request)
    {
        $run_id = HFCD_Ingest::sanitize_run_id((string) $request['run_id']);
        if (is_wp_error($run_id)) {
            return $run_id;
        }

        $run = HFCD_Run_Store::find($run_id);
        if ($run === null) {
            return new WP_Error('hfcd_not_found', 'Unknown run id: ' . $run_id, ['status' => 404]);
        }

        $events = HFCD_Event_Store::for_run($run_id);

        $payload = self::shape_run($run) + [
            'events' => array_map([self::class, 'shape_event'], $events),
            'stages' => HFCD_Workflow::stages($run, $events),
            'documents' => array_map(
                static fn (array $document): array => [
                    'slug' => $document['slug'],
                    'title' => $document['title'],
                    'kind' => $document['kind'],
                ],
                HFCD_Document_Store::for_run($run_id)
            ),
            'media' => array_map([self::class, 'shape_artifact'], HFCD_Media_Store::for_run($run_id)),
            'graph' => json_decode((string) $run['graph'], true) ?: null,
            'metrics' => json_decode((string) $run['metrics'], true) ?: null,
        ];

        return self::with_cors(rest_ensure_response($payload));
    }

    public static function summary(): WP_REST_Response
    {
        return self::with_cors(rest_ensure_response(HFCD_Run_Store::summary()));
    }

    private static function shape_run(array $run): array
    {
        return [
            'run_id' => $run['run_id'],
            'feature' => $run['feature'],
            'status' => $run['status'],
            'display_status' => HFCD_Format::display_status($run),
            'health' => HFCD_Format::health($run),
            'classification' => $run['classification'],
            'phase' => $run['phase'],
            'active_node' => $run['active_node'],
            'last_node' => $run['last_node'],
            'host' => $run['host'],
            'error' => $run['error'],
            'started_at' => HFCD_Format::iso($run['started_at']),
            'ended_at' => HFCD_Format::iso($run['ended_at']),
            'heartbeat_at' => HFCD_Format::iso($run['heartbeat_at']),
            'updated_at' => HFCD_Format::iso($run['updated_at']),
            'duration_ms' => self::nullable_int($run['duration_ms']),
            'worker_ms' => self::nullable_int($run['worker_ms']),
            'event_count' => (int) $run['event_count'],
            'revisions' => (int) $run['revisions'],
            'cost_usd' => $run['cost_usd'] === null ? null : (float) $run['cost_usd'],
            'input_tokens' => self::nullable_int($run['input_tokens']),
            'output_tokens' => self::nullable_int($run['output_tokens']),
            'url' => HFCD_View::run_url((string) $run['run_id']),
        ];
    }

    private static function shape_event(array $event): array
    {
        return [
            'sequence' => (int) $event['seq'],
            'node' => $event['node'],
            'status' => $event['status'],
            'duration_ms' => self::nullable_int($event['duration_ms']),
            'occurred_at' => HFCD_Format::iso($event['occurred_at']),
            'summary' => $event['summary'],
            'error' => $event['error'],
        ];
    }

    private static function shape_artifact(array $artifact): array
    {
        return [
            'path' => $artifact['rel_path'],
            'mime' => $artifact['mime'],
            'bytes' => (int) $artifact['bytes'],
            'width' => self::nullable_int($artifact['width']),
            'height' => self::nullable_int($artifact['height']),
            'animated' => (bool) $artifact['animated'],
            'checksum' => $artifact['checksum'],
            'url' => HFCD_Media_Store::url($artifact),
            'thumb_url' => HFCD_Media_Store::thumb_url($artifact),
        ];
    }

    private static function nullable_int($value): ?int
    {
        return $value === null ? null : (int) $value;
    }

    private static function with_cors(WP_REST_Response $response): WP_REST_Response
    {
        $response->header('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
