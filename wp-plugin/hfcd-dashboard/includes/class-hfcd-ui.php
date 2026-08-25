<?php
/**
 * Small pieces of markup shared by the list and run templates.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Ui
{
    /** Status name => palette class. */
    private const STATUS_TONE = [
        'running' => 'info',
        'completed' => 'ok',
        'failed' => 'err',
        'abandoned' => 'err',
        'cancelled' => 'warn',
        'interrupted' => 'warn',
        'unknown' => 'mute',
    ];

    /**
     * Verdicts the team-leader and check reports actually emit, measured over
     * the published run history: pass, fixable, blocked, unknown, design_failure.
     * Anything unrecognised stays neutral rather than claiming a result.
     */
    private const CLASSIFICATION_TONE = [
        'pass' => 'ok',
        'fixable' => 'warn',
        'escalate' => 'warn',
        'escalated' => 'warn',
        'fail' => 'err',
        'blocked' => 'err',
        'design_failure' => 'err',
        'unknown' => 'mute',
    ];

    public static function status_badge(array $run): string
    {
        $status = HFCD_Format::display_status($run);
        $tone = self::STATUS_TONE[$status] ?? 'mute';
        $pulse = $status === 'running' ? '<span class="pulse" aria-hidden="true"></span>' : '';

        return '<span class="badge tone-' . esc_attr($tone) . '">' . $pulse . esc_html($status) . '</span>';
    }

    public static function classification_badge(?string $classification): string
    {
        if ($classification === null || $classification === '') {
            return '<span class="dim">' . esc_html(HFCD_Format::DASH) . '</span>';
        }

        $tone = self::CLASSIFICATION_TONE[strtolower($classification)] ?? 'mute';

        return '<span class="badge badge-soft tone-' . esc_attr($tone) . '">' . esc_html($classification) . '</span>';
    }

    /** Heartbeat warning for a run that claims to still be going. */
    public static function health_notice(array $run): string
    {
        $health = HFCD_Format::health($run);
        if ($health === null || $health['state'] === 'live') {
            return '';
        }

        $tone = $health['state'] === 'abandoned' ? 'err' : 'warn';

        return '<p class="notice tone-' . esc_attr($tone) . '">' . esc_html($health['label']) . '</p>';
    }

    public static function time_cell(?string $utc_datetime): string
    {
        if ($utc_datetime === null || $utc_datetime === '') {
            return '<span class="dim">' . esc_html(HFCD_Format::DASH) . '</span>';
        }

        return '<time datetime="' . esc_attr(HFCD_Format::iso($utc_datetime)) . '" title="'
            . esc_attr(HFCD_Format::absolute($utc_datetime)) . '">'
            . esc_html(HFCD_Format::relative($utc_datetime)) . '</time>';
    }

    /**
     * @param array<int,array> $stages
     */
    public static function stages(array $stages, bool $compact = false): string
    {
        if ($stages === []) {
            return '';
        }

        $html = '<ol class="stages' . ($compact ? ' stages-compact' : '') . '">';
        foreach ($stages as $stage) {
            $meta = $compact ? '' : self::stage_meta($stage);
            $html .= '<li class="stage is-' . esc_attr($stage['state']) . '" title="'
                . esc_attr(self::stage_title($stage)) . '">'
                . '<span class="stage-dot" aria-hidden="true"></span>'
                . '<span class="stage-label">' . esc_html($stage['label']) . '</span>'
                . $meta
                . '</li>';
        }

        return $html . '</ol>';
    }

    public static function progress_bar(int $percent, string $label): string
    {
        $percent = max(0, min(100, $percent));

        return '<div class="progress" role="progressbar" aria-valuenow="' . esc_attr((string) $percent)
            . '" aria-valuemin="0" aria-valuemax="100" aria-label="' . esc_attr($label) . '">'
            . '<div class="progress-fill" style="width:' . esc_attr((string) $percent) . '%"></div></div>';
    }

    /** A labelled figure for the metric grids. */
    public static function metric(string $label, string $value, string $modifier = ''): string
    {
        return '<div class="metric' . ($modifier !== '' ? ' ' . esc_attr($modifier) : '') . '">'
            . '<dt>' . esc_html($label) . '</dt>'
            . '<dd>' . $value . '</dd></div>';
    }

    private static function stage_meta(array $stage): string
    {
        $parts = [];
        if ($stage['duration_ms'] !== null) {
            $parts[] = HFCD_Format::duration($stage['duration_ms']);
        }
        if ($stage['passes'] > 1) {
            $parts[] = $stage['passes'] . '&times;';
        }

        return $parts === [] ? '' : '<span class="stage-meta">' . implode(' · ', $parts) . '</span>';
    }

    private static function stage_title(array $stage): string
    {
        $state = [
            HFCD_Workflow::DONE => 'completed',
            HFCD_Workflow::ACTIVE => 'in progress',
            HFCD_Workflow::FAILED => 'failed',
            HFCD_Workflow::PENDING => 'not started yet',
            HFCD_Workflow::SKIPPED => 'never reached',
        ][$stage['state']] ?? $stage['state'];

        $title = $stage['label'] . ' — ' . $state;
        if ($stage['passes'] > 1) {
            $title .= ', ' . $stage['passes'] . ' passes';
        }

        return $title;
    }
}
