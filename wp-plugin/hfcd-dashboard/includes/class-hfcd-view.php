<?php
/**
 * Front-end routing.
 *
 * The dashboard renders as a standalone document rather than inside the active
 * theme: it is an operational tool that has to look the same on any site, and
 * theme CSS would fight the tables and the gallery.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_View
{
    public const QUERY_VIEW = 'hfcd_view';
    public const QUERY_RUN = 'hfcd_run';

    public const VIEW_LIST = 'list';
    public const VIEW_RUN = 'run';

    public const OPTION_SLUG = 'hfcd_base_slug';
    public const DEFAULT_SLUG = 'feature-check';

    public static function init(): void
    {
        add_action('init', [self::class, 'register_rewrites']);
        add_filter('query_vars', [self::class, 'query_vars']);
        add_action('template_redirect', [self::class, 'maybe_render']);
    }

    public static function slug(): string
    {
        $slug = (string) get_option(self::OPTION_SLUG, self::DEFAULT_SLUG);
        $slug = trim(sanitize_title($slug));

        return $slug !== '' ? $slug : self::DEFAULT_SLUG;
    }

    public static function base_url(): string
    {
        return home_url('/' . self::slug() . '/');
    }

    public static function run_url(string $run_id): string
    {
        return self::base_url() . 'run/' . rawurlencode($run_id) . '/';
    }

    /** @param array<string,string|int> $args */
    public static function list_url(array $args = []): string
    {
        $args = array_filter(
            $args,
            static fn ($value): bool => $value !== '' && $value !== null && $value !== 0
        );

        return $args === [] ? self::base_url() : add_query_arg($args, self::base_url());
    }

    public static function asset(string $file): string
    {
        return add_query_arg('v', HFCD_VERSION, HFCD_URL . 'assets/' . $file);
    }

    public static function register_rewrites(): void
    {
        $slug = preg_quote(self::slug(), '#');

        add_rewrite_rule(
            '^' . $slug . '/run/([A-Za-z0-9._-]+)/?$',
            'index.php?' . self::QUERY_VIEW . '=' . self::VIEW_RUN . '&' . self::QUERY_RUN . '=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^' . $slug . '/?$',
            'index.php?' . self::QUERY_VIEW . '=' . self::VIEW_LIST,
            'top'
        );
    }

    /** @param string[] $vars */
    public static function query_vars(array $vars): array
    {
        $vars[] = self::QUERY_VIEW;
        $vars[] = self::QUERY_RUN;

        return $vars;
    }

    public static function maybe_render(): void
    {
        $view = (string) get_query_var(self::QUERY_VIEW);
        if ($view === self::VIEW_LIST) {
            self::render_list();
        }
        if ($view === self::VIEW_RUN) {
            self::render_run((string) get_query_var(self::QUERY_RUN));
        }
    }

    private static function render_list(): void
    {
        $filters = [
            'status' => sanitize_key((string) ($_GET['status'] ?? '')),
            'q' => sanitize_text_field(wp_unslash((string) ($_GET['q'] ?? ''))),
            // Not `page`/`paged`: both are reserved WordPress query vars.
            'page' => max(1, (int) ($_GET['pg'] ?? 1)),
        ];

        $result = HFCD_Run_Store::query($filters + ['per_page' => 50]);

        self::render('list', [
            'filters' => $filters,
            'result' => $result,
            'summary' => HFCD_Run_Store::summary(),
            'active' => HFCD_Run_Store::active(),
        ]);
    }

    private static function render_run(string $run_id): void
    {
        $run = HFCD_Run_Store::find($run_id);
        if ($run === null) {
            self::render('not-found', ['run_id' => $run_id], 404);

            return;
        }

        $events = HFCD_Event_Store::for_run($run_id);

        self::render('run', [
            'run' => $run,
            'events' => $events,
            'stages' => HFCD_Workflow::stages($run, $events),
            'documents' => HFCD_Document_Store::for_run($run_id),
            'media' => HFCD_Media_Store::for_run($run_id),
        ]);
    }

    /**
     * @param array<string,mixed> $context
     */
    private static function render(string $template, array $context, int $status = 200): void
    {
        status_header($status);
        nocache_headers();
        header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
        // Operational data; keep it out of search results.
        header('X-Robots-Tag: noindex, nofollow', true);

        $file = HFCD_DIR . 'templates/' . $template . '.php';
        if (is_readable($file)) {
            (static function (string $__file, array $__context): void {
                extract($__context, EXTR_SKIP);
                require $__file;
            })($file, $context);
        }

        exit;
    }
}
