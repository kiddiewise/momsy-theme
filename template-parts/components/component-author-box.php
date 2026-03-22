<?php
$author_id    = (int) get_the_author_meta('ID');
$author_name  = get_the_author();
$author_url   = get_author_posts_url($author_id);
$author_bio   = get_the_author_meta('description', $author_id);
$author_posts = count_user_posts($author_id, 'post', true);
?>
<section class="author-box">
    <span class="section-kicker"><?php esc_html_e('Yazar', 'momsy'); ?></span>
    <div class="author-inline author-inline--feature">
        <?php echo get_avatar($author_id, 56); ?>
        <span class="author-inline__copy">
            <strong><?php echo esc_html($author_name); ?></strong>
            <span><?php echo esc_html($author_bio ? $author_bio : __('Momsy editör ekibi', 'momsy')); ?></span>
        </span>
    </div>

    <div class="author-box__meta">
        <span class="meta-pill meta-pill--ghost">
            <span class="meta-pill__icon"><?php momsy_the_icon('sparkles'); ?></span>
            <span><?php echo esc_html(number_format_i18n($author_posts)); ?> <?php esc_html_e('yazı', 'momsy'); ?></span>
        </span>
    </div>

    <a class="inline-link" href="<?php echo esc_url($author_url); ?>"><?php esc_html_e('Yazarın tüm içeriklerini gör', 'momsy'); ?></a>
</section>
