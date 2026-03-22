<?php get_header(); ?>
<main class="container archive-shell section-space">
    <header class="archive-header">
        <h1><?php the_archive_title(); ?></h1>
        <p><?php the_archive_description(); ?></p>
    </header>

    <?php if (have_posts()) : ?>
        <div class="story-grid">
            <?php while (have_posts()) : the_post();
                get_template_part('template-parts/cards/card', 'post');
            endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content', 'none'); ?>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
