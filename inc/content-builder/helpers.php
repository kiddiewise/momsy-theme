<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_is_content_builder_page(): bool
{
    return is_page('yazi-olustur');
}

function momsy_get_content_builder_boot_data(): array
{
    $current_user = wp_get_current_user();
    $editing_post = momsy_get_content_builder_editing_post();
    $initial_state = $editing_post instanceof WP_Post
        ? momsy_get_content_builder_saved_state($editing_post->ID)
        : momsy_get_empty_builder_state();

    return [
        'pageTitle'     => __('Yeni Yazı Oluştur', 'momsy'),
        'restUrl'       => esc_url_raw(rest_url()),
        'restNonce'     => wp_create_nonce('wp_rest'),
        'postType'      => 'post',
        'canPublish'    => current_user_can('publish_posts'),
        'saveEndpoint'  => esc_url_raw(rest_url('momsy/v1/builder/draft')),
        'mediaEndpoint' => esc_url_raw(rest_url('wp/v2/media')),
        'initialState'  => $initial_state,
        'currentPost'   => $editing_post instanceof WP_Post ? [
            'id'          => $editing_post->ID,
            'status'      => $editing_post->post_status,
            'previewLink' => get_preview_post_link($editing_post->ID),
            'permalink'   => get_permalink($editing_post->ID),
        ] : null,
        'currentUser'   => [
            'id'          => get_current_user_id(),
            'displayName' => $current_user instanceof WP_User ? $current_user->display_name : '',
        ],
        'i18n'          => [
            'titleLabel'                => __('Yazı başlığı', 'momsy'),
            'titlePlaceholder'          => __('Başlığını yazmaya başla...', 'momsy'),
            'featuredImageLabel'        => __('Öne çıkan görsel', 'momsy'),
            'featuredImageHelp'         => __('Kapak görseli yükleyerek yazının önizlemesini daha gerçek hale getirebilirsin.', 'momsy'),
            'contentSectionTitle'       => __('İçerik akışı', 'momsy'),
            'contentSectionDescription' => __('Blokları düzenle, görselleri yükle ve taslak olarak kaydederek önizle.', 'momsy'),
            'emptyStateTitle'           => __('İçerik blokları burada görünecek', 'momsy'),
            'emptyStateDescription'     => __('İlk bloğunu ekleyerek yazı akışını oluşturmaya başlayabilirsin.', 'momsy'),
            'addContent'                => __('İçerik ekle +', 'momsy'),
            'modalTitle'                => __('Bir blok seç', 'momsy'),
            'modalDescription'          => __('Aşağıdaki bileşenlerden birini seçerek yazı akışına yeni bir parça ekleyebilirsin.', 'momsy'),
            'closeModal'                => __('Kapat', 'momsy'),
            'selectBlock'               => __('Bloğu ekle', 'momsy'),
            'moveUp'                    => __('Yukarı taşı', 'momsy'),
            'moveDown'                  => __('Aşağı taşı', 'momsy'),
            'deleteBlock'               => __('Bloğu sil', 'momsy'),
            'saveDraft'                 => __('Taslak Kaydet', 'momsy'),
            'publish'                   => __('Yayınla', 'momsy'),
            'statusLabel'               => __('Canlı builder', 'momsy'),
            'savedDraft'                => __('Taslak kaydedildi', 'momsy'),
            'saving'                    => __('Kaydediliyor...', 'momsy'),
            'openPreview'               => __('Önizlemeyi aç', 'momsy'),
            'continueEditing'           => __('Taslağa devam et', 'momsy'),
            'saveError'                 => __('Taslak kaydedilirken bir hata oluştu.', 'momsy'),
        ],
    ];
}
