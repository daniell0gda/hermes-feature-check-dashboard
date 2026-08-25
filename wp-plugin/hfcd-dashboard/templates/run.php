<?php
/**
 * Single run view. Everything here comes out of the dashboard database.
 *
 * @var array $run
 * @var array<int,array> $events
 * @var array<int,array> $stages
 * @var array<int,array> $documents
 * @var array<int,array> $media
 */

if (!defined('ABSPATH')) {
    exit;
}

$run_id = (string) $run['run_id'];
$feature = (string) ($run['feature'] ?: $run_id);
$page_title = $feature;

/*
 * Resolve markdown image references against this run's own artifacts, from a
 * map built once rather than a query per image.
 */
$media_urls = [];
foreach ($media as $artifact) {
    $url = HFCD_Media_Store::url($artifact);
    $media_urls[(string) $artifact['rel_path']] = $url;
    $media_urls[(string) $artifact['file_name']] = $url;
}
$resolve_media = static function (string $reference) use ($media_urls): ?string {
    $path = HFCD_Media_Store::sanitize_path($reference);

    return $media_urls[$path] ?? $media_urls[basename($path)] ?? null;
};

$documents_by_kind = [
    HFCD_Document_Store::KIND_PRIMARY => [],
    HFCD_Document_Store::KIND_CLUSTER => [],
    HFCD_Document_Store::KIND_CODER => [],
];
foreach ($documents as $document) {
    $documents_by_kind[(string) $document['kind']][] = $document;
}

$run_metrics = json_decode((string) $run['metrics'], true);
$invocations = is_array($run_metrics) && is_array($run_metrics['invocations'] ?? null)
    ? $run_metrics['invocations']
    : [];

$sections = ['overview' => 'Overview', 'workflow' => 'Workflow', 'timeline' => 'Timeline'];
if ($invocations !== []) {
    $sections['usage'] = 'Usage';
}
if ($media !== []) {
    $sections['screenshots'] = 'Screenshots';
}
if ($documents !== []) {
    $sections['reports'] = 'Reports';
}

require HFCD_DIR . 'templates/partials/head.php';
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?php echo esc_url(HFCD_View::base_url()); ?>">All runs</a>
    <span aria-hidden="true">/</span>
    <span><?php echo esc_html($feature); ?></span>
</nav>

<header class="page-head run-head" data-live="head">
    <div class="run-title">
        <div>
            <p class="eyebrow">Feature check run</p>
            <h1><?php echo esc_html($feature); ?></h1>
            <p class="run-id-row">
                <code><?php echo esc_html($run_id); ?></code>
                <button type="button" class="copy" data-copy="<?php echo esc_attr($run_id); ?>">Copy id</button>
            </p>
        </div>
        <div class="run-badges">
            <?php
            echo HFCD_Ui::status_badge($run); // phpcs:ignore WordPress.Security.EscapeOutput
            echo HFCD_Ui::classification_badge($run['classification']); // phpcs:ignore WordPress.Security.EscapeOutput
            ?>
        </div>
    </div>
    <?php echo HFCD_Ui::health_notice($run); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    <?php if (!empty($run['error'])) : ?>
        <div class="notice tone-err">
            <strong>Run error</strong>
            <pre><?php echo esc_html((string) $run['error']); ?></pre>
        </div>
    <?php endif; ?>
</header>

<nav class="subnav" aria-label="Sections">
    <?php foreach ($sections as $anchor => $label) : ?>
        <a href="#<?php echo esc_attr($anchor); ?>"><?php echo esc_html($label); ?></a>
    <?php endforeach; ?>
</nav>

