<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_enqueue_assets(): void
{
    wp_enqueue_style('momsy-style', get_stylesheet_uri(), [], MOMSY_VERSION);
    wp_enqueue_script('momsy-theme', MOMSY_URI . '/assets/js/theme.js', [], MOMSY_VERSION, true);

    wp_localize_script('momsy-theme', 'momsyConfig', [
        'theme' => momsy_get_theme_mode(),
    ]);
}
add_action('wp_enqueue_scripts', 'momsy_enqueue_assets');
