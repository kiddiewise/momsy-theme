<?php
get_header();

$home_badge = get_theme_mod('momsy_home_badge', __('Momsy Blog', 'momsy'));
$home_title = get_theme_mod('momsy_home_title', __('Anne ve anne adayları için güvenilir içerikler', 'momsy'));
$home_desc  = get_theme_mod('momsy_home_desc', __('Uzman görüşleri, anne deneyimleri ve topluluk odaklı içerikler tek yerde.', 'momsy'));

$featured_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 1,
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

$featured_post_id = 0;

if ($featured_query->have_posts()) {
    $featured_post_id = (int) $featured_query->posts[0]->ID;
}

$feed_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 6,
    'post__not_in'           => array_filter([$featured_post_id]),
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
]);

$popular_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 4,
    'post__not_in'           => array_filter([$featured_post_id]),
    'meta_key'               => 'momsy_views',
    'orderby'                => 'meta_value_num',
    'order'                  => 'DESC',
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
]);
?>

<main id="content" class="momsy-app-shell">
    <section class="home-hero container">
        <div class="hero-copy">
            <span class="eyebrow"><?php echo esc_html($home_badge); ?></span>
            <h1><?php echo esc_html($home_title); ?></h1>
            <p><?php echo esc_html($home_desc); ?></p>

            <div class="hero-highlights">
                <span class="feature-pill"><?php momsy_the_icon('sparkles'); ?><?php esc_html_e('Uzman görüşleri', 'momsy'); ?></span>
                <span class="feature-pill"><?php momsy_the_icon('heart'); ?><?php esc_html_e('Gerçek anne deneyimleri', 'momsy'); ?></span>
                <span class="feature-pill"><?php momsy_the_icon('chat'); ?><?php esc_html_e('Topluluk odaklı içerikler', 'momsy'); ?></span>
            </div>

            <div class="hero-actions">
                <a class="button-primary" href="<?php echo esc_url(momsy_get_posts_page_url()); ?>"><?php esc_html_e('Son yazıları keşfet', 'momsy'); ?></a>
                <?php if (has_nav_menu('home_categories')) : ?>
                    <a class="button-secondary" href="#topics"><?php esc_html_e('Konulara göz at', 'momsy'); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero-feature">
            <?php if ($featured_query->have_posts()) : ?>
                <?php while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                    <article class="hero-card">
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="hero-card__media" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('momsy-hero', ['loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 767px) 100vw, 44vw']); ?>
                            <?php else : ?>
                                <span class="media-placeholder media-placeholder--hero"><?php momsy_the_icon('sparkles'); ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="hero-card__body">
                            <div class="hero-card__meta">
                                <?php momsy_post_category_badges(get_the_ID(), 2); ?>
                                <span class="meta-inline"><?php momsy_the_icon('clock'); ?><?php echo esc_html(momsy_reading_time()); ?></span>
                            </div>

                            <h2><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h2>
                            <p><?php echo esc_html(momsy_get_post_card_excerpt(get_the_ID(), 24)); ?></p>

                            <div class="post-stats">
                                <?php momsy_post_stats(); ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <section class="empty-card">
                    <h2><?php esc_html_e('İlk içerik burada parlayacak', 'momsy'); ?></h2>
                    <p><?php esc_html_e('Öne çıkarılmış içerik kartı için ilk yazınızı yayınlayın.', 'momsy'); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </section>

    <?php if (has_nav_menu('home_categories')) : ?>
        <section id="topics" class="container section-space">
            <?php
            wp_nav_menu([
                'theme_location' => 'home_categories',
                'container'      => 'nav',
                'container_class'=> 'pill-nav',
                'fallback_cb'    => false,
            ]);
            ?>
        </section>
    <?php endif; ?>

    <section class="container section-space home-feed">
        <div class="section-header section-header--stack">
            <div>
                <span class="section-kicker"><?php esc_html_e('Editörden Seçtik', 'momsy'); ?></span>
                <h2><?php esc_html_e('Bugün Momsy’de', 'momsy'); ?></h2>
                <p><?php esc_html_e('Uygulamanın içerik akışı gibi temiz, hızlı ve dikkatlice kürat edilmiş yazılar.', 'momsy'); ?></p>
            </div>
            <a class="inline-link" href="<?php echo esc_url(momsy_get_posts_page_url()); ?>"><?php esc_html_e('Tüm yazılar', 'momsy'); ?></a>
        </div>

        <?php if ($feed_query->have_posts()) : ?>
            <div class="story-grid">
                <?php while ($feed_query->have_posts()) : $feed_query->the_post(); ?>
                    <?php get_template_part('template-parts/cards/card', 'post'); ?>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content/content', 'none'); ?>
        <?php endif; ?>
    </section>

    <section class="container section-space home-columns">
        <div class="home-panel home-panel--community">
            <div class="section-header section-header--stack">
                <div>
                    <span class="section-kicker"><?php esc_html_e('Topluluk Ritmi', 'momsy'); ?></span>
                    <h2><?php esc_html_e('App içi içerik hissi', 'momsy'); ?></h2>
                </div>
            </div>

            <div class="community-list">
                <article class="community-card">
                    <span class="community-card__icon"><?php momsy_the_icon('sparkles'); ?></span>
                    <h3><?php esc_html_e('Sakin ve güven veren akış', 'momsy'); ?></h3>
                    <p><?php esc_html_e('Okunabilir tipografi, yumuşak geçişler ve premium kart yüzeyleri ile tasarlandı.', 'momsy'); ?></p>
                </article>
                <article class="community-card">
                    <span class="community-card__icon"><?php momsy_the_icon('heart'); ?></span>
                    <h3><?php esc_html_e('Anne odaklı ama klişesiz', 'momsy'); ?></h3>
                    <p><?php esc_html_e('Sıcak, profesyonel ve modern görsel dil Momsy uygulama dünyasıyla uyumlu ilerliyor.', 'momsy'); ?></p>
                </article>
                <article class="community-card">
                    <span class="community-card__icon"><?php momsy_the_icon('chat'); ?></span>
                    <h3><?php esc_html_e('Topluluk hissi güçlü', 'momsy'); ?></h3>
                    <p><?php esc_html_e('Yorumlar, yazar kutuları ve ilgili içerikler birlikte daha zengin bir içerik ekranı kuruyor.', 'momsy'); ?></p>
                </article>
            </div>
        </div>

        <div class="home-panel home-panel--popular">
            <div class="section-header section-header--stack">
                <div>
                    <span class="section-kicker"><?php esc_html_e('Çok Okunanlar', 'momsy'); ?></span>
                    <h2><?php esc_html_e('Şu anda öne çıkan yazılar', 'momsy'); ?></h2>
                </div>
            </div>

            <?php if ($popular_query->have_posts()) : ?>
                <div class="mini-post-list">
                    <?php while ($popular_query->have_posts()) : $popular_query->the_post(); ?>
                        <?php get_template_part('template-parts/cards/card', 'mini'); ?>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <section class="empty-card">
                    <h3><?php esc_html_e('Henüz popüler içerik yok', 'momsy'); ?></h3>
                    <p><?php esc_html_e('Okunma verileri biriktikçe bu alan otomatik olarak dolacak.', 'momsy'); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
