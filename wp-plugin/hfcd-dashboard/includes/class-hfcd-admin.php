<?php
/**
 * Tools -> HFCD Dashboard: the worker token and the public URL slug.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Admin
{
    private const PAGE = 'hfcd-dashboard';
    private const ACTION = 'hfcd_save_settings';

    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'register_page']);
        add_action('admin_post_' . self::ACTION, [self::class, 'handle_save']);
    }

    public static function register_page(): void
    {
        add_management_page(
            'HFCD Dashboard',
            'HFCD Dashboard',
            'manage_options',
            self::PAGE,
            [self::class, 'render_page']
        );
    }

    public static function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions.');
        }

        $summary = HFCD_Run_Store::summary();
        $upload = wp_get_upload_dir();
        ?>
        <div class="wrap">
            <h1>HFCD Dashboard</h1>

            <h2 class="title">Status</h2>
            <table class="widefat striped" style="max-width:760px">
                <tbody>
                    <tr>
                        <td style="width:220px"><strong>Public dashboard</strong></td>
                        <td><a href="<?php echo esc_url(HFCD_View::base_url()); ?>" target="_blank" rel="noopener"><?php echo esc_html(HFCD_View::base_url()); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong>Ingest endpoint</strong></td>
                        <td><code><?php echo esc_html(rest_url(HFCD_Rest::NAMESPACE)); ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Runs stored</strong></td>
                        <td><?php echo esc_html((string) $summary['total']); ?> (<?php echo esc_html((string) $summary['running']); ?> running)</td>
                    </tr>
                    <tr>
                        <td><strong>Media directory</strong></td>
                        <td>
                            <code><?php echo esc_html($upload['basedir'] . '/hfcd'); ?></code>
                            <?php if (!wp_is_writable($upload['basedir'])) : ?>
                                <span style="color:#b32d2e"> — not writable</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Max upload body</strong></td>
                        <td><?php echo esc_html(size_format(wp_max_upload_size())); ?> (PHP <code>post_max_size</code> / <code>upload_max_filesize</code>)</td>
                    </tr>
                </tbody>
            </table>

            <h2 class="title">Settings</h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field(self::ACTION); ?>
                <input type="hidden" name="action" value="<?php echo esc_attr(self::ACTION); ?>">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="hfcd-slug">URL slug</label></th>
                        <td>
                            <input name="hfcd_slug" id="hfcd-slug" type="text" class="regular-text"
                                   value="<?php echo esc_attr(HFCD_View::slug()); ?>">
                            <p class="description">The dashboard lives at <code>/&lt;slug&gt;/</code>. Must not collide with an existing page slug.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hfcd-token">Worker token</label></th>
                        <td>
                            <input id="hfcd-token" type="text" class="large-text code" readonly
                                   value="<?php echo esc_attr(HFCD_Auth::token()); ?>"
                                   onclick="this.select()">
                            <p class="description">
                                Sent by the publisher as the <code>X-HFCD-Token</code> header. Rotating it
                                immediately locks out any worker still using the old value.
                            </p>
                            <label>
                                <input type="checkbox" name="hfcd_rotate" value="1">
                                Rotate the token when saving
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save changes'); ?>
            </form>
        </div>
        <?php
    }

    public static function handle_save(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions.');
        }
        check_admin_referer(self::ACTION);

        $slug = sanitize_title((string) ($_POST['hfcd_slug'] ?? ''));
        if ($slug !== '' && $slug !== HFCD_View::slug()) {
            update_option(HFCD_View::OPTION_SLUG, $slug);
            // Rewrite rules embed the slug, so they must be rebuilt.
            HFCD_View::register_rewrites();
            flush_rewrite_rules();
        }

        if (!empty($_POST['hfcd_rotate'])) {
            HFCD_Auth::rotate();
        }

        wp_safe_redirect(add_query_arg('page', self::PAGE, admin_url('tools.php')));
        exit;
    }
}
