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

    wp_enqueue_style(
        'momsy-music-player',
        MOMSY_URI . '/assets/css/music-player.css',
        ['momsy-main'],
        momsy_get_asset_version('assets/css/music-player.css')
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
                'labels'       => [
                    'copy'       => __('Paylaş', 'momsy'),
                    'copied'     => __('Kopyalandı', 'momsy'),
                    'like'       => __('Beğen', 'momsy'),
                    'liked'      => __('Beğenildi', 'momsy'),
                    'save'       => __('Kaydet', 'momsy'),
                    'saved'      => __('Kaydedildi', 'momsy'),
                    'themeDark'  => __('Koyu moda geç', 'momsy'),
                    'themeLight' => __('Açık moda geç', 'momsy'),
                ],
            ]
        ) . ';',
        'before'
    );

    wp_enqueue_script(
        'momsy-music-player',
        MOMSY_URI . '/assets/js/music-player.js',
        [],
        momsy_get_asset_version('assets/js/music-player.js'),
        [
            'strategy'  => 'defer',
            'in_footer' => true,
        ]
    );

    wp_add_inline_script(
        'momsy-music-player',
        'window.momsyMusicConfig = ' . wp_json_encode(
            [
                'audioUrl'         => 'https://blog.momsy.com.tr/wp-content/uploads/2026/05/sakinlestirici-muzik.mp3',
                'homeUrl'          => home_url('/'),
                'contentSelectors' => ['main#content', 'main', '#primary', '.site-main', '.content-area'],
                'labels'           => [
                    'play'       => __('Rahatlatıcı müziği başlat', 'momsy'),
                    'pause'      => __('Rahatlatıcı müziği durdur', 'momsy'),
                    'mute'       => __('Sesi kapat', 'momsy'),
                    'unmute'     => __('Sesi aç', 'momsy'),
                    'volume'     => __('Müzik ses seviyesi', 'momsy'),
                    'playing'    => __('Müzik çalıyor', 'momsy'),
                    'paused'     => __('Müzik duraklatıldı', 'momsy'),
                    'muted'      => __('Ses kapalı', 'momsy'),
                    'unmuted'    => __('Ses açık', 'momsy'),
                    'loading'    => __('Sayfa yükleniyor', 'momsy'),
                    'loadFailed' => __('Sayfa normal şekilde açılıyor', 'momsy'),
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

function momsy_print_music_player_markup(): void
{
    ?>
    <div class="momsy-music-player" data-momsy-music-player data-state="idle">
        <button class="momsy-music-player__button momsy-music-player__button--play" type="button" data-momsy-music-toggle aria-label="<?php esc_attr_e('Rahatlatıcı müziği başlat', 'momsy'); ?>" aria-pressed="false">
            <span class="momsy-music-player__icon momsy-music-player__icon--play" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false">
                    <path d="M12 4v10.5A3.2 3.2 0 1 1 10 11.6V7h8V4h-6Z"></path>
                </svg>
            </span>
            <span class="momsy-music-player__icon momsy-music-player__icon--pause" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false">
                    <path d="M7 5h4v14H7V5Zm6 0h4v14h-4V5Z"></path>
                </svg>
            </span>
        </button>

        <div class="momsy-music-player__extras" aria-label="<?php esc_attr_e('Müzik kontrolleri', 'momsy'); ?>">
            <button class="momsy-music-player__button momsy-music-player__button--mute" type="button" data-momsy-music-mute aria-label="<?php esc_attr_e('Sesi kapat', 'momsy'); ?>" aria-pressed="false">
                <span class="momsy-music-player__icon momsy-music-player__icon--volume" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M4 9v6h4l5 4V5L8 9H4Zm12.4-.3 1.4-1.4A6.7 6.7 0 0 1 20 12a6.7 6.7 0 0 1-2.2 4.7l-1.4-1.4A4.8 4.8 0 0 0 18 12a4.8 4.8 0 0 0-1.6-3.3Z"></path>
                    </svg>
                </span>
                <span class="momsy-music-player__icon momsy-music-player__icon--muted" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M4 9v6h4l5 4V5L8 9H4Zm12.7 1.3 1.8-1.8 1.2 1.2-1.8 1.8 1.8 1.8-1.2 1.2-1.8-1.8-1.8 1.8-1.2-1.2 1.8-1.8-1.8-1.8 1.2-1.2 1.8 1.8Z"></path>
                    </svg>
                </span>
            </button>
            <label class="momsy-music-player__volume">
                <span class="screen-reader-text"><?php esc_html_e('Müzik ses seviyesi', 'momsy'); ?></span>
                <input type="range" min="0" max="1" step="0.05" value="0.45" data-momsy-music-volume aria-label="<?php esc_attr_e('Müzik ses seviyesi', 'momsy'); ?>">
            </label>
        </div>

        <span class="screen-reader-text" data-momsy-music-status aria-live="polite"><?php esc_html_e('Müzik duraklatıldı', 'momsy'); ?></span>
    </div>
    <?php
}
add_action('wp_footer', 'momsy_print_music_player_markup', 20);

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
