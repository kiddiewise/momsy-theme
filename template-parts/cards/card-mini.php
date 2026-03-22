<article <?php post_class('mini-post'); ?>>
    <a href="<?php the_permalink(); ?>">
        <?php if (has_post_thumbnail()) : the_post_thumbnail('momsy-mini'); endif; ?>
    </a>
    <div>
        <div class="meta-cluster"><?php momsy_post_category_badges(); ?></div>
        <h3 class="story-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="post-stats">
            <span class="meta-pill"><?php echo esc_html(momsy_reading_time()); ?></span>
        </div>
    </div>
</article>
