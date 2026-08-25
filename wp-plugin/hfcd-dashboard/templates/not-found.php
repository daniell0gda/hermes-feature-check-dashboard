<?php
/**
 * @var string $run_id
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = 'Run not found';
require HFCD_DIR . 'templates/partials/head.php';
?>
<header class="page-head">
    <p class="eyebrow">Feature check</p>
    <h1>Run not found</h1>
    <p class="lede">
        No run is stored under <code><?php echo esc_html($run_id); ?></code>. It may not have been
        published yet, or it may have been deleted.
    </p>
    <p><a class="button" href="<?php echo esc_url(HFCD_View::base_url()); ?>">Back to all runs</a></p>
</header>
<?php require HFCD_DIR . 'templates/partials/foot.php'; ?>
