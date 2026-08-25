<?php
/**
 * Document shell. Expects $page_title and optionally $page_subtitle.
 *
 * @var string $page_title
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html lang="<?php echo esc_attr(str_replace('_', '-', get_locale())); ?>">
<head>
    <meta charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light dark">
    <title><?php echo esc_html($page_title); ?> · Feature Check</title>
    <link rel="stylesheet" href="<?php echo esc_url(HFCD_View::asset('dashboard.css')); ?>">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="topbar">
    <a class="brand" href="<?php echo esc_url(HFCD_View::base_url()); ?>">
        <span class="brand-mark" aria-hidden="true">FC</span>
        <span class="brand-text">
            <strong>Feature Check</strong>
            <span><?php echo esc_html(get_bloginfo('name')); ?></span>
        </span>
    </a>
    <nav class="topbar-nav">
        <a href="<?php echo esc_url(HFCD_View::base_url()); ?>">All runs</a>
        <a href="<?php echo esc_url(HFCD_View::list_url(['status' => 'running'])); ?>">Running</a>
        <a href="<?php echo esc_url(HFCD_View::list_url(['status' => 'failed'])); ?>">Failed</a>
    </nav>
</header>
<main id="main" class="shell">
