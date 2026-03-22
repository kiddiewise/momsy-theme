<?php get_header(); ?>
<main id="content" class="container page-shell section-space">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('page-card'); ?>>
            <header class="page-intro">
                <span class="section-kicker"><?php esc_html_e('Momsy Sayfası', 'momsy'); ?></span>
                <h1><?php echo esc_html(get_the_title()); ?></h1>
            </header>
            <div class="page-content article-content-card"><?php the_content(); ?></div>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
