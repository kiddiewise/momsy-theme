<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_get_empty_builder_state(): array
{
    return [
        'version'       => 1,
        'title'         => '',
        'featuredImage' => null,
        'blocks'        => [],
    ];
}

function momsy_get_content_builder_saved_state(int $post_id): array
{
    if ($post_id <= 0) {
        return momsy_get_empty_builder_state();
    }

    $raw_state = get_post_meta($post_id, '_momsy_builder_state', true);

    if (! is_string($raw_state) || $raw_state === '') {
        return momsy_get_empty_builder_state();
    }

    $decoded = json_decode($raw_state, true);

    if (! is_array($decoded)) {
        return momsy_get_empty_builder_state();
    }

    return momsy_sanitize_builder_state($decoded);
}

function momsy_get_content_builder_editing_post(): ?WP_Post
{
    $requested_post_id = isset($_GET['post_id']) ? absint(wp_unslash($_GET['post_id'])) : 0;

    if ($requested_post_id <= 0) {
        return null;
    }

    $post = get_post($requested_post_id);

    if (! $post instanceof WP_Post) {
        return null;
    }

    if ($post->post_type !== 'post' || ! current_user_can('edit_post', $post->ID)) {
        return null;
    }

    return $post;
}

function momsy_sanitize_builder_state($state): array
{
    $safe_state = momsy_get_empty_builder_state();

    if (! is_array($state)) {
        return $safe_state;
    }

    $safe_state['version'] = isset($state['version']) ? max(1, (int) $state['version']) : 1;
    $safe_state['title'] = isset($state['title']) ? sanitize_text_field((string) $state['title']) : '';
    $safe_state['featuredImage'] = momsy_sanitize_builder_featured_image($state['featuredImage'] ?? null);

    if (! empty($state['blocks']) && is_array($state['blocks'])) {
        foreach ($state['blocks'] as $block) {
            $safe_block = momsy_sanitize_builder_block($block);

            if ($safe_block !== null) {
                $safe_state['blocks'][] = $safe_block;
            }
        }
    }

    return $safe_state;
}

function momsy_sanitize_builder_featured_image($image): ?array
{
    if (! is_array($image)) {
        return null;
    }

    $attachment_id = isset($image['id']) ? absint($image['id']) : 0;
    $url = isset($image['url']) ? esc_url_raw((string) $image['url']) : '';
    $alt = isset($image['alt']) ? sanitize_text_field((string) $image['alt']) : '';

    if ($attachment_id <= 0 && $url === '') {
        return null;
    }

    return [
        'id'  => $attachment_id,
        'url' => $url,
        'alt' => $alt,
    ];
}

function momsy_sanitize_builder_block($block): ?array
{
    if (! is_array($block) || empty($block['type'])) {
        return null;
    }

    $type = sanitize_key((string) $block['type']);
    $id = isset($block['id']) ? sanitize_text_field((string) $block['id']) : uniqid('blk_', false);
    $props = is_array($block['props'] ?? null) ? $block['props'] : [];

    switch ($type) {
        case 'text':
            $props = [
                'html' => isset($props['html']) ? (string) $props['html'] : '',
            ];
            break;

        case 'heading':
            $props = [
                'level' => in_array(($props['level'] ?? 'h2'), ['h2', 'h3', 'h4'], true) ? (string) $props['level'] : 'h2',
                'text'  => sanitize_text_field((string) ($props['text'] ?? '')),
                'align' => in_array(($props['align'] ?? 'left'), ['left', 'center', 'right'], true) ? (string) $props['align'] : 'left',
            ];
            break;

        case 'image':
            $props = momsy_sanitize_builder_image_props($props);
            break;

        case 'quote':
            $props = [
                'text' => sanitize_textarea_field((string) ($props['text'] ?? '')),
                'cite' => sanitize_text_field((string) ($props['cite'] ?? '')),
            ];
            break;

        case 'cta':
            $props = [
                'title'       => sanitize_text_field((string) ($props['title'] ?? '')),
                'description' => sanitize_textarea_field((string) ($props['description'] ?? '')),
                'buttonLabel' => sanitize_text_field((string) ($props['buttonLabel'] ?? '')),
                'buttonUrl'   => esc_url_raw((string) ($props['buttonUrl'] ?? '')),
                'variant'     => in_array(($props['variant'] ?? 'soft'), ['soft', 'strong', 'outline'], true) ? (string) $props['variant'] : 'soft',
            ];
            break;

        case 'slider':
            $items = [];

            if (! empty($props['items']) && is_array($props['items'])) {
                foreach ($props['items'] as $item) {
                    $items[] = momsy_sanitize_builder_media_item($item);
                }
            }

            $props = [
                'items' => $items,
            ];
            break;

        case 'divider':
            $props = [
                'style'   => in_array(($props['style'] ?? 'line'), ['line', 'space', 'dots'], true) ? (string) $props['style'] : 'line',
                'spacing' => in_array(($props['spacing'] ?? 'md'), ['sm', 'md', 'lg'], true) ? (string) $props['spacing'] : 'md',
            ];
            break;

        default:
            return null;
    }

    return [
        'id'    => $id,
        'type'  => $type,
        'props' => $props,
    ];
}

