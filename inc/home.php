<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_get_home_featured_post(): ?WP_Post
{
    $query = new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 1,
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
    ]);

    if (! empty($query->posts[0]) && $query->posts[0] instanceof WP_Post) {
        return $query->posts[0];
    }

    return null;
}

function momsy_get_home_feed_query_args(int $page = 1, int $category_id = 0, array $exclude_ids = []): array
{
    $exclude_ids = array_values(array_filter(array_map('absint', $exclude_ids)));

    $args = [
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 10,
        'paged'                  => max(1, $page),
        'ignore_sticky_posts'    => true,
        'update_post_term_cache' => true,
    ];

    if ($category_id > 0) {
        $args['cat'] = $category_id;
    }

    if (! empty($exclude_ids)) {
        $args['post__not_in'] = $exclude_ids;
    }

    return $args;
}

function momsy_get_home_feed_query(int $page = 1, int $category_id = 0, array $exclude_ids = []): WP_Query
{
    return new WP_Query(momsy_get_home_feed_query_args($page, $category_id, $exclude_ids));
}

function momsy_get_home_categories(): array
{
    $terms = get_categories([
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    return is_array($terms) ? $terms : [];
}

function momsy_render_home_feed_empty_state(): string
{
    return sprintf(
        '<div class="home-feed-empty"><p>%s</p></div>',
        esc_html__('Bu kategoride henüz içerik bulunmuyor.', 'momsy')
    );
}

function momsy_render_home_search_empty_state(?string $message = null): string
{
    return sprintf(
        '<div class="home-search-empty"><p>%s</p></div>',
        esc_html($message ?: __('Aramak istediğiniz konuyu yazın.', 'momsy'))
    );
}

function momsy_render_home_feed_card(?WP_Post $post = null): void
{
    $post = $post ?: get_post();

    if (! $post instanceof WP_Post) {
        return;
    }

    $post_id   = $post->ID;
    $permalink = get_permalink($post_id);
    $title     = get_the_title($post_id);

    if (! is_string($permalink) || '' === $permalink) {
        return;
    }
    ?>
    <article class="home-feed-card">
        <a class="home-feed-card__media" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <?php if (has_post_thumbnail($post_id)) : ?>
                <?php echo get_the_post_thumbnail($post_id, 'momsy-mini', ['sizes' => '(max-width: 767px) 92px, 110px']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php else : ?>
                <span class="media-placeholder media-placeholder--mini"><?php momsy_the_icon('sparkles'); ?></span>
            <?php endif; ?>
        </a>

        <div class="home-feed-card__body">
            <h3 class="home-feed-card__title">
                <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
            </h3>

            <div class="home-feed-card__meta">
                <span class="meta-inline">
                    <span class="meta-inline__icon"><?php momsy_the_icon('calendar'); ?></span>
                    <time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>"><?php echo esc_html(get_the_date('d M Y', $post_id)); ?></time>
                </span>
                <span class="meta-inline">
                    <span class="meta-inline__icon"><?php momsy_the_icon('clock'); ?></span>
                    <span><?php echo esc_html(momsy_reading_time($post_id)); ?></span>
                </span>
            </div>
        </div>
    </article>
    <?php
}

function momsy_render_home_feed_cards(WP_Query $query): string
{
    if (! $query->have_posts()) {
        return momsy_render_home_feed_empty_state();
    }

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        momsy_render_home_feed_card();
    }

    wp_reset_postdata();

    return (string) ob_get_clean();
}

function momsy_get_home_search_query(string $search_term): WP_Query
{
    return new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 10,
        's'                      => $search_term,
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
    ]);
}

function momsy_render_home_search_results(string $search_term): string
{
    $search_term = trim($search_term);

    if (mb_strlen($search_term) < 2) {
        return momsy_render_home_search_empty_state(__('En az 2 karakter ile arama yapabilirsiniz.', 'momsy'));
    }

    $query = momsy_get_home_search_query($search_term);

    if (! $query->have_posts()) {
        return momsy_render_home_search_empty_state(__('Aramanızla eşleşen bir içerik bulunamadı.', 'momsy'));
    }

    ob_start();
    echo '<div class="home-search-results__list">';
    while ($query->have_posts()) {
        $query->the_post();
        momsy_render_home_feed_card();
    }
    echo '</div>';
    wp_reset_postdata();

    return (string) ob_get_clean();
}

function momsy_render_home_featured_card(?WP_Post $post): void
{
    if (! $post instanceof WP_Post) {
        return;
    }

    $post_id   = $post->ID;
    $permalink = get_permalink($post_id);
    $title     = get_the_title($post_id);
    $author_id = (int) get_post_field('post_author', $post_id);

    if (! is_string($permalink) || '' === $permalink) {
        return;
    }
    ?>
    <article class="featured-story">
        <a class="featured-story__media" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <?php if (has_post_thumbnail($post_id)) : ?>
                <?php echo get_the_post_thumbnail($post_id, 'momsy-hero', ['sizes' => '(max-width: 767px) 100vw, 32rem']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php else : ?>
                <span class="media-placeholder media-placeholder--hero"><?php momsy_the_icon('sparkles'); ?></span>
            <?php endif; ?>
        </a>

        <div class="featured-story__body">
            <h2 class="featured-story__title">
                <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
            </h2>

            <div class="featured-story__meta">
                <span class="featured-story__author">
                    <?php echo get_avatar($author_id, 28); ?>
                    <span><?php echo esc_html(get_the_author_meta('display_name', $author_id)); ?></span>
                </span>
                <span class="meta-inline">
                    <span class="meta-inline__icon"><?php momsy_the_icon('calendar'); ?></span>
                    <time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>"><?php echo esc_html(get_the_date('d M Y', $post_id)); ?></time>
                </span>
            </div>
        </div>
    </article>
    <?php
}

function momsy_handle_home_posts_load(): void
{
    check_ajax_referer('momsy_home_posts', 'nonce');

    $page        = isset($_POST['page']) ? max(1, absint(wp_unslash($_POST['page']))) : 1;
    $category_id = isset($_POST['category_id']) ? absint(wp_unslash($_POST['category_id'])) : 0;
    $hero_id     = isset($_POST['hero_id']) ? absint(wp_unslash($_POST['hero_id'])) : 0;
    $exclude_ids = 0 === $category_id && $hero_id > 0 ? [$hero_id] : [];
    $query       = momsy_get_home_feed_query($page, $category_id, $exclude_ids);

    wp_send_json_success([
        'html'     => momsy_render_home_feed_cards($query),
        'page'     => $page,
        'maxPages' => max(1, (int) $query->max_num_pages),
        'hasMore'  => $page < (int) $query->max_num_pages,
    ]);
}
add_action('wp_ajax_momsy_load_home_posts', 'momsy_handle_home_posts_load');
add_action('wp_ajax_nopriv_momsy_load_home_posts', 'momsy_handle_home_posts_load');

function momsy_handle_home_search(): void
{
    check_ajax_referer('momsy_home_search', 'nonce');

    $search_term = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';

    wp_send_json_success([
        'html' => momsy_render_home_search_results($search_term),
    ]);
}
add_action('wp_ajax_momsy_home_search', 'momsy_handle_home_search');
add_action('wp_ajax_nopriv_momsy_home_search', 'momsy_handle_home_search');
