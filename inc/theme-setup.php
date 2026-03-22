<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_set_content_width(): void
{
    $GLOBALS['content_width'] = apply_filters('momsy_content_width', 760);
}
add_action('after_setup_theme', 'momsy_set_content_width', 0);

function momsy_theme_setup(): void
{
    load_theme_textdomain('momsy', MOMSY_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'               => 48,
        'width'                => 160,
        'flex-height'          => true,
        'flex-width'           => true,
        'unlink-homepage-logo' => true,
    ]);
    add_theme_support('custom-line-height');
    add_theme_support('custom-spacing');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets']);

    add_image_size('momsy-card', 720, 520, true);
    add_image_size('momsy-mini', 184, 140, true);
    add_image_size('momsy-hero', 1280, 960, true);

    add_editor_style('assets/css/editor.css');

    register_nav_menus([
        'primary'         => __('Ana Menü', 'momsy'),
        'footer'          => __('Footer Menü', 'momsy'),
        'home_categories' => __('Ana Sayfa Kategorileri', 'momsy'),
    ]);
}
add_action('after_setup_theme', 'momsy_theme_setup');

function momsy_fallback_menu(): void
{
    echo '<ul class="nav-list">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Ana Sayfa', 'momsy') . '</a></li>';
    echo '<li><a href="' . esc_url(momsy_get_posts_page_url()) . '">' . esc_html__('Yazılar', 'momsy') . '</a></li>';
    echo '</ul>';
}

function momsy_get_theme_mode(): string
{
    return momsy_sanitize_theme_mode((string) get_theme_mod('momsy_default_theme', 'system'));
}

function momsy_get_active_theme_mode(): string
{
    return 'light' === momsy_get_theme_mode() ? 'light' : 'dark';
}
