<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_get_home_discover_categories(int $limit = 6): array
{
    $terms = [];
    $locations = get_nav_menu_locations();
    $menu_id = isset($locations['home_categories']) ? (int) $locations['home_categories'] : 0;

    if ($menu_id > 0) {
        $menu_items = wp_get_nav_menu_items($menu_id);

        if (is_array($menu_items)) {
            foreach ($menu_items as $menu_item) {
                if (! $menu_item instanceof WP_Post || 'category' !== $menu_item->object) {
                    continue;
                }

                $term = get_term((int) $menu_item->object_id, 'category');

                if ($term instanceof WP_Term) {
                    $terms[$term->term_id] = $term;
                }
            }
        }
    }

    if (empty($terms)) {
        $fallback_terms = get_categories([
            'taxonomy'   => 'category',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => max(1, $limit),
        ]);

        if (is_array($fallback_terms)) {
            foreach ($fallback_terms as $term) {
                if ($term instanceof WP_Term) {
                    $terms[$term->term_id] = $term;
                }
            }
        }
    }

    return array_slice(array_values($terms), 0, max(1, $limit));
}

function momsy_get_home_discover_query(int $category_id, int $posts_per_page = 6): WP_Query
{
    return new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => max(1, $posts_per_page),
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
        'cat'                    => $category_id,
    ]);
}

function momsy_get_home_discover_sections(int $category_limit = 6, int $posts_per_section = 6): array
{
    $sections = [];
    $categories = momsy_get_home_discover_categories($category_limit);

    foreach ($categories as $category) {
        if (! $category instanceof WP_Term) {
            continue;
        }

        $query = momsy_get_home_discover_query((int) $category->term_id, $posts_per_section);

        if (! $query->have_posts()) {
            continue;
        }

        $sections[] = [
            'term'  => $category,
            'query' => $query,
        ];
    }

    return $sections;
}

function momsy_render_home_discover_empty_state(): string
{
    return sprintf(
        '<div class="discover-empty-state"><p>%s</p></div>',
        esc_html__('Bu tasarımı gösterebilmek için seçili kategorilere yazı ekleyin.', 'momsy')
    );
}

function momsy_render_home_discover_card(?WP_Post $post = null): void
{
    $post = $post ?: get_post();

    if (! $post instanceof WP_Post) {
        return;
    }

    $post_id = $post->ID;
    $permalink = get_permalink($post_id);
    $title = get_the_title($post_id);

    if (! is_string($permalink) || '' === $permalink) {
        return;
    }
    ?>
    <article class="discover-card">
        <a class="discover-card__link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <span class="discover-card__media">
                <?php if (has_post_thumbnail($post_id)) : ?>
                    <?php echo get_the_post_thumbnail($post_id, 'full', ['sizes' => '(max-width: 767px) 78vw, (max-width: 1099px) 42vw, 22rem']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php else : ?>
                    <span class="media-placeholder media-placeholder--discover"><?php momsy_the_icon('sparkles'); ?></span>
                <?php endif; ?>
            </span>

            <span class="discover-card__gradient" aria-hidden="true"></span>

            <span class="discover-card__content">
                <span class="discover-card__title"><?php echo esc_html($title); ?></span>
            </span>
        </a>
    </article>
    <?php
}

function momsy_render_home_discover_cards(WP_Query $query): string
{
    if (! $query->have_posts()) {
        return momsy_render_home_discover_empty_state();
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        momsy_render_home_discover_card();
    }

    wp_reset_postdata();

    return (string) ob_get_clean();
}
