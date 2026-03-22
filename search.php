<?php get_header(); ?>
<main id="content" class="container archive-shell section-space">
    <header class="page-intro page-intro--compact">
        <span class="section-kicker"><?php esc_html_e('Arama Sonuçları', 'momsy'); ?></span>
        <h1><?php printf(esc_html__('Arama: %s', 'momsy'), esc_html(get_search_query())); ?></h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="story-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/cards/card', 'post'); ?>
            <?php endwhile; ?>
        </div>
        <div class="pagination-shell">
            <?php the_posts_pagination(); ?>
        </div>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content', 'none'); ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
