<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); momsy_track_post_view(get_the_ID()); ?>
<main class="single-shell">
    <article <?php post_class('article-page'); ?>>
        <header class="article-hero container">
            <div class="article-topbar">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="back-chip">← <?php esc_html_e('Geri', 'momsy'); ?></a>
                <div class="article-author">
                    <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                </div>
            </div>

            <div class="article-meta-line">
                <span><?php echo esc_html(get_the_date()); ?></span>
                <span>•</span>
                <span><?php echo esc_html(momsy_reading_time()); ?></span>
            </div>

            <h1 class="article-title"><?php the_title(); ?></h1>

            <div class="article-author-row">
                <div class="author-inline">
                    <?php echo get_avatar(get_the_author_meta('ID'), 44); ?>
                    <div>
                        <strong><?php the_author(); ?></strong>
                        <span><?php esc_html_e('Momsy yazarı', 'momsy'); ?></span>
                    </div>
                </div>
                <button class="share-chip" type="button" data-share-post="<?php the_permalink(); ?>">
                    <?php esc_html_e('Paylaş', 'momsy'); ?>
                </button>
            </div>

            <?php if (has_post_thumbnail()) : ?>
                <figure class="article-cover">
                    <?php the_post_thumbnail('full'); ?>
                </figure>
            <?php endif; ?>

            <div class="article-stats-card">
                <?php momsy_post_stats(true); ?>
            </div>
        </header>

        <div class="article-content-wrap container">
            <div class="article-content-card">
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
            </div>

            <aside class="article-aside">
                <?php get_template_part('template-parts/components/component', 'author-box'); ?>
                <?php get_template_part('template-parts/components/component', 'related-posts'); ?>
            </aside>
        </div>
    </article>

    <div class="container">
        <?php comments_template(); ?>
    </div>
</main>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
