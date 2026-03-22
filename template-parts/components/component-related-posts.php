<?php
$post_id       = get_the_ID();
$category_ids  = wp_get_post_categories($post_id);
$related_query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 3,
    'post__not_in'           => [$post_id],
    'category__in'           => $category_ids,
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
]);

if (! $related_query->have_posts()) {
    $related_query = new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 3,
        'post__not_in'           => [$post_id],
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
    ]);
}
?>
<?php if ($related_query->have_posts()) : ?>
    <section class="related-posts-box">
        <div class="section-header section-header--stack">
            <div>
                <span class="section-kicker"><?php esc_html_e('İlgili Yazılar', 'momsy'); ?></span>
                <h3><?php esc_html_e('Okumaya devam edin', 'momsy'); ?></h3>
            </div>
        </div>

        <div class="mini-post-list">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                <?php get_template_part('template-parts/cards/card', 'mini'); ?>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    </section>
<?php endif; ?>
