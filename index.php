<?php get_header(); ?>
<main class="container archive-shell section-space">
    <?php if (have_posts()) : ?>
        <header class="archive-header">
            <h1><?php bloginfo('name'); ?></h1>
            <p><?php bloginfo('description'); ?></p>
        </header>

        <div class="story-grid">
            <?php while (have_posts()) : the_post();
                get_template_part('template-parts/cards/card', 'post');
            endwhile; ?>
        </div>

        <?php the_posts_pagination([
            'mid_size'  => 1,
            'prev_text' => __('Önceki', 'momsy'),
            'next_text' => __('Sonraki', 'momsy'),
        ]); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content', 'none'); ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
