<?php
/**
 * Table names and schema migrations.
 *
 * All datetime columns hold UTC. Uniqueness on the child tables is carried by a
 * `uid` hash column so composite keys stay well inside the InnoDB index-prefix
 * limit regardless of MySQL version or row format.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Schema
{
    public const VERSION = 1;

    private const OPTION_VERSION = 'hfcd_db_version';

    public static function runs(): string
    {
        return self::table('runs');
    }

    public static function events(): string
    {
        return self::table('events');
    }

    public static function documents(): string
    {
        return self::table('documents');
    }

    public static function artifacts(): string
    {
        return self::table('artifacts');
    }

    /** Stable hash used as the uniqueness key on child rows. */
    public static function uid(string $run_id, string $path): string
    {
        return md5($run_id . '|' . $path);
    }

    public static function install(): void
    {
        if ((int) get_option(self::OPTION_VERSION) === self::VERSION) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        foreach (self::statements($charset) as $statement) {
            dbDelta($statement);
        }

        update_option(self::OPTION_VERSION, self::VERSION);
    }

    public static function drop(): void
    {
        global $wpdb;
        foreach ([self::artifacts(), self::documents(), self::events(), self::runs()] as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}"); // phpcs:ignore WordPress.DB
        }
        delete_option(self::OPTION_VERSION);
    }

    private static function table(string $name): string
    {
        global $wpdb;

        return $wpdb->prefix . 'hfcd_' . $name;
    }

    /** @return string[] */
    private static function statements(string $charset): array
    {
        return [
            "CREATE TABLE " . self::runs() . " (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    run_id varchar(128) NOT NULL,
    feature varchar(191) DEFAULT NULL,
    status varchar(32) NOT NULL DEFAULT 'unknown',
    classification varchar(32) DEFAULT NULL,
    phase varchar(64) DEFAULT NULL,
    active_node varchar(64) DEFAULT NULL,
    last_node varchar(64) DEFAULT NULL,
    host varchar(191) DEFAULT NULL,
    started_at datetime DEFAULT NULL,
    ended_at datetime DEFAULT NULL,
    heartbeat_at datetime DEFAULT NULL,
    duration_ms bigint(20) unsigned DEFAULT NULL,
    worker_ms bigint(20) unsigned DEFAULT NULL,
    event_count int(10) unsigned NOT NULL DEFAULT 0,
    revisions int(10) unsigned NOT NULL DEFAULT 0,
    cost_usd decimal(12,4) DEFAULT NULL,
    input_tokens bigint(20) unsigned DEFAULT NULL,
    output_tokens bigint(20) unsigned DEFAULT NULL,
    error text,
    graph longtext,
    metrics longtext,
    created_at datetime NOT NULL,
    updated_at datetime NOT NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY run_id (run_id),
    KEY status_updated (status, updated_at),
    KEY updated_at (updated_at),
    KEY feature (feature)
) {$charset};",

            "CREATE TABLE " . self::events() . " (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    run_id varchar(128) NOT NULL,
    seq int(10) unsigned NOT NULL,
    node varchar(64) DEFAULT NULL,
    status varchar(32) DEFAULT NULL,
    duration_ms bigint(20) unsigned DEFAULT NULL,
    occurred_at datetime DEFAULT NULL,
    summary longtext,
    error text,
    PRIMARY KEY  (id),
    UNIQUE KEY run_seq (run_id, seq),
    KEY run_id (run_id)
) {$charset};",

            "CREATE TABLE " . self::documents() . " (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    uid char(32) NOT NULL,
    run_id varchar(128) NOT NULL,
    slug varchar(191) NOT NULL,
    title varchar(191) NOT NULL,
    kind varchar(32) NOT NULL DEFAULT 'primary',
    sort_order int(10) unsigned NOT NULL DEFAULT 0,
    body longtext,
    PRIMARY KEY  (id),
    UNIQUE KEY uid (uid),
    KEY run_kind (run_id, kind, sort_order)
) {$charset};",

            "CREATE TABLE " . self::artifacts() . " (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    uid char(32) NOT NULL,
    run_id varchar(128) NOT NULL,
    rel_path varchar(191) NOT NULL,
    file_name varchar(191) NOT NULL,
    mime varchar(100) NOT NULL,
    bytes bigint(20) unsigned NOT NULL DEFAULT 0,
    width int(10) unsigned DEFAULT NULL,
    height int(10) unsigned DEFAULT NULL,
    animated tinyint(1) NOT NULL DEFAULT 0,
    checksum char(40) NOT NULL DEFAULT '',
    thumb_name varchar(191) DEFAULT NULL,
    sort_order int(10) unsigned NOT NULL DEFAULT 0,
    created_at datetime NOT NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY uid (uid),
    KEY run_order (run_id, sort_order)
) {$charset};",
        ];
    }
}
