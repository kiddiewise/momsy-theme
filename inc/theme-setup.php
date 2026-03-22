<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('custom-line-height');
    add_theme_support('custom-spacing');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    add_image_size('momsy-card', 720, 520, true);
    add_image_size('momsy-mini', 184, 140, true);

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
    echo '<li><a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">' . esc_html__('Yazılar', 'momsy') . '</a></li>';
    echo '</ul>';
}

function momsy_get_theme_mode(): string
{
    return get_theme_mod('momsy_default_theme', 'dark');
}
