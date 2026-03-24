<?php
get_header();

$home_title    = get_theme_mod('momsy_home_title', __('Momsy Blog', 'momsy'));
$home_desc     = get_theme_mod('momsy_home_desc', __('Uzman görüşleri, anne deneyimleri ve topluluk odaklı içerikler tek yerde.', 'momsy'));
$hero_post     = momsy_get_home_featured_post();
$hero_id       = $hero_post instanceof WP_Post ? (int) $hero_post->ID : 0;
$feed_query    = momsy_get_home_feed_query(1, 0, $hero_id > 0 ? [$hero_id] : []);
$categories    = momsy_get_home_categories();
?>

<main id="content" class="momsy-app-shell">
    <section class="app-dashboard container">
        <div
            class="app-shell app-shell--blog"
            data-home-feed
            data-page="1"
            data-category="0"
            data-max-pages="<?php echo esc_attr((string) max(1, (int) $feed_query->max_num_pages)); ?>"
            data-hero-id="<?php echo esc_attr((string) $hero_id); ?>"
        >
            <header class="app-shell__masthead">
                <div class="app-shell__headline">
                    <h1 class="app-shell__title"><?php echo esc_html($home_title); ?></h1>
                    <p class="app-shell__intro"><?php echo esc_html($home_desc); ?></p>
                </div>
            </header>

            <?php if ($hero_post instanceof WP_Post) : ?>
                <section class="app-section app-section--featured" aria-label="<?php esc_attr_e('Öne çıkan içerik', 'momsy'); ?>">
                    <?php momsy_render_home_featured_card($hero_post); ?>
                </section>
            <?php endif; ?>

            <section class="app-section app-section--latest" aria-labelledby="momsy-latest-posts-title">
                <div class="app-section__header app-section__header--stack">
                    <span class="app-section__eyebrow"><?php esc_html_e('Son içerikler', 'momsy'); ?></span>
                    <h2 id="momsy-latest-posts-title"><?php esc_html_e('Son paylaşımlar', 'momsy'); ?></h2>
                </div>

                <div id="momsy-home-categories" class="app-category-tabs" role="tablist" aria-label="<?php esc_attr_e('İçerik kategorileri', 'momsy'); ?>">
                    <button class="app-category-tab is-active" type="button" role="tab" aria-selected="true" data-home-category="0">
                        <?php esc_html_e('Tümü', 'momsy'); ?>
                    </button>

                    <?php foreach ($categories as $category) : ?>
                        <?php if (! $category instanceof WP_Term) : ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <button
                            class="app-category-tab"
                            type="button"
                            role="tab"
                            aria-selected="false"
                            data-home-category="<?php echo esc_attr((string) $category->term_id); ?>"
                        >
                            <?php echo esc_html($category->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="home-feed-status" data-home-feed-status hidden aria-live="polite"></div>

                <div class="home-feed-list" data-home-feed-list>
                    <?php echo momsy_render_home_feed_cards($feed_query); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <button class="button-secondary home-feed-more" type="button" data-home-feed-more hidden>
                    <?php esc_html_e('Daha fazla yükle', 'momsy'); ?>
                </button>

                <div class="home-feed-sentinel" data-home-feed-sentinel aria-hidden="true"></div>
            </section>
        </div>
    </section>

    <div class="home-search-sheet" data-home-search-panel hidden>
        <div class="home-search-sheet__backdrop" data-home-search-close></div>

        <section class="home-search-sheet__dialog" aria-label="<?php esc_attr_e('Yazılarda ara', 'momsy'); ?>">
            <div class="home-search-sheet__header">
                <div>
                    <span class="app-section__eyebrow"><?php esc_html_e('Hızlı arama', 'momsy'); ?></span>
                    <h2><?php esc_html_e('Yazılarda ara', 'momsy'); ?></h2>
                </div>

                <button class="home-search-sheet__close" type="button" data-home-search-close aria-label="<?php esc_attr_e('Aramayı kapat', 'momsy'); ?>">
                    <?php momsy_the_icon('arrow-left'); ?>
                </button>
            </div>

            <form class="home-search-form" data-home-search-form>
                <label class="screen-reader-text" for="momsy-home-search-input"><?php esc_html_e('İçeriklerde ara', 'momsy'); ?></label>
                <input
                    id="momsy-home-search-input"
                    type="search"
                    name="s"
                    autocomplete="off"
                    placeholder="<?php esc_attr_e('Konu, başlık veya anahtar kelime ara', 'momsy'); ?>"
                    data-home-search-input
                >
                <button class="app-search__button" type="submit" aria-label="<?php esc_attr_e('Ara', 'momsy'); ?>">
                    <?php momsy_the_icon('search'); ?>
                </button>
            </form>

            <div class="home-search-results" data-home-search-results>
                <?php echo momsy_render_home_search_empty_state(__('Aramak istediğiniz konuyu yazın.', 'momsy')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </section>
    </div>

    <nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e('Mobil alt menü', 'momsy'); ?>">
        <a class="mobile-bottom-nav__item is-active" href="<?php echo esc_url(home_url('/')); ?>" data-home-nav-item="home">
            <span class="mobile-bottom-nav__icon"><?php momsy_the_icon('home'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Anasayfa', 'momsy'); ?></span>
        </a>

        <button class="mobile-bottom-nav__item" type="button" data-home-open-categories data-home-nav-item="categories">
            <span class="mobile-bottom-nav__icon"><?php momsy_the_icon('grid'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Kategoriler', 'momsy'); ?></span>
        </button>

        <button class="mobile-bottom-nav__item" type="button" data-home-search-toggle aria-controls="momsy-home-search-input" aria-expanded="false" data-home-nav-item="search">
            <span class="mobile-bottom-nav__icon"><?php momsy_the_icon('search'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Ara', 'momsy'); ?></span>
        </button>

        <button class="mobile-bottom-nav__item theme-toggle" type="button" data-theme-toggle data-home-nav-item="theme" aria-label="<?php esc_attr_e('Tema değiştir', 'momsy'); ?>">
            <span class="mobile-bottom-nav__icon icon-button__icon icon-button__icon--sun"><?php momsy_the_icon('sun'); ?></span>
            <span class="mobile-bottom-nav__icon icon-button__icon icon-button__icon--moon"><?php momsy_the_icon('moon'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Tema', 'momsy'); ?></span>
        </button>
    </nav>
</main>

<?php get_footer(); ?>
