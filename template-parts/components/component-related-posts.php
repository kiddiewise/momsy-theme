<?php
if (! defined('ABSPATH')) {
    exit;
}

$post_id       = get_the_ID();
$primary_term  = momsy_get_post_primary_category($post_id);
$category_ids  = wp_get_post_categories($post_id, ['fields' => 'ids']);
$query_args    = [
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 6,
    'post__not_in'           => [$post_id],
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
];

if (! empty($category_ids)) {
    $query_args['category__in'] = $category_ids;
}

$related_query = new WP_Query($query_args);

if (! $related_query->have_posts()) {
    $related_query = new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 6,
        'post__not_in'           => [$post_id],
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
    ]);
}

$primary_term_link = $primary_term instanceof WP_Term ? get_category_link($primary_term) : '';
?>
<?php if ($related_query->have_posts()) : ?>
    <section class="related-posts-box article-related-section">
        <div class="article-related-section__header">
            <div>
                <span class="section-kicker"><?php esc_html_e('İlginizi çekebilecek diğer içerikler', 'momsy'); ?></span>
                <h2 class="article-related-section__title"><?php esc_html_e('Okumaya devam etmek isteyebilirsiniz', 'momsy'); ?></h2>
                <p class="article-related-section__intro"><?php esc_html_e('Aynı ritmi koruyan, benzer duyguda ve faydada içerikleri sizin için bir araya getirdik.', 'momsy'); ?></p>
            </div>

            <?php if (! is_wp_error($primary_term_link) && is_string($primary_term_link) && '' !== $primary_term_link) : ?>
                <a class="article-related-section__link" href="<?php echo esc_url($primary_term_link); ?>">
                    <?php esc_html_e('Hepsini Gör', 'momsy'); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="discover-carousel article-related-carousel" aria-label="<?php esc_attr_e('İlginizi çekebilecek diğer içerikler', 'momsy'); ?>">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                <?php momsy_render_home_discover_card(); ?>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    </section>
<?php endif; ?>
