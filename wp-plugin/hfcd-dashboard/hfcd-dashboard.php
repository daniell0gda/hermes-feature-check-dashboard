<?php
/**
 * Plugin Name:       HFCD Dashboard
 * Description:       Hermes feature-check dashboard: token-authenticated ingest API, MySQL run storage and a rendered team/run view.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Hermes
 * License:           MIT
 */

if (!defined('ABSPATH')) {
    exit;
}

define('HFCD_VERSION', '1.0.0');
define('HFCD_FILE', __FILE__);
define('HFCD_DIR', plugin_dir_path(__FILE__));
define('HFCD_URL', plugin_dir_url(__FILE__));

foreach ([
    'class-hfcd-schema',
    'class-hfcd-auth',
    'class-hfcd-run-store',
    'class-hfcd-event-store',
    'class-hfcd-document-store',
    'class-hfcd-media-store',
    'class-hfcd-ingest',
    'class-hfcd-markdown',
    'class-hfcd-format',
    'class-hfcd-workflow',
    'class-hfcd-ui',
    'class-hfcd-rest',
    'class-hfcd-view',
    'class-hfcd-admin',
] as $hfcd_include) {
    require_once HFCD_DIR . 'includes/' . $hfcd_include . '.php';
}
unset($hfcd_include);

register_activation_hook(__FILE__, static function (): void {
    HFCD_Schema::install();
    HFCD_View::register_rewrites();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, static function (): void {
    flush_rewrite_rules();
});

add_action('plugins_loaded', [HFCD_Schema::class, 'install']);

HFCD_Rest::init();
HFCD_View::init();
HFCD_Admin::init();
