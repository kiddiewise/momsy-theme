<?php get_header(); ?>
<main class="container page-shell section-space">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('page-card'); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="page-content"><?php the_content(); ?></div>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
