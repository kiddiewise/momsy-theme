<?php
get_header();

$home_badge    = get_theme_mod('momsy_home_badge', __('Momsy Blog', 'momsy'));
$home_title    = get_theme_mod('momsy_home_title', __('Anne ve anne adayları için güvenilir içerikler', 'momsy'));
$home_desc     = get_theme_mod('momsy_home_desc', __('Uzman görüşleri, anne deneyimleri ve topluluk odaklı içerikler tek yerde.', 'momsy'));
$site_icon_url = get_site_icon_url(96);

$featured_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 4,
    'ignore_sticky_posts'    => true,
    'meta_query'             => [
        [
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS',
        ],
    ],
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
]);

$featured_ids = wp_list_pluck($featured_query->posts, 'ID');
$featured_posts = $featured_query->posts;
$hero_post      = ! empty($featured_posts) ? array_shift($featured_posts) : null;

$feed_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 5,
    'post__not_in'           => $featured_ids,
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
]);

$popular_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 4,
    'post__not_in'           => $featured_ids,
    'meta_key'               => 'momsy_views',
    'orderby'                => 'meta_value_num',
    'order'                  => 'DESC',
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
]);
?>

<main id="content" class="momsy-app-shell">
    <section class="app-dashboard container">
        <div class="app-shell">
            <header class="app-shell__top">
                <div class="app-shell__copy">
                    <span class="app-greeting"><?php echo esc_html($home_badge); ?></span>
                    <h1><?php echo esc_html($home_title); ?></h1>
                </div>

                <a class="app-avatar" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <?php if ($site_icon_url) : ?>
                        <img src="<?php echo esc_url($site_icon_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <?php else : ?>
                        <span>M</span>
                    <?php endif; ?>
                </a>
            </header>

            <p class="app-shell__intro"><?php echo esc_html($home_desc); ?></p>

            <form role="search" method="get" class="app-search" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="momsy-home-search"><?php esc_html_e('İçeriklerde ara', 'momsy'); ?></label>
                <input id="momsy-home-search" type="search" name="s" placeholder="<?php esc_attr_e('İçeriklerde ara', 'momsy'); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
                <button class="app-search__button" type="submit" aria-label="<?php esc_attr_e('Ara', 'momsy'); ?>">
                    <?php momsy_the_icon('search'); ?>
                </button>
            </form>

            <?php if ($hero_post instanceof WP_Post) : ?>
                <section class="dashboard-section dashboard-section--spotlight">
                    <div class="dashboard-section__head">
                        <div>
                            <span class="section-kicker"><?php esc_html_e('Öne Çıkanlar', 'momsy'); ?></span>
                            <h2><?php esc_html_e('Bugünün vitrini', 'momsy'); ?></h2>
                        </div>
                        <a class="inline-link" href="<?php echo esc_url(momsy_get_posts_page_url()); ?>"><?php esc_html_e('Tümünü gör', 'momsy'); ?></a>
                    </div>

                    <?php
                    $post = $hero_post;
                    setup_postdata($post);
                    ?>
                    <article class="spotlight-card">
                        <a class="spotlight-card__media" href="<?php echo esc_url(get_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('momsy-hero', ['sizes' => '(max-width: 767px) 100vw, 72rem']); ?>
                            <?php else : ?>
                                <span class="media-placeholder media-placeholder--hero"><?php momsy_the_icon('sparkles'); ?></span>
                            <?php endif; ?>
                            <span class="spotlight-card__overlay"></span>
                        </a>

                        <div class="spotlight-card__body">
                            <div class="meta-cluster"><?php momsy_post_category_badges(get_the_ID(), 2); ?></div>
                            <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                            <p><?php echo esc_html(momsy_get_post_card_excerpt(get_the_ID(), 26)); ?></p>
                            <div class="spotlight-card__meta">
                                <span class="meta-inline"><?php momsy_the_icon('clock'); ?><?php echo esc_html(momsy_reading_time()); ?></span>
                                <span class="meta-inline"><?php momsy_the_icon('eye'); ?><?php echo esc_html(number_format_i18n(momsy_get_post_views(get_the_ID()))); ?></span>
                            </div>
                        </div>
                    </article>
                    <?php wp_reset_postdata(); ?>

                    <?php if (! empty($featured_posts)) : ?>
                        <div class="feature-scroll" aria-label="<?php esc_attr_e('Diğer öne çıkan içerikler', 'momsy'); ?>">
                            <?php foreach ($featured_posts as $featured_post) : ?>
                                <?php
                                $post = $featured_post;
                                setup_postdata($post);
                                ?>
                                <article class="feature-tile">
                                    <a class="feature-tile__media" href="<?php echo esc_url(get_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('momsy-card', ['sizes' => '(max-width: 767px) 76vw, 18rem']); ?>
                                        <?php else : ?>
                                            <span class="media-placeholder media-placeholder--feature"><?php momsy_the_icon('sparkles'); ?></span>
                                        <?php endif; ?>
                                        <span class="feature-tile__overlay"></span>
                                        <span class="feature-tile__content">
                                            <span class="feature-tile__meta"><?php momsy_post_category_badges(get_the_ID(), 1); ?></span>
                                            <strong><?php echo esc_html(get_the_title()); ?></strong>
                                        </span>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php else : ?>
                <section class="dashboard-section">
                    <?php get_template_part('template-parts/content/content', 'none'); ?>
                </section>
            <?php endif; ?>

            <?php if (has_nav_menu('home_categories')) : ?>
                <section id="topics" class="dashboard-section">
                    <div class="dashboard-section__head">
                        <div>
                            <span class="section-kicker"><?php esc_html_e('Kategoriler', 'momsy'); ?></span>
                            <h2><?php esc_html_e('Konular', 'momsy'); ?></h2>
                        </div>
                    </div>

                    <?php
                    wp_nav_menu([
                        'theme_location' => 'home_categories',
                        'container'      => 'nav',
                        'container_class'=> 'pill-nav pill-nav--app',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </section>
            <?php endif; ?>

            <section class="dashboard-grid">
                <div class="dashboard-panel">
                    <div class="dashboard-section__head">
                        <div>
                            <span class="section-kicker"><?php esc_html_e('Son Yazılar', 'momsy'); ?></span>
                            <h2><?php esc_html_e('Son içerikler', 'momsy'); ?></h2>
                        </div>
                        <a class="inline-link" href="<?php echo esc_url(momsy_get_posts_page_url()); ?>"><?php esc_html_e('Daha fazla', 'momsy'); ?></a>
                    </div>

                    <?php if ($feed_query->have_posts()) : ?>
                        <div class="dashboard-story-list">
                            <?php while ($feed_query->have_posts()) : $feed_query->the_post(); ?>
                                <article class="dashboard-story">
                                    <a class="dashboard-story__media" href="<?php echo esc_url(get_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('momsy-mini', ['sizes' => '(max-width: 767px) 96px, 120px']); ?>
                                        <?php else : ?>
                                            <span class="media-placeholder media-placeholder--mini"><?php momsy_the_icon('sparkles'); ?></span>
                                        <?php endif; ?>
                                    </a>

                                    <div class="dashboard-story__body">
                                        <div class="meta-cluster"><?php momsy_post_category_badges(get_the_ID(), 1); ?></div>
                                        <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                                        <div class="dashboard-story__meta">
                                            <span class="meta-inline"><?php momsy_the_icon('clock'); ?><?php echo esc_html(momsy_reading_time()); ?></span>
                                            <span class="meta-inline"><?php momsy_the_icon('calendar'); ?><?php echo esc_html(get_the_date('d M')); ?></span>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <?php get_template_part('template-parts/content/content', 'none'); ?>
                    <?php endif; ?>
                </div>

                <div class="dashboard-panel dashboard-panel--aside">
                    <div class="dashboard-section__head">
                        <div>
                            <span class="section-kicker"><?php esc_html_e('Şimdi Popüler', 'momsy'); ?></span>
                            <h2><?php esc_html_e('Çok okunanlar', 'momsy'); ?></h2>
                        </div>
                    </div>

                    <?php if ($popular_query->have_posts()) : ?>
                        <div class="mini-post-list mini-post-list--dashboard">
                            <?php while ($popular_query->have_posts()) : $popular_query->the_post(); ?>
                                <?php get_template_part('template-parts/cards/card', 'mini'); ?>
                            <?php endwhile; ?>
                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <?php get_template_part('template-parts/content/content', 'none'); ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </section>
</main>

<?php get_footer(); ?>
