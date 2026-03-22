<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_reading_time(?int $post_id = null): string
{
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $words   = str_word_count(wp_strip_all_tags($content));
    $minutes = max(1, (int) ceil($words / 200));

    return sprintf(_n('%s dk okuma', '%s dk okuma', $minutes, 'momsy'), $minutes);
}

function momsy_post_category_badges(?int $post_id = null): void
{
    $post_id = $post_id ?: get_the_ID();
    $terms = get_the_category($post_id);

    if (empty($terms)) {
        return;
    }

    foreach (array_slice($terms, 0, 2) as $term) {
        echo '<span class="category-badge">#' . esc_html($term->name) . '</span>';
    }
}
