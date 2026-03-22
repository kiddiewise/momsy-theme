<?php
$post_id   = get_the_ID();
$permalink = get_permalink($post_id);
$title     = get_the_title($post_id);
$author_id = (int) get_the_author_meta('ID');
?>
<article <?php post_class('story-card'); ?>>
    <a class="story-card__image" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('momsy-card', ['sizes' => '(max-width: 767px) 100vw, (max-width: 1099px) 50vw, 32vw']); ?>
        <?php else : ?>
            <span class="media-placeholder"><?php momsy_the_icon('sparkles'); ?></span>
        <?php endif; ?>
    </a>

    <div class="story-card__content">
        <div class="meta-cluster">
            <?php momsy_post_category_badges($post_id, 2); ?>
        </div>

        <h3 class="story-card__title">
            <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
        </h3>

        <p><?php echo esc_html(momsy_get_post_card_excerpt($post_id, 20)); ?></p>

        <div class="story-card__footer">
            <div class="author-inline author-inline--compact">
                <?php echo get_avatar($author_id, 32); ?>
                <span class="author-inline__copy">
                    <strong><?php the_author(); ?></strong>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d M Y')); ?></time>
                </span>
            </div>

            <div class="post-stats">
                <span class="meta-pill meta-pill--ghost">
                    <span class="meta-pill__icon"><?php momsy_the_icon('clock'); ?></span>
                    <span><?php echo esc_html(momsy_reading_time($post_id)); ?></span>
                </span>
            </div>
        </div>
    </div>
</article>
