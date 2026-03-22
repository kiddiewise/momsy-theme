<?php
$post_id   = get_the_ID();
$permalink = get_permalink($post_id);
$title     = get_the_title($post_id);
?>
<article <?php post_class('mini-post'); ?>>
    <a class="mini-post__media" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('momsy-mini', ['sizes' => '(max-width: 767px) 92px, 110px']); ?>
        <?php else : ?>
            <span class="media-placeholder media-placeholder--mini"><?php momsy_the_icon('sparkles'); ?></span>
        <?php endif; ?>
    </a>

    <div class="mini-post__content">
        <div class="meta-cluster"><?php momsy_post_category_badges($post_id, 1); ?></div>
        <h3 class="story-card__title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h3>
        <div class="post-stats">
            <span class="meta-pill meta-pill--ghost">
                <span class="meta-pill__icon"><?php momsy_the_icon('clock'); ?></span>
                <span><?php echo esc_html(momsy_reading_time($post_id)); ?></span>
            </span>
            <span class="meta-pill meta-pill--ghost">
                <span class="meta-pill__icon"><?php momsy_the_icon('eye'); ?></span>
                <span><?php echo esc_html(number_format_i18n(momsy_get_post_views($post_id))); ?></span>
            </span>
        </div>
    </div>
</article>
