<?php get_header(); ?>
<main id="content" class="container archive-shell section-space">
    <header class="page-intro page-intro--compact">
        <span class="section-kicker"><?php esc_html_e('Arşiv', 'momsy'); ?></span>
        <h1><?php the_archive_title(); ?></h1>
        <?php if (get_the_archive_description()) : ?>
            <div class="page-intro__description"><?php echo wp_kses_post(wpautop(get_the_archive_description())); ?></div>
        <?php endif; ?>
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
