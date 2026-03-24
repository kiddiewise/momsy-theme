<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_get_asset_version(string $relative_path): string
{
    $asset_path = MOMSY_DIR . '/' . ltrim($relative_path, '/');

    if (! file_exists($asset_path)) {
        return MOMSY_VERSION;
    }

    return (string) filemtime($asset_path);
}

function momsy_enqueue_assets(): void
{
    wp_enqueue_style(
        'momsy-fonts',
        'https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'momsy-main',
        MOMSY_URI . '/assets/css/main.css',
        ['momsy-fonts'],
        momsy_get_asset_version('assets/css/main.css')
    );

    wp_enqueue_script(
        'momsy-theme',
        MOMSY_URI . '/assets/js/theme.js',
        [],
        momsy_get_asset_version('assets/js/theme.js'),
        true
    );

    wp_add_inline_script(
        'momsy-theme',
        'window.momsyConfig = ' . wp_json_encode(
            [
                'defaultTheme' => momsy_get_theme_mode(),
                'ajaxUrl'      => admin_url('admin-ajax.php'),
                'likeNonce'    => wp_create_nonce('momsy_like_post'),
                'homePostsNonce' => wp_create_nonce('momsy_home_posts'),
                'homeSearchNonce' => wp_create_nonce('momsy_home_search'),
                'labels'       => [
                    'copy'       => __('Paylaş', 'momsy'),
                    'copied'     => __('Kopyalandı', 'momsy'),
                    'like'       => __('Beğen', 'momsy'),
                    'liked'      => __('Beğenildi', 'momsy'),
                    'save'       => __('Kaydet', 'momsy'),
                    'saved'      => __('Kaydedildi', 'momsy'),
                    'themeDark'  => __('Koyu moda geç', 'momsy'),
                    'themeLight' => __('Açık moda geç', 'momsy'),
                    'loading'    => __('İçerikler yükleniyor...', 'momsy'),
                    'loadError'  => __('İçerikler yüklenirken bir sorun oluştu.', 'momsy'),
                    'searchOpen' => __('Aramayı aç', 'momsy'),
                    'searchClose'=> __('Aramayı kapat', 'momsy'),
                    'searching'  => __('Arama sonuçları getiriliyor...', 'momsy'),
                    'searchTitle'=> __('Yazılarda ara', 'momsy'),
                ],
            ]
        ) . ';',
        'before'
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'momsy_enqueue_assets');

function momsy_print_theme_bootstrap_script(): void
{
    $default_theme = momsy_get_theme_mode();
    ?>
    <script>
        (function () {
            var doc = document.documentElement;
            var storedTheme = null;
            var preferredTheme = <?php echo wp_json_encode($default_theme); ?>;
            doc.classList.add('momsy-ui-loading');

            try {
                storedTheme = window.localStorage.getItem('momsyTheme');
            } catch (error) {
                storedTheme = null;
            }

            if (storedTheme) {
                preferredTheme = storedTheme;
            }

            if (preferredTheme === 'system') {
                try {
                    preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                } catch (error) {
                    preferredTheme = 'dark';
                }
            }

            doc.setAttribute('data-theme', preferredTheme);
        }());
    </script>
    <?php
}
add_action('wp_head', 'momsy_print_theme_bootstrap_script', 0);

function momsy_resource_hints(array $urls, string $relation_type): array
{
    if ('preconnect' === $relation_type) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = [
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    }

    return $urls;
}
add_filter('wp_resource_hints', 'momsy_resource_hints', 10, 2);
