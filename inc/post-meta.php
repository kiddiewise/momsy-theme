<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_get_post_views(int $post_id): int
{
    return (int) get_post_meta($post_id, 'momsy_views', true);
}

function momsy_track_post_view(int $post_id): void
{
    if (is_admin() || ! is_single()) {
        return;
    }

    $views = momsy_get_post_views($post_id);
    update_post_meta($post_id, 'momsy_views', $views + 1);
}

function momsy_get_post_likes(int $post_id): int
{
    return (int) get_post_meta($post_id, 'momsy_likes', true);
}

function momsy_post_stats(bool $buttons = false): void
{
    $post_id = get_the_ID();
    $comments = get_comments_number($post_id);
    $views = momsy_get_post_views($post_id);
    $likes = momsy_get_post_likes($post_id);

    $items = [
        '❤ ' . number_format_i18n($likes),
        '💬 ' . number_format_i18n($comments),
        '👁 ' . number_format_i18n($views),
    ];

    foreach ($items as $item) {
        echo '<span class="meta-pill">' . esc_html($item) . '</span>';
    }

    if ($buttons) {
        echo '<button class="meta-pill" type="button">♡ ' . esc_html__('Beğen', 'momsy') . '</button>';
        echo '<button class="meta-pill" type="button">' . esc_html__('Kaydet', 'momsy') . '</button>';
    }
}
