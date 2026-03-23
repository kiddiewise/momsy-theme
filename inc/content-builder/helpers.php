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

    return [
        'pageTitle'   => __('Yeni Yazı Oluştur', 'momsy'),
        'restUrl'     => esc_url_raw(rest_url()),
        'restNonce'   => wp_create_nonce('wp_rest'),
        'postType'    => 'post',
        'canPublish'  => current_user_can('publish_posts'),
        'currentUser' => [
            'id'          => get_current_user_id(),
            'displayName' => $current_user instanceof WP_User ? $current_user->display_name : '',
        ],
        'i18n'        => [
            'titleLabel'                => __('Yazı başlığı', 'momsy'),
            'titlePlaceholder'          => __('Başlığını yazmaya başla...', 'momsy'),
            'featuredImageLabel'        => __('Öne çıkan görsel', 'momsy'),
            'featuredImageHelp'         => __('Kapak görseli seçme alanı bir sonraki adımda aktif olacak.', 'momsy'),
            'contentSectionTitle'       => __('İçerik akışı', 'momsy'),
            'contentSectionDescription' => __('Bloklar sıralı şekilde burada birikecek. Bu sürümde ekleme, silme ve yukarı aşağı sıralama aktif.', 'momsy'),
            'emptyStateTitle'           => __('İçerik blokları burada görünecek', 'momsy'),
            'emptyStateDescription'     => __('İlk sürümde bu alan blokları listeleyecek. Şimdilik boş shell görünümü hazırlanıyor.', 'momsy'),
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
            'statusLabel'               => __('Hazırlık aşaması', 'momsy'),
        ],
    ];
}
