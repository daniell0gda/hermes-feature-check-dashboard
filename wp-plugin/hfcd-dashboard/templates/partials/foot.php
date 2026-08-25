<?php
/**
 * Closes the document opened by head.php.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
</main>
<footer class="footer">
    <span>Rendered from the dashboard database · <?php echo esc_html(wp_date('Y-m-d H:i T')); ?></span>
    <span class="footer-links">
        <a href="<?php echo esc_url(rest_url(HFCD_Rest::NAMESPACE . '/runs')); ?>">Runs API</a>
        <a href="<?php echo esc_url(rest_url(HFCD_Rest::NAMESPACE . '/summary')); ?>">Summary API</a>
    </span>
</footer>
<script src="<?php echo esc_url(HFCD_View::asset('dashboard.js')); ?>" defer></script>
</body>
</html>
