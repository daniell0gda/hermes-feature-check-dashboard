<?php
/**
 * Runs only when the plugin is deleted from the Plugins screen.
 * Drops the run tables, the stored media and the plugin options.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once __DIR__ . '/includes/class-hfcd-schema.php';

HFCD_Schema::drop();

delete_option('hfcd_write_token');
delete_option('hfcd_base_slug');

$hfcd_media_dir = wp_get_upload_dir()['basedir'] . '/hfcd';
if (is_dir($hfcd_media_dir)) {
    $hfcd_iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($hfcd_media_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($hfcd_iterator as $hfcd_entry) {
        $hfcd_entry->isDir() ? @rmdir($hfcd_entry->getPathname()) : @unlink($hfcd_entry->getPathname());
    }
    @rmdir($hfcd_media_dir);
}
