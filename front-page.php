<?php
get_header();

$home_title    = get_theme_mod('momsy_home_title', __('Momsy Blog', 'momsy'));
$home_desc     = get_theme_mod('momsy_home_desc', __('Uzman görüşleri, anne deneyimleri ve topluluk odaklı içerikler tek yerde.', 'momsy'));
$site_icon_url = get_site_icon_url(96);
$today_label   = wp_date('d M, Y');
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
                <div class="app-shell__masthead-row">
                    <a class="app-avatar" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                        <?php if ($site_icon_url) : ?>
                            <img src="<?php echo esc_url($site_icon_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                        <?php else : ?>
                            <span>M</span>
                        <?php endif; ?>
                    </a>

                    <p class="app-shell__date"><?php echo esc_html($today_label); ?></p>

                    <button
                        class="app-search-toggle"
                        type="button"
                        data-home-search-toggle
                        aria-controls="momsy-home-search-panel"
                        aria-expanded="false"
                        aria-label="<?php esc_attr_e('Aramayı aç', 'momsy'); ?>"
                    >
                        <?php momsy_the_icon('search'); ?>
                    </button>
                </div>

                <div class="app-shell__headline">
                    <h1 class="app-shell__title"><?php echo esc_html($home_title); ?></h1>
                    <p class="app-shell__intro"><?php echo esc_html($home_desc); ?></p>
                </div>
            </header>

            <form role="search" method="get" class="app-search app-search--panel" action="<?php echo esc_url(home_url('/')); ?>" id="momsy-home-search-panel" hidden>
                <label class="screen-reader-text" for="momsy-home-search"><?php esc_html_e('İçeriklerde ara', 'momsy'); ?></label>
                <input id="momsy-home-search" type="search" name="s" placeholder="<?php esc_attr_e('İçeriklerde ara', 'momsy'); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
                <button class="app-search__button" type="submit" aria-label="<?php esc_attr_e('Ara', 'momsy'); ?>">
                    <?php momsy_the_icon('search'); ?>
                </button>
            </form>

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

                <div class="app-category-tabs" role="tablist" aria-label="<?php esc_attr_e('İçerik kategorileri', 'momsy'); ?>">
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
</main>

<?php get_footer(); ?>