function momsy_sanitize_builder_image_props(array $props): array
{
    return [
        'attachmentId' => isset($props['attachmentId']) ? absint($props['attachmentId']) : 0,
        'url'          => isset($props['url']) ? esc_url_raw((string) $props['url']) : '',
        'alt'          => sanitize_text_field((string) ($props['alt'] ?? '')),
        'caption'      => sanitize_text_field((string) ($props['caption'] ?? '')),
        'size'         => in_array(($props['size'] ?? 'large'), ['thumbnail', 'medium', 'large', 'full'], true) ? (string) $props['size'] : 'large',
    ];
}

function momsy_sanitize_builder_media_item($item): array
{
    $item = is_array($item) ? $item : [];

    return [
        'attachmentId' => isset($item['attachmentId']) ? absint($item['attachmentId']) : 0,
        'url'          => isset($item['url']) ? esc_url_raw((string) $item['url']) : '',
        'alt'          => sanitize_text_field((string) ($item['alt'] ?? '')),
        'caption'      => sanitize_text_field((string) ($item['caption'] ?? '')),
    ];
}

function momsy_prepare_builder_media_data(array $media): array
{
    $attachment_id = absint($media['attachmentId'] ?? 0);
    $url = isset($media['url']) ? esc_url_raw((string) $media['url']) : '';
    $alt = isset($media['alt']) ? sanitize_text_field((string) $media['alt']) : '';

    if ($attachment_id > 0) {
        $image_url = wp_get_attachment_image_url($attachment_id, 'full');

        if (is_string($image_url) && $image_url !== '') {
            $url = $image_url;
        }

        if ($alt === '') {
            $alt = (string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        }
    }

    return [
        'attachmentId' => $attachment_id,
        'url'          => $url,
        'alt'          => $alt,
        'caption'      => sanitize_text_field((string) ($media['caption'] ?? '')),
        'size'         => isset($media['size']) ? (string) $media['size'] : 'large',
    ];
}

function momsy_render_builder_state(array $state): string
{
    $state = momsy_sanitize_builder_state($state);
    $html = '';

    foreach ($state['blocks'] as $block) {
        $html .= momsy_render_builder_block($block);
    }

    return trim($html);
}

function momsy_render_builder_block(array $block): string
{
    $type = $block['type'] ?? '';
    $props = is_array($block['props'] ?? null) ? $block['props'] : [];

    switch ($type) {
        case 'text':
            $html = (string) ($props['html'] ?? '');

            if ($html === '') {
                return '';
            }

            if (! preg_match('/<[^>]+>/', $html)) {
                $html = wpautop(esc_html($html));
            } else {
                $html = wp_kses_post($html);
            }

            return '<div class="momsy-builder-content-block momsy-builder-content-block--text">' . $html . '</div>';

        case 'heading':
            $level = in_array(($props['level'] ?? 'h2'), ['h2', 'h3', 'h4'], true) ? (string) $props['level'] : 'h2';
            $text = sanitize_text_field((string) ($props['text'] ?? ''));
            $align = in_array(($props['align'] ?? 'left'), ['left', 'center', 'right'], true) ? (string) $props['align'] : 'left';

            if ($text === '') {
                return '';
            }

            return sprintf(
                '<%1$s class="momsy-builder-content-block momsy-builder-content-block--heading" style="text-align:%2$s;">%3$s</%1$s>',
                esc_html($level),
                esc_attr($align),
                esc_html($text)
            );

        case 'image':
            return momsy_render_builder_image_block($props);

        case 'quote':
            $text = sanitize_textarea_field((string) ($props['text'] ?? ''));
            $cite = sanitize_text_field((string) ($props['cite'] ?? ''));

            if ($text === '') {
                return '';
            }

            return '<blockquote class="momsy-builder-content-block momsy-builder-content-block--quote"><p>' .
                esc_html($text) .
                '</p>' .
                ($cite !== '' ? '<cite>' . esc_html($cite) . '</cite>' : '') .
                '</blockquote>';

        case 'cta':
            return momsy_render_builder_cta_block($props);

        case 'slider':
            return momsy_render_builder_slider_block($props);

        case 'divider':
            return momsy_render_builder_divider_block($props);

        default:
            return '';
    }
}

function momsy_render_builder_image_block(array $props): string
{
    $media = momsy_prepare_builder_media_data($props);
    $caption = sanitize_text_field((string) ($media['caption'] ?? ''));

    if ($media['url'] === '') {
        return '';
    }

    $image_html = sprintf(
        '<img src="%1$s" alt="%2$s" loading="lazy">',
        esc_url($media['url']),
        esc_attr($media['alt'])
    );

    return '<figure class="momsy-builder-content-block momsy-builder-content-block--image">' .
        $image_html .
        ($caption !== '' ? '<figcaption>' . esc_html($caption) . '</figcaption>' : '') .
        '</figure>';
}

function momsy_render_builder_cta_block(array $props): string
{
    $title = sanitize_text_field((string) ($props['title'] ?? ''));
    $description = sanitize_textarea_field((string) ($props['description'] ?? ''));
    $button_label = sanitize_text_field((string) ($props['buttonLabel'] ?? ''));
    $button_url = esc_url((string) ($props['buttonUrl'] ?? ''));
    $variant = in_array(($props['variant'] ?? 'soft'), ['soft', 'strong', 'outline'], true) ? (string) $props['variant'] : 'soft';

    if ($title === '' && $description === '' && $button_label === '') {
        return '';
    }

    $html = '<section class="momsy-builder-content-block momsy-builder-content-block--cta momsy-article-cta momsy-article-cta--' . esc_attr($variant) . '">';

    if ($title !== '') {
        $html .= '<h3 class="momsy-article-cta__title">' . esc_html($title) . '</h3>';
    }

    if ($description !== '') {
        $html .= '<p class="momsy-article-cta__description">' . esc_html($description) . '</p>';
    }

    if ($button_label !== '') {
        $button_tag = $button_url !== '' ? 'a' : 'span';
        $button_attrs = $button_tag === 'a'
            ? ' href="' . esc_url($button_url) . '"'
            : '';

        $html .= '<' . $button_tag . ' class="momsy-article-cta__button"' . $button_attrs . '>' . esc_html($button_label) . '</' . $button_tag . '>';
    }

    $html .= '</section>';

    return $html;
}

function momsy_render_builder_slider_block(array $props): string
{
    $items = [];

    if (! empty($props['items']) && is_array($props['items'])) {
        foreach ($props['items'] as $item) {
            $media = momsy_prepare_builder_media_data(is_array($item) ? $item : []);

            if ($media['url'] !== '') {
                $items[] = $media;
            }
        }
    }

    if ($items === []) {
        return '';
    }

    $html = '<section class="momsy-builder-content-block momsy-builder-content-block--slider momsy-article-slider">';
    $html .= '<div class="momsy-article-slider__track">';

    foreach ($items as $item) {
        $html .= '<figure class="momsy-article-slider__item">';
        $html .= '<img src="' . esc_url($item['url']) . '" alt="' . esc_attr($item['alt']) . '" loading="lazy">';

        if ($item['caption'] !== '') {
            $html .= '<figcaption>' . esc_html($item['caption']) . '</figcaption>';
        }

        $html .= '</figure>';
    }

    $html .= '</div></section>';

    return $html;
}

function momsy_render_builder_divider_block(array $props): string
{
    $style = in_array(($props['style'] ?? 'line'), ['line', 'space', 'dots'], true) ? (string) $props['style'] : 'line';
    $spacing = in_array(($props['spacing'] ?? 'md'), ['sm', 'md', 'lg'], true) ? (string) $props['spacing'] : 'md';

    $inner = '';

    if ($style === 'line') {
        $inner = '<span class="momsy-article-divider__line" aria-hidden="true"></span>';
    } elseif ($style === 'space') {
        $inner = '<span class="momsy-article-divider__space" aria-hidden="true"></span>';
    } else {
        $inner = '<span class="momsy-article-divider__dots" aria-hidden="true"><i></i><i></i><i></i></span>';
    }

    return '<div class="momsy-builder-content-block momsy-builder-content-block--divider momsy-article-divider momsy-article-divider--' .
        esc_attr($style) .
        ' momsy-article-divider--' .
        esc_attr($spacing) .
        '">' .
        $inner .
        '</div>';
}
