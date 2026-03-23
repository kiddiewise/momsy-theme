<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_enqueue_content_builder_assets(): void
{
    if (! momsy_is_content_builder_page()) {
        return;
    }

    // The builder screen has its own visual shell, but it should still inherit theme tokens.
    wp_enqueue_style(
        'momsy-builder',
        MOMSY_URI . '/assets/css/builder.css',
        ['momsy-main'],
        momsy_get_asset_version('assets/css/builder.css')
    );

    if (! momsy_can_access_content_builder()) {
        return;
    }

    // Load the React entry point only on the protected builder screen.
    wp_enqueue_script(
        'momsy-builder-app',
        MOMSY_URI . '/assets/src/builder/index.js',
        ['wp-element'],
        momsy_get_asset_version('assets/src/builder/index.js'),
        true
    );

    wp_add_inline_script(
        'momsy-builder-app',
        'window.momsyBuilderConfig = ' . wp_json_encode(momsy_get_content_builder_boot_data()) . ';',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'momsy_enqueue_content_builder_assets', 20);

function momsy_content_builder_script_loader_tag(string $tag, string $handle, string $src): string
{
    if ('momsy-builder-app' !== $handle) {
        return $tag;
    }

    // Use native ES modules so the builder source can stay split into small files.
    return sprintf(
        '<script type="module" src="%1$s" id="%2$s-js"></script>',
        esc_url($src),
        esc_attr($handle)
    );
}
add_filter('script_loader_tag', 'momsy_content_builder_script_loader_tag', 10, 3);
