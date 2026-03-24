<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_get_post_views(int $post_id): int
{
    return (int) get_post_meta($post_id, 'momsy_views', true);
}

function momsy_is_probably_bot(): bool
{
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower((string) wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

    if ('' === $user_agent) {
        return false;
    }

    return 1 === preg_match('/bot|crawl|slurp|spider|mediapartners|facebookexternalhit|preview|monitor|headless/i', $user_agent);
}

function momsy_maybe_track_post_view(): void
{
    if (is_admin() || ! is_singular('post') || is_preview() || post_password_required()) {
        return;
    }

    if (momsy_is_probably_bot()) {
        return;
    }

    $post_id = get_queried_object_id();

    if ($post_id <= 0) {
        return;
    }

    $cookie_name = 'momsy_viewed_' . $post_id;

    if (isset($_COOKIE[$cookie_name])) {
        return;
    }

    update_post_meta($post_id, 'momsy_views', momsy_get_post_views($post_id) + 1);

    if (! headers_sent()) {
        setcookie(
            $cookie_name,
            '1',
            [
                'expires'  => time() + (6 * HOUR_IN_SECONDS),
                'path'     => COOKIEPATH ? COOKIEPATH : '/',
                'domain'   => COOKIE_DOMAIN,
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    $_COOKIE[$cookie_name] = '1';
}
add_action('template_redirect', 'momsy_maybe_track_post_view');

function momsy_get_post_likes(int $post_id): int
{
    return (int) get_post_meta($post_id, 'momsy_likes', true);
}

function momsy_is_post_liked(int $post_id): bool
{
    return isset($_COOKIE['momsy_liked_' . $post_id]);
}

function momsy_get_post_stat_items(?int $post_id = null): array
{
    $post_id  = $post_id ?: get_the_ID();
    $comments = get_comments_number($post_id);
    $views    = momsy_get_post_views($post_id);
    $likes    = momsy_get_post_likes($post_id);

    return [
        [
            'icon'    => 'heart',
            'key'     => 'likes',
            'value'   => number_format_i18n($likes),
            'label'   => __('beğeni', 'momsy'),
            'sr_text' => sprintf(__('%s beğeni', 'momsy'), number_format_i18n($likes)),
        ],
        [
            'icon'    => 'chat',
            'key'     => 'comments',
            'value'   => number_format_i18n($comments),
            'label'   => __('yorum', 'momsy'),
            'sr_text' => sprintf(__('%s yorum', 'momsy'), number_format_i18n($comments)),
        ],
        [
            'icon'    => 'eye',
            'key'     => 'views',
            'value'   => number_format_i18n($views),
            'label'   => __('okunma', 'momsy'),
            'sr_text' => sprintf(__('%s okunma', 'momsy'), number_format_i18n($views)),
        ],
    ];
}

function momsy_render_like_button(?int $post_id = null, string $class_name = 'meta-pill meta-pill--action'): void
{
    $post_id = $post_id ?: get_the_ID();
    $liked   = momsy_is_post_liked($post_id);

    echo '<button class="' . esc_attr($class_name) . '" type="button" data-like-post="' . esc_attr((string) $post_id) . '" data-label-default="' . esc_attr__('Beğen', 'momsy') . '" data-label-active="' . esc_attr__('Beğenildi', 'momsy') . '" aria-pressed="' . ($liked ? 'true' : 'false') . '">';
    echo '<span class="meta-pill__icon">' . momsy_get_icon_svg('heart') . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '<span data-label-text>' . esc_html($liked ? __('Beğenildi', 'momsy') : __('Beğen', 'momsy')) . '</span>';
    echo '</button>';
}

function momsy_post_stats(bool $buttons = false, array $exclude_keys = []): void
{
    $post_id = get_the_ID();
    $items   = momsy_get_post_stat_items($post_id);

    foreach ($items as $item) {
        if (in_array($item['key'], $exclude_keys, true)) {
            continue;
        }

        $classes = 'meta-pill meta-pill--stat';

        if ('likes' === $item['key']) {
            $classes .= ' meta-pill--likes';
        }

        echo '<span class="' . esc_attr($classes) . '" data-stat="' . esc_attr($item['key']) . '" data-post-id="' . esc_attr((string) $post_id) . '">';
        echo '<span class="meta-pill__icon">' . momsy_get_icon_svg($item['icon']) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<span class="meta-pill__value">' . esc_html($item['value']) . '</span>';
        echo '<span class="meta-pill__label">' . esc_html($item['label']) . '</span>';
        echo '<span class="screen-reader-text">' . esc_html($item['sr_text']) . '</span>';
        echo '</span>';
    }

    if ($buttons) {
        momsy_render_like_button($post_id);

        echo '<a class="meta-pill meta-pill--action" href="#comments">';
        echo '<span class="meta-pill__icon">' . momsy_get_icon_svg('chat') . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<span data-label-text>' . esc_html__('Yorumlar', 'momsy') . '</span>';
        echo '</a>';

        echo '<button class="meta-pill meta-pill--action" type="button" data-share-post="' . esc_url(get_permalink($post_id)) . '" data-label-default="' . esc_attr__('Paylaş', 'momsy') . '" data-label-active="' . esc_attr__('Kopyalandı', 'momsy') . '">';
        echo '<span class="meta-pill__icon">' . momsy_get_icon_svg('share') . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<span data-label-text>' . esc_html__('Paylaş', 'momsy') . '</span>';
        echo '</button>';

        echo '<button class="meta-pill meta-pill--action" type="button" data-save-post="' . esc_attr((string) $post_id) . '" data-label-default="' . esc_attr__('Kaydet', 'momsy') . '" data-label-active="' . esc_attr__('Kaydedildi', 'momsy') . '" aria-pressed="false">';
        echo '<span class="meta-pill__icon">' . momsy_get_icon_svg('bookmark') . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<span data-label-text>' . esc_html__('Kaydet', 'momsy') . '</span>';
        echo '</button>';
    }
}

function momsy_handle_post_like(): void
{
    check_ajax_referer('momsy_like_post', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;

    if ($post_id <= 0 || 'post' !== get_post_type($post_id)) {
        wp_send_json_error(['message' => __('Geçersiz yazı.', 'momsy')], 400);
    }

    $cookie_name = 'momsy_liked_' . $post_id;
    $likes       = momsy_get_post_likes($post_id);
    $liked       = false;

    if (isset($_COOKIE[$cookie_name])) {
        $likes = max(0, $likes - 1);
        update_post_meta($post_id, 'momsy_likes', $likes);

        if (! headers_sent()) {
            setcookie(
                $cookie_name,
                '',
                [
                    'expires'  => time() - HOUR_IN_SECONDS,
                    'path'     => COOKIEPATH ? COOKIEPATH : '/',
                    'domain'   => COOKIE_DOMAIN,
                    'secure'   => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]
            );
        }

        unset($_COOKIE[$cookie_name]);
    } else {
        $likes++;
        $liked = true;
        update_post_meta($post_id, 'momsy_likes', $likes);

        if (! headers_sent()) {
            setcookie(
                $cookie_name,
                '1',
                [
                    'expires'  => time() + (30 * DAY_IN_SECONDS),
                    'path'     => COOKIEPATH ? COOKIEPATH : '/',
                    'domain'   => COOKIE_DOMAIN,
                    'secure'   => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]
            );
        }

        $_COOKIE[$cookie_name] = '1';
    }

    wp_send_json_success(
        [
            'liked' => $liked,
            'count' => $likes,
        ]
    );
}
add_action('wp_ajax_momsy_like_post', 'momsy_handle_post_like');
add_action('wp_ajax_nopriv_momsy_like_post', 'momsy_handle_post_like');