<section id="overview" class="panel">
    <div class="panel-head"><h2>Overview</h2></div>
    <dl class="metrics metrics-facts" data-live="facts">
        <?php
        echo HFCD_Ui::metric('Phase', esc_html(HFCD_Format::label($run['active_node'] ?: $run['phase']))); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Duration', esc_html(HFCD_Format::duration(HFCD_Format::run_duration_ms($run)))); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Worker time', esc_html(HFCD_Format::duration($run['worker_ms'] === null ? null : (int) $run['worker_ms']))); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Revisions', esc_html((string) $run['revisions'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Worker calls', esc_html((string) $run['event_count'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Started', HFCD_Ui::time_cell($run['started_at'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Ended', HFCD_Ui::time_cell($run['ended_at'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Heartbeat', HFCD_Ui::time_cell($run['heartbeat_at'])); // phpcs:ignore WordPress.Security.EscapeOutput
        ?>
    </dl>
    <?php if ($run['cost_usd'] !== null || $run['input_tokens'] !== null || $run['output_tokens'] !== null) : ?>
        <dl class="metrics metrics-secondary">
            <?php
            echo HFCD_Ui::metric('Cost', esc_html(HFCD_Format::money($run['cost_usd'] === null ? null : (float) $run['cost_usd']))); // phpcs:ignore WordPress.Security.EscapeOutput
            echo HFCD_Ui::metric('Input tokens', esc_html(HFCD_Format::tokens($run['input_tokens'] === null ? null : (float) $run['input_tokens']))); // phpcs:ignore WordPress.Security.EscapeOutput
            echo HFCD_Ui::metric('Output tokens', esc_html(HFCD_Format::tokens($run['output_tokens'] === null ? null : (float) $run['output_tokens']))); // phpcs:ignore WordPress.Security.EscapeOutput
            ?>
        </dl>
    <?php else : ?>
        <p class="hint">
            The worker did not report token usage for this run.
        </p>
    <?php endif; ?>
    <?php if ($invocations !== [] && $run['cost_usd'] === null) : ?>
        <p class="hint">
            Cost is unavailable: every worker call reported <code>cost_status: unknown</code>, so
            no price was resolved for the model used.
        </p>
    <?php endif; ?>
    <?php if (!empty($run['host'])) : ?>
        <p class="hint">Executed on host <code><?php echo esc_html((string) $run['host']); ?></code>.</p>
    <?php endif; ?>
</section>

<section id="workflow" class="panel" data-live="workflow">
    <div class="panel-head">
        <h2>Workflow</h2>
        <span class="panel-meta"><?php echo esc_html((string) HFCD_Workflow::progress($stages)); ?>% settled</span>
    </div>
    <?php
    echo HFCD_Ui::progress_bar(HFCD_Workflow::progress($stages), 'Run progress'); // phpcs:ignore WordPress.Security.EscapeOutput
    echo HFCD_Ui::stages($stages); // phpcs:ignore WordPress.Security.EscapeOutput
    ?>
    <ul class="legend">
        <li class="is-done">Done</li>
        <li class="is-active">Active</li>
        <li class="is-failed">Failed</li>
        <li class="is-pending">Pending</li>
        <li class="is-skipped">Not reached</li>
    </ul>
</section>

<section id="timeline" class="panel" data-live="timeline">
    <div class="panel-head">
        <h2>Timeline</h2>
        <span class="panel-meta"><?php echo esc_html((string) count($events)); ?> worker calls</span>
    </div>
    <?php if ($events === []) : ?>
        <p class="empty">No worker calls recorded yet.</p>
    <?php else : ?>
        <div class="table-scroll">
            <table class="timeline">
                <thead>
                    <tr>
                        <th scope="col" class="num">#</th>
                        <th scope="col">Stage</th>
                        <th scope="col">Result</th>
                        <th scope="col" class="num">Duration</th>
                        <th scope="col">When</th>
                        <th scope="col">Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event) :
                        $failed = in_array(strtolower((string) $event['status']), ['failed', 'error'], true);
                        ?>
                        <tr>
                            <td class="num dim"><?php echo esc_html((string) $event['seq']); ?></td>
                            <td><strong><?php echo esc_html(HFCD_Format::label($event['node'])); ?></strong></td>
                            <td>
                                <span class="badge badge-soft tone-<?php echo $failed ? 'err' : 'ok'; ?>">
                                    <?php echo esc_html((string) ($event['status'] ?: 'unknown')); ?>
                                </span>
                            </td>
                            <td class="num"><?php echo esc_html(HFCD_Format::duration($event['duration_ms'] === null ? null : (int) $event['duration_ms'])); ?></td>
                            <td><?php echo HFCD_Ui::time_cell($event['occurred_at']); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
                            <td class="cell-summary">
                                <?php if (trim((string) $event['summary']) === '') : ?>
                                    <span class="dim"><?php echo esc_html(HFCD_Format::DASH); ?></span>
                                <?php else : ?>
                                    <details>
                                        <summary><?php echo esc_html(HFCD_Format::one_line($event['summary'])); ?></summary>
                                        <div class="markdown">
                                            <?php echo HFCD_Markdown::to_html((string) $event['summary'], $resolve_media); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                        </div>
                                    </details>
                                <?php endif; ?>
                                <?php if (!empty($event['error'])) : ?>
                                    <p class="inline-error"><?php echo esc_html((string) $event['error']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php if ($invocations !== []) : ?>
    <section id="usage" class="panel">
        <div class="panel-head">
            <h2>Usage</h2>
            <span class="panel-meta"><?php echo esc_html((string) count($invocations)); ?> worker calls</span>
        </div>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Task</th>
                        <th scope="col">Role</th>
                        <th scope="col">Model</th>
                        <th scope="col" class="num">Input</th>
                        <th scope="col" class="num">Output</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invocations as $invocation) :
                        if (!is_array($invocation)) {
                            continue;
                        }
                        ?>
                        <tr>
                            <td><?php echo esc_html((string) ($invocation['task_id'] ?? HFCD_Format::DASH)); ?></td>
                            <td><?php echo esc_html(HFCD_Format::label($invocation['role'] ?? null)); ?></td>
                            <td class="dim"><?php echo esc_html((string) ($invocation['model'] ?? HFCD_Format::DASH)); ?></td>
                            <td class="num"><?php echo esc_html(HFCD_Format::count(isset($invocation['input_tokens']) ? (int) $invocation['input_tokens'] : null)); ?></td>
                            <td class="num"><?php echo esc_html(HFCD_Format::count(isset($invocation['output_tokens']) ? (int) $invocation['output_tokens'] : null)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row" colspan="3">Total</th>
                        <td class="num"><?php echo esc_html(HFCD_Format::count($run['input_tokens'] === null ? null : (int) $run['input_tokens'])); ?></td>
                        <td class="num"><?php echo esc_html(HFCD_Format::count($run['output_tokens'] === null ? null : (int) $run['output_tokens'])); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php if ($media !== []) : ?>
    <section id="screenshots" class="panel">
        <div class="panel-head">
            <h2>Screenshots</h2>
            <span class="panel-meta"><?php echo esc_html((string) count($media)); ?> files</span>
        </div>
        <div class="gallery">
            <?php foreach ($media as $artifact) : ?>
                <figure class="shot">
                    <a href="<?php echo esc_url(HFCD_Media_Store::url($artifact)); ?>"
                       data-lightbox
                       data-caption="<?php echo esc_attr((string) $artifact['rel_path']); ?>">
                        <img src="<?php echo esc_url(HFCD_Media_Store::thumb_url($artifact)); ?>"
                             alt="<?php echo esc_attr((string) $artifact['file_name']); ?>"
                             <?php if ($artifact['width'] && $artifact['height']) : ?>
                                 width="<?php echo esc_attr((string) $artifact['width']); ?>"
                                 height="<?php echo esc_attr((string) $artifact['height']); ?>"
                             <?php endif; ?>
                             loading="lazy" decoding="async">
                        <?php if ((int) $artifact['animated'] === 1) : ?>
                            <span class="shot-flag">GIF</span>
                        <?php endif; ?>
                    </a>
                    <figcaption>
                        <span class="shot-name"><?php echo esc_html((string) $artifact['file_name']); ?></span>
                        <span class="shot-meta">
                            <?php if ($artifact['width'] && $artifact['height']) : ?>
                                <?php echo esc_html($artifact['width'] . '×' . $artifact['height']); ?> ·
                            <?php endif; ?>
                            <?php echo esc_html(size_format((int) $artifact['bytes'])); ?>
                        </span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($documents !== []) : ?>
    <section id="reports" class="panel">
        <div class="panel-head"><h2>Reports</h2></div>

        <?php if ($documents_by_kind[HFCD_Document_Store::KIND_PRIMARY] !== []) : ?>
            <div class="tabs" data-tabs>
                <div class="tablist" role="tablist">
                    <?php foreach ($documents_by_kind[HFCD_Document_Store::KIND_PRIMARY] as $position => $document) : ?>
                        <button type="button" role="tab" class="tab<?php echo $position === 0 ? ' is-current' : ''; ?>"
                                aria-selected="<?php echo $position === 0 ? 'true' : 'false'; ?>"
                                aria-controls="doc-<?php echo esc_attr(md5((string) $document['slug'])); ?>">
                            <?php echo esc_html((string) $document['title']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php foreach ($documents_by_kind[HFCD_Document_Store::KIND_PRIMARY] as $position => $document) : ?>
                    <div class="tabpanel" role="tabpanel"
                         id="doc-<?php echo esc_attr(md5((string) $document['slug'])); ?>"
                         <?php echo $position === 0 ? '' : 'hidden'; ?>>
                        <div class="markdown">
                            <?php echo HFCD_Markdown::to_html((string) $document['body'], $resolve_media); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        $groups = [
            HFCD_Document_Store::KIND_CLUSTER => 'Work clusters',
            HFCD_Document_Store::KIND_CODER => 'Coder reports',
        ];
        foreach ($groups as $kind => $group_title) :
            if ($documents_by_kind[$kind] === []) {
                continue;
            }
            ?>
            <div class="doc-group">
                <h3><?php echo esc_html($group_title); ?>
                    <span class="pill"><?php echo esc_html((string) count($documents_by_kind[$kind])); ?></span>
                </h3>
                <?php foreach ($documents_by_kind[$kind] as $document) : ?>
                    <details class="doc">
                        <summary><?php echo esc_html((string) $document['title']); ?></summary>
                        <div class="markdown">
                            <?php echo HFCD_Markdown::to_html((string) $document['body'], $resolve_media); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<p class="hint hint-end">
    Raw data:
    <a href="<?php echo esc_url(rest_url(HFCD_Rest::NAMESPACE . '/runs/' . $run_id)); ?>">run JSON</a> ·
    <a href="<?php echo esc_url(rest_url(HFCD_Rest::NAMESPACE . '/runs/' . $run_id . '/media')); ?>">media manifest</a>
</p>

<?php require HFCD_DIR . 'templates/partials/foot.php'; ?>
