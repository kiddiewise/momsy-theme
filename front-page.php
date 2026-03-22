<?php get_header(); ?>

<main class="momsy-app-shell">
    <section class="momsy-hero container">
        <div class="hero-copy">
            <span class="eyebrow"><?php esc_html_e('Momsy Blog', 'momsy'); ?></span>
            <h1><?php echo esc_html(get_theme_mod('momsy_home_title', __('Anne ve anne adayları için güvenilir içerikler', 'momsy'))); ?></h1>
            <p><?php echo esc_html(get_theme_mod('momsy_home_desc', __('Uzman görüşleri, anne deneyimleri ve topluluk odaklı içerikler tek yerde.', 'momsy'))); ?></p>
        </div>

        <?php
        $featured = new WP_Query([
            'posts_per_page'      => 1,
            'ignore_sticky_posts' => true,
            'meta_key'            => '_thumbnail_id',
        ]);
        if ($featured->have_posts()) :
            while ($featured->have_posts()) : $featured->the_post(); ?>
                <article class="hero-card">
                    <a href="<?php the_permalink(); ?>" class="hero-card__media">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', ['loading' => 'eager']); ?>
                        <?php endif; ?>
                    </a>
                    <div class="hero-card__body">
                        <div class="hero-card__meta">
                            <?php momsy_post_category_badges(); ?>
                            <span><?php echo esc_html(momsy_reading_time()); ?></span>
                        </div>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="post-stats">
                            <?php momsy_post_stats(); ?>
                        </div>
                    </div>
                </article>
            <?php endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </section>

    <section class="container section-space">
        <?php
        wp_nav_menu([
            'theme_location' => 'home_categories',
            'container'      => 'nav',
            'container_class'=> 'pill-nav',
            'fallback_cb'    => false,
        ]);
        ?>
    </section>

    <section class="container section-space">
        <div class="section-header">
            <h2><?php esc_html_e('Bugün öne çıkanlar', 'momsy'); ?></h2>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"><?php esc_html_e('Tümünü gör', 'momsy'); ?></a>
        </div>

        <div class="story-grid">
            <?php
            $grid = new WP_Query([
                'posts_per_page'      => 6,
                'ignore_sticky_posts' => true,
            ]);
            if ($grid->have_posts()) :
                while ($grid->have_posts()) : $grid->the_post();
                    get_template_part('template-parts/cards/card', 'post');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>

    <section class="container section-space">
        <div class="section-header">
            <h2><?php esc_html_e('En çok okunanlar', 'momsy'); ?></h2>
        </div>

        <div class="mini-post-list">
            <?php
            $popular = new WP_Query([
                'posts_per_page' => 5,
                'meta_key'       => 'momsy_views',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
            ]);
            if ($popular->have_posts()) :
                while ($popular->have_posts()) : $popular->the_post();
                    get_template_part('template-parts/cards/card', 'mini');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
