<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_register_content_builder_rest_routes(): void
{
    register_rest_route(
        'momsy/v1',
        '/builder/draft',
        [
            'methods'             => WP_REST_Server::CREATABLE,
            'permission_callback' => 'momsy_can_access_content_builder',
            'callback'            => 'momsy_handle_content_builder_save_request',
        ]
    );
}
add_action('rest_api_init', 'momsy_register_content_builder_rest_routes');

function momsy_handle_content_builder_save_request(WP_REST_Request $request): WP_REST_Response
{
    $params = $request->get_json_params();
    $state = momsy_sanitize_builder_state($params['state'] ?? []);
    $post_id = isset($params['postId']) ? absint($params['postId']) : 0;
    $requested_status = isset($params['status']) && $params['status'] === 'publish' ? 'publish' : 'draft';
    $post_status = $requested_status === 'publish' && current_user_can('publish_posts') ? 'publish' : 'draft';
    $title = $state['title'] !== '' ? $state['title'] : __('İsimsiz taslak', 'momsy');
    $post_type = 'post';

    if ($post_id > 0 && ! current_user_can('edit_post', $post_id)) {
        return new WP_REST_Response(
            [
                'message' => __('Bu taslağı düzenleme iznin yok.', 'momsy'),
            ],
            403
        );
    }

    $post_data = [
        'post_type'    => $post_type,
        'post_status'  => $post_status,
        'post_title'   => $title,
        'post_content' => momsy_render_builder_state($state),
    ];

    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        $result = wp_update_post(wp_slash($post_data), true);
    } else {
        $post_data['post_author'] = get_current_user_id();
        $result = wp_insert_post(wp_slash($post_data), true);
    }

    if (is_wp_error($result)) {
        return new WP_REST_Response(
            [
                'message' => $result->get_error_message(),
            ],
            500
        );
    }

    $saved_post_id = (int) $result;
    $featured_image = is_array($state['featuredImage'] ?? null) ? $state['featuredImage'] : null;
    $featured_image_id = $featured_image ? absint($featured_image['id'] ?? 0) : 0;

    update_post_meta($saved_post_id, '_momsy_builder_state', wp_json_encode($state));

    if ($featured_image_id > 0) {
        set_post_thumbnail($saved_post_id, $featured_image_id);
    } else {
        delete_post_thumbnail($saved_post_id);
    }

    $saved_post = get_post($saved_post_id);
    $preview_link = get_preview_post_link($saved_post_id);
    $permalink = get_permalink($saved_post_id);

    return new WP_REST_Response(
        [
            'id'             => $saved_post_id,
            'postId'         => $saved_post_id,
            'status'         => $saved_post instanceof WP_Post ? $saved_post->post_status : $post_status,
            'title'          => get_the_title($saved_post_id),
            'previewLink'    => $preview_link ? $preview_link : $permalink,
            'permalink'      => $permalink,
            'editBuilderUrl' => add_query_arg('post_id', (string) $saved_post_id, home_url('/yazi-olustur/')),
            'updatedAt'      => current_time('mysql'),
        ],
        200
    );
}
