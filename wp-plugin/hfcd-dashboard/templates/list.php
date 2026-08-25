<?php
/**
 * All-runs view.
 *
 * @var array $filters
 * @var array $result
 * @var array $summary
 * @var array<int,array> $active
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = 'Run dashboard';
require HFCD_DIR . 'templates/partials/head.php';

$status_filters = [
    '' => 'All',
    'running' => 'Running',
    'completed' => 'Completed',
    'failed' => 'Failed',
    'other' => 'Other',
];
?>
<header class="page-head">
    <p class="eyebrow">Hermes · Feature check</p>
    <h1>Run dashboard</h1>
    <p class="lede">
        Every feature-check run the worker has published, newest first. Runs update in place
        while they are live.
    </p>
</header>

<dl class="metrics" data-live="summary">
    <?php
    echo HFCD_Ui::metric('Runs', HFCD_Format::count($summary['total'])); // phpcs:ignore WordPress.Security.EscapeOutput
    echo HFCD_Ui::metric('Running now', HFCD_Format::count($summary['running']), $summary['running'] > 0 ? 'is-live' : ''); // phpcs:ignore WordPress.Security.EscapeOutput
    echo HFCD_Ui::metric('Passed gate', HFCD_Format::count($summary['passed'])); // phpcs:ignore WordPress.Security.EscapeOutput
    echo HFCD_Ui::metric('Failed', HFCD_Format::count($summary['failed'])); // phpcs:ignore WordPress.Security.EscapeOutput
    echo HFCD_Ui::metric('Avg duration', HFCD_Format::duration($summary['avg_duration_ms'])); // phpcs:ignore WordPress.Security.EscapeOutput
    echo HFCD_Ui::metric('Worker calls', HFCD_Format::count($summary['worker_calls'])); // phpcs:ignore WordPress.Security.EscapeOutput
    ?>
</dl>

<?php if ($summary['cost_usd'] !== null || $summary['input_tokens'] !== null) : ?>
    <dl class="metrics metrics-secondary">
        <?php
        echo HFCD_Ui::metric('Total cost', HFCD_Format::money($summary['cost_usd'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Input tokens', HFCD_Format::tokens($summary['input_tokens'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Output tokens', HFCD_Format::tokens($summary['output_tokens'])); // phpcs:ignore WordPress.Security.EscapeOutput
        echo HFCD_Ui::metric('Revisions', HFCD_Format::count($summary['revisions'])); // phpcs:ignore WordPress.Security.EscapeOutput
        ?>
    </dl>
<?php endif; ?>

<?php if ($active !== []) : ?>
    <section class="panel" data-live="active">
        <div class="panel-head">
            <h2>Running now</h2>
            <span class="pill"><?php echo esc_html((string) count($active)); ?></span>
        </div>
        <div class="active-grid">
            <?php foreach ($active as $run) :
                $stages = HFCD_Workflow::stages($run, HFCD_Event_Store::for_run((string) $run['run_id']));
                ?>
                <article class="active-card">
                    <div class="active-card-head">
                        <h3>
                            <a href="<?php echo esc_url(HFCD_View::run_url((string) $run['run_id'])); ?>">
                                <?php echo esc_html((string) ($run['feature'] ?: $run['run_id'])); ?>
                            </a>
                        </h3>
                        <?php echo HFCD_Ui::status_badge($run); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </div>
                    <p class="run-id"><?php echo esc_html((string) $run['run_id']); ?></p>
                    <?php
                    echo HFCD_Ui::progress_bar(HFCD_Workflow::progress($stages), 'Run progress'); // phpcs:ignore WordPress.Security.EscapeOutput
                    echo HFCD_Ui::stages($stages); // phpcs:ignore WordPress.Security.EscapeOutput
                    echo HFCD_Ui::health_notice($run); // phpcs:ignore WordPress.Security.EscapeOutput
                    ?>
                    <dl class="inline-facts">
                        <div><dt>Phase</dt><dd><?php echo esc_html(HFCD_Format::label($run['phase'])); ?></dd></div>
                        <div><dt>Elapsed</dt><dd><?php echo esc_html(HFCD_Format::duration(HFCD_Format::run_duration_ms($run))); ?></dd></div>
                        <div><dt>Heartbeat</dt><dd><?php echo HFCD_Ui::time_cell($run['heartbeat_at']); // phpcs:ignore WordPress.Security.EscapeOutput ?></dd></div>
                    </dl>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-head">
        <h2>All runs</h2>
        <form class="search" method="get" action="<?php echo esc_url(HFCD_View::base_url()); ?>" role="search">
            <?php if ($filters['status'] !== '') : ?>
                <input type="hidden" name="status" value="<?php echo esc_attr($filters['status']); ?>">
            <?php endif; ?>
            <input type="search" name="q" value="<?php echo esc_attr($filters['q']); ?>"
                   placeholder="Filter by run id or feature" aria-label="Filter runs">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="chips" role="tablist" aria-label="Filter by status">
        <?php foreach ($status_filters as $value => $label) :
            $is_current = $filters['status'] === $value;
            ?>
            <a class="chip<?php echo $is_current ? ' is-current' : ''; ?>"
               <?php echo $is_current ? 'aria-current="page"' : ''; ?>
               href="<?php echo esc_url(HFCD_View::list_url(['status' => $value, 'q' => $filters['q']])); ?>">
                <?php echo esc_html($label); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div data-live="runs">
        <?php if ($result['rows'] === []) : ?>
            <p class="empty">
                No runs match this view.
                <?php if ($filters['q'] !== '' || $filters['status'] !== '') : ?>
                    <a href="<?php echo esc_url(HFCD_View::base_url()); ?>">Clear filters</a>
                <?php endif; ?>
            </p>
        <?php else : ?>
            <div class="table-scroll">
                <table class="runs">
                    <thead>
                        <tr>
                            <th scope="col">Run</th>
                            <th scope="col">Status</th>
                            <th scope="col">Verdict</th>
                            <th scope="col">Phase</th>
                            <th scope="col" class="num">Duration</th>
                            <th scope="col" class="num">Events</th>
                            <th scope="col">Started</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['rows'] as $run) : ?>
                            <tr>
                                <td class="cell-run">
                                    <a class="run-link" href="<?php echo esc_url(HFCD_View::run_url((string) $run['run_id'])); ?>">
                                        <?php echo esc_html((string) ($run['feature'] ?: $run['run_id'])); ?>
                                    </a>
                                    <span class="run-id"><?php echo esc_html((string) $run['run_id']); ?></span>
                                    <?php if ((int) $run['revisions'] > 0) : ?>
                                        <span class="tag" title="Revision passes through the coding stage">
                                            <?php echo esc_html((string) $run['revisions']); ?> rev
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo HFCD_Ui::status_badge($run); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
                                <td><?php echo HFCD_Ui::classification_badge($run['classification']); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
                                <td><?php echo esc_html(HFCD_Format::label($run['last_node'] ?: $run['phase'])); ?></td>
                                <td class="num"><?php echo esc_html(HFCD_Format::duration(HFCD_Format::run_duration_ms($run))); ?></td>
                                <td class="num"><?php echo esc_html((string) $run['event_count']); ?></td>
                                <td><?php echo HFCD_Ui::time_cell($run['started_at'] ?: $run['updated_at']); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($result['pages'] > 1) : ?>
                <nav class="pager" aria-label="Pagination">
                    <?php
                    $page_args = ['status' => $filters['status'], 'q' => $filters['q']];
                    if ($result['page'] > 1) :
                        ?>
                        <a href="<?php echo esc_url(HFCD_View::list_url($page_args + ['pg' => $result['page'] - 1])); ?>">Newer</a>
                    <?php else : ?>
                        <span class="disabled">Newer</span>
                    <?php endif; ?>
                    <span class="pager-state">
                        Page <?php echo esc_html((string) $result['page']); ?>
                        of <?php echo esc_html((string) $result['pages']); ?>
                        · <?php echo esc_html(HFCD_Format::count($result['total'])); ?> runs
                    </span>
                    <?php if ($result['page'] < $result['pages']) : ?>
                        <a href="<?php echo esc_url(HFCD_View::list_url($page_args + ['pg' => $result['page'] + 1])); ?>">Older</a>
                    <?php else : ?>
                        <span class="disabled">Older</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require HFCD_DIR . 'templates/partials/foot.php'; ?>
