<?php
/**
 * Write authentication: a single shared token sent by the Hermes worker.
 *
 * Reads are public by design (the dashboard replaces a public GitHub Pages
 * site); every mutating route goes through check_request().
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Auth
{
    public const HEADER = 'x-hfcd-token';

    private const OPTION = 'hfcd_write_token';

    public static function token(): string
    {
        $token = get_option(self::OPTION);
        if (!is_string($token) || $token === '') {
            $token = self::rotate();
        }

        return $token;
    }

    public static function rotate(): string
    {
        $token = wp_generate_password(48, false);
        update_option(self::OPTION, $token, false);

        return $token;
    }

    /**
     * @return true|WP_Error
     */
    public static function check_request(WP_REST_Request $request)
    {
        $given = (string) $request->get_header(self::HEADER);
        if ($given === '' || !hash_equals(self::token(), $given)) {
            return new WP_Error(
                'hfcd_forbidden',
                'Missing or invalid X-HFCD-Token header.',
                ['status' => 403]
            );
        }

        return true;
    }
}
