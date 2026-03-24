<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_customize_register(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('momsy_theme_options', [
        'title'    => __('Momsy Tema Ayarları', 'momsy'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('momsy_default_theme', [
        'default'           => 'system',
        'sanitize_callback' => 'momsy_sanitize_theme_mode',
    ]);

    $wp_customize->add_control('momsy_default_theme', [
        'label'   => __('Varsayılan Tema', 'momsy'),
        'section' => 'momsy_theme_options',
        'type'    => 'radio',
        'choices' => [
            'system' => __('Sistem', 'momsy'),
            'dark'   => __('Koyu', 'momsy'),
            'light'  => __('Açık', 'momsy'),
        ],
    ]);

    $wp_customize->add_setting('momsy_home_badge', [
        'default'           => __('Momsy Blog', 'momsy'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('momsy_home_badge', [
        'label'   => __('Ana Sayfa Etiketi', 'momsy'),
        'section' => 'momsy_theme_options',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('momsy_home_title', [
        'default'           => __('Momsy Blog', 'momsy'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('momsy_home_title', [
        'label'   => __('Ana Sayfa Başlığı', 'momsy'),
        'section' => 'momsy_theme_options',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('momsy_home_desc', [
        'default'           => __('Uzman görüşleri, anne deneyimleri ve topluluk odaklı içerikler tek yerde.', 'momsy'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('momsy_home_desc', [
        'label'   => __('Ana Sayfa Açıklaması', 'momsy'),
        'section' => 'momsy_theme_options',
        'type'    => 'textarea',
    ]);
}
add_action('customize_register', 'momsy_customize_register');
