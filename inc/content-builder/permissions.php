<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_can_access_content_builder(): bool
{
    return is_user_logged_in() && current_user_can('edit_posts');
}

function momsy_get_content_builder_guard_state(): array
{
    if (! is_user_logged_in()) {
        return [
            'allowed'     => false,
            'status_code' => 401,
            'title'       => __('Yazı oluşturmak için giriş yapmalısın', 'momsy'),
            'message'     => __('Bu ekran yalnızca içerik üreticileri için açık. Giriş yaptıktan sonra doğrudan içerik oluşturma alanına dönebilirsin.', 'momsy'),
            'action_url'  => wp_login_url(get_permalink()),
            'action_text' => __('Giriş yap', 'momsy'),
        ];
    }

    if (! current_user_can('edit_posts')) {
        return [
            'allowed'     => false,
            'status_code' => 403,
            'title'       => __('Bu alana erişim iznin yok', 'momsy'),
            'message'     => __('İçerik oluşturma ekranı yalnızca yazı oluşturma yetkisi olan kullanıcılar için kullanılabilir.', 'momsy'),
            'action_url'  => home_url('/'),
            'action_text' => __('Ana sayfaya dön', 'momsy'),
        ];
    }

    return [
        'allowed'     => true,
        'status_code' => 200,
    ];
}
