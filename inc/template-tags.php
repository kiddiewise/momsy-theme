<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_sanitize_theme_mode(string $value): string
{
    $allowed = ['dark', 'light', 'system'];

    return in_array($value, $allowed, true) ? $value : 'system';
}

function momsy_get_posts_page_url(): string
{
    $posts_page_id = (int) get_option('page_for_posts');

    if ($posts_page_id > 0) {
        $posts_page_url = get_permalink($posts_page_id);

        if (is_string($posts_page_url) && '' !== $posts_page_url) {
            return $posts_page_url;
        }
    }

    return home_url('/');
}

function momsy_reading_time(?int $post_id = null): string
{
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $words   = str_word_count(wp_strip_all_tags((string) html_entity_decode((string) $content, ENT_QUOTES, 'UTF-8')));
    $minutes = max(1, (int) ceil($words / 200));

    return sprintf(_n('%s dk okuma', '%s dk okuma', $minutes, 'momsy'), $minutes);
}

function momsy_get_post_primary_category(?int $post_id = null): ?WP_Term
{
    $post_id = $post_id ?: get_the_ID();
    $terms   = get_the_category($post_id);

    if (empty($terms)) {
        return null;
    }

    $primary_term = $terms[0];

    return $primary_term instanceof WP_Term ? $primary_term : null;
}

function momsy_post_category_badges(?int $post_id = null, int $limit = 2): void
{
    $post_id = $post_id ?: get_the_ID();
    $terms   = get_the_category($post_id);

    if (empty($terms)) {
        return;
    }

    foreach (array_slice($terms, 0, $limit) as $term) {
        if (! $term instanceof WP_Term) {
            continue;
        }

        $term_link = get_category_link($term);

        if (is_wp_error($term_link)) {
            continue;
        }

        echo '<a class="category-badge" href="' . esc_url($term_link) . '">';
        echo '<span class="category-badge__hash">#</span>';
        echo '<span>' . esc_html($term->name) . '</span>';
        echo '</a>';
    }
}

function momsy_get_post_card_excerpt(?int $post_id = null, int $words = 20): string
{
    $post_id = $post_id ?: get_the_ID();

    return wp_trim_words(wp_strip_all_tags((string) get_the_excerpt($post_id)), $words);
}

function momsy_get_icon_svg(string $icon, string $title = ''): string
{
    $icons = [
        'arrow-left' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6" /><path d="M9 12h10" /></svg>',
        'bookmark'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 4.75h12a1 1 0 0 1 1 1V20l-7-4-7 4V5.75a1 1 0 0 1 1-1Z" /></svg>',
        'calendar'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.75" y="5.75" width="16.5" height="14.5" rx="2.5" /><path d="M8 3.75v4" /><path d="M16 3.75v4" /><path d="M3.75 10.25h16.5" /></svg>',
        'chat'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18.25 3.75 20v-4.25A7.25 7.25 0 0 1 11 4.75h2a7.25 7.25 0 0 1 0 14.5H7Z" /></svg>',
        'clock'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.25" /><path d="M12 7.75v4.75l3 1.75" /></svg>',
        'eye'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.75 12S6.25 5.75 12 5.75 21.25 12 21.25 12 17.75 18.25 12 18.25 2.75 12 2.75 12Z" /><circle cx="12" cy="12" r="2.75" /></svg>',
        'heart'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20.25 4.7 13.4a4.55 4.55 0 0 1 6.44-6.44L12 7.82l.86-.86a4.55 4.55 0 1 1 6.44 6.44L12 20.25Z" /></svg>',
        'menu'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16" /><path d="M4 12h16" /><path d="M4 17h16" /></svg>',
        'moon'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14.75A7.75 7.75 0 0 1 9.25 5a8.25 8.25 0 1 0 9.75 9.75Z" /></svg>',
        'search'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="6.25" /><path d="m16 16 4.25 4.25" /></svg>',
        'share'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5.5" r="2.25" /><circle cx="6" cy="12" r="2.25" /><circle cx="18" cy="18.5" r="2.25" /><path d="m8 11 7.5-4" /><path d="M8 13l7.5 4" /></svg>',
        'sparkles'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 1.7 4.8L18.5 9.5l-4.8 1.7L12 16l-1.7-4.8L5.5 9.5l4.8-1.7L12 3Z" /><path d="M18 15.5 19 18l2.5 1-2.5 1L18 22.5 17 20l-2.5-1 2.5-1 1-2.5Z" /><path d="M5.5 15 6 16.25 7.25 16.75 6 17.25 5.5 18.5 5 17.25 3.75 16.75 5 16.25 5.5 15Z" /></svg>',
        'sun'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3.75" /><path d="M12 2.75v2.5" /><path d="M12 18.75v2.5" /><path d="m5.46 5.46 1.77 1.77" /><path d="m16.77 16.77 1.77 1.77" /><path d="M2.75 12h2.5" /><path d="M18.75 12h2.5" /><path d="m5.46 18.54 1.77-1.77" /><path d="m16.77 7.23 1.77-1.77" /></svg>',
    ];

    if (! isset($icons[$icon])) {
        return '';
    }

    $icon_markup = $icons[$icon];

    if ('' !== $title) {
        $icon_markup = str_replace('aria-hidden="true"', 'role="img"', $icon_markup);
        $icon_markup = preg_replace('/<svg([^>]*)>/', '<svg$1><title>' . esc_html($title) . '</title>', $icon_markup, 1);
    }

    return wp_kses(
        $icon_markup,
        [
            'svg'    => [
                'aria-hidden'     => true,
                'class'           => true,
                'fill'            => true,
                'role'            => true,
                'stroke'          => true,
                'stroke-linecap'  => true,
                'stroke-linejoin' => true,
                'stroke-width'    => true,
                'viewBox'         => true,
                'xmlns'           => true,
            ],
            'path'   => [
                'd'                => true,
                'fill'             => true,
                'stroke'           => true,
                'stroke-linecap'   => true,
                'stroke-linejoin'  => true,
                'stroke-width'     => true,
            ],
            'rect'   => [
                'fill'             => true,
                'height'           => true,
                'rx'               => true,
                'stroke'           => true,
                'stroke-width'     => true,
                'width'            => true,
                'x'                => true,
                'y'                => true,
            ],
            'circle' => [
                'cx'               => true,
                'cy'               => true,
                'fill'             => true,
                'r'                => true,
                'stroke'           => true,
                'stroke-width'     => true,
            ],
            'title'  => [],
        ]
    );
}

function momsy_the_icon(string $icon, string $title = ''): void
{
    echo momsy_get_icon_svg($icon, $title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
