<?php
/**
 * Presentation helpers shared by the templates and the REST payloads.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Format
{
    /** A run reported as running but silent for this long is probably stuck. */
    public const STALE_AFTER = 120;
    public const ABANDONED_AFTER = 600;

    public const DASH = '—';

    public static function duration(?int $milliseconds): string
    {
        if ($milliseconds === null) {
            return self::DASH;
        }

        $seconds = (int) round($milliseconds / 1000);
        if ($seconds < 60) {
            return $seconds . 's';
        }
        if ($seconds < 3600) {
            return intdiv($seconds, 60) . 'm ' . str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT) . 's';
        }

        return intdiv($seconds, 3600) . 'h ' . str_pad((string) intdiv($seconds % 3600, 60), 2, '0', STR_PAD_LEFT) . 'm';
    }

    /** Wall-clock time a still-running run has been going. */
    public static function elapsed_since(?string $utc_datetime): ?int
    {
        $timestamp = self::timestamp($utc_datetime);

        return $timestamp === null ? null : max(0, time() - $timestamp) * 1000;
    }

    /**
     * How long a run took, or how long it has been going if it is still live.
     */
    public static function run_duration_ms(array $run): ?int
    {
        if (($run['duration_ms'] ?? null) !== null) {
            return (int) $run['duration_ms'];
        }
        if (($run['status'] ?? '') === 'running') {
            return self::elapsed_since($run['started_at'] ?? null);
        }

        return null;
    }

    public static function relative(?string $utc_datetime): string
    {
        $timestamp = self::timestamp($utc_datetime);
        if ($timestamp === null) {
            return self::DASH;
        }

        // Covers clock skew from the worker too: a future stamp reads "just now".
        if (time() - $timestamp < 45) {
            return 'just now';
        }

        return human_time_diff($timestamp) . ' ago';
    }

    /** Absolute time in the site timezone, for tooltips. */
    public static function absolute(?string $utc_datetime): string
    {
        $timestamp = self::timestamp($utc_datetime);

        return $timestamp === null ? self::DASH : wp_date('Y-m-d H:i:s T', $timestamp);
    }

    public static function iso(?string $utc_datetime): string
    {
        $timestamp = self::timestamp($utc_datetime);

        return $timestamp === null ? '' : gmdate('c', $timestamp);
    }

    /**
     * Liveness of a running run, derived at read time so no cron is needed.
     *
     * @return array{state:string,label:string,age:int}|null
     */
    public static function health(array $run): ?array
    {
        if (($run['status'] ?? '') !== 'running') {
            return null;
        }

        $timestamp = self::timestamp($run['heartbeat_at'] ?? null);
        if ($timestamp === null) {
            return null;
        }

        $age = max(0, time() - $timestamp);
        if ($age > self::ABANDONED_AFTER) {
            return [
                'state' => 'abandoned',
                'label' => 'Abandoned — no heartbeat for ' . human_time_diff($timestamp) . '.',
                'age' => $age,
            ];
        }
        if ($age > self::STALE_AFTER) {
            return [
                'state' => 'stale',
                'label' => 'Possibly stale — last heartbeat ' . human_time_diff($timestamp) . ' ago.',
                'age' => $age,
            ];
        }

        return ['state' => 'live', 'label' => 'Live — heartbeat is current.', 'age' => $age];
    }

    /**
     * The status to show a reader: a run whose heartbeat expired is reported as
     * abandoned rather than as still running.
     */
    public static function display_status(array $run): string
    {
        $health = self::health($run);
        if ($health !== null && $health['state'] === 'abandoned') {
            return 'abandoned';
        }

        return (string) ($run['status'] ?? 'unknown');
    }

    public static function label(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return self::DASH;
        }

        return ucfirst(str_replace(['-', '_'], ' ', $value));
    }

    public static function count(?int $value): string
    {
        return $value === null ? self::DASH : number_format_i18n($value);
    }

    public static function money(?float $value): string
    {
        return $value === null ? self::DASH : '$' . number_format_i18n($value, 2);
    }

    /** Large token counts read better abbreviated. */
    public static function tokens(?float $value): string
    {
        if ($value === null) {
            return self::DASH;
        }
        if ($value >= 1000000) {
            return number_format_i18n($value / 1000000, 1) . 'M';
        }
        if ($value >= 1000) {
            return number_format_i18n($value / 1000, 1) . 'k';
        }

        return number_format_i18n($value);
    }

    /** Collapse a multi-line worker summary to a single readable line. */
    public static function one_line(?string $text, int $length = 150): string
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text) ?? '');
        if ($text === '') {
            return '';
        }

        return mb_strlen($text) > $length ? mb_substr($text, 0, $length - 1) . '…' : $text;
    }

    private static function timestamp(?string $utc_datetime): ?int
    {
        if (!is_string($utc_datetime) || trim($utc_datetime) === '' || str_starts_with($utc_datetime, '0000')) {
            return null;
        }

        $timestamp = strtotime($utc_datetime . ' UTC');

        return $timestamp ?: null;
    }
}
