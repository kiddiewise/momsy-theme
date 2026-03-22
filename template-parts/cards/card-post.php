<article <?php post_class('story-card'); ?>>
    <a class="story-card__image" href="<?php the_permalink(); ?>">
        <?php if (has_post_thumbnail()) : the_post_thumbnail('momsy-card'); endif; ?>
    </a>

    <div class="story-card__content">
        <div class="meta-cluster">
            <?php momsy_post_category_badges(); ?>
        </div>

        <h3 class="story-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>

        <div class="post-stats">
            <span class="meta-pill"><?php echo esc_html(momsy_reading_time()); ?></span>
            <span class="meta-pill"><?php echo esc_html(get_the_date('d M Y')); ?></span>
        </div>
    </div>
</article>
