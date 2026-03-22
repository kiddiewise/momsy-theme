<?php get_header(); ?>
<main id="content" class="container archive-shell section-space">
    <header class="page-intro page-intro--compact">
        <span class="section-kicker"><?php esc_html_e('Momsy Akışı', 'momsy'); ?></span>
        <h1><?php bloginfo('name'); ?></h1>
        <p><?php bloginfo('description'); ?></p>
    </header>

    <?php if (have_posts()) : ?>
        <div class="story-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/cards/card', 'post'); ?>
            <?php endwhile; ?>
        </div>

        <div class="pagination-shell">
            <?php
            the_posts_pagination([
                'mid_size'  => 1,
                'prev_text' => __('Önceki', 'momsy'),
                'next_text' => __('Sonraki', 'momsy'),
            ]);
            ?>
        </div>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content', 'none'); ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
