<?php
get_header();

$home_title = get_theme_mod('momsy_home_title', __('Kesfet', 'momsy'));
$home_desc = get_theme_mod('momsy_home_desc', __('Hamilelikten bebegine uzanan icerikleri kategori kategori kesfet.', 'momsy'));
$discover_sections = momsy_get_home_discover_sections();
$posts_page_url = momsy_get_posts_page_url();
$tools_page = get_page_by_path('yazi-olustur');
$tools_url = $tools_page instanceof WP_Post ? get_permalink($tools_page) : $posts_page_url;
?>

<main id="content" class="momsy-app-shell momsy-discover-page">
    <section class="discover-shell container">
        <header class="discover-pagehead">
            <span class="discover-pagehead__eyebrow"><?php esc_html_e('Kesfet', 'momsy'); ?></span>
            <h1 class="discover-pagehead__title"><?php echo esc_html($home_title); ?></h1>
            <p class="discover-pagehead__intro"><?php echo esc_html($home_desc); ?></p>
        </header>

        <div id="momsy-discover-sections" class="discover-stack">
            <?php if (! empty($discover_sections)) : ?>
                <?php foreach ($discover_sections as $section) : ?>
                    <?php
                    $term = $section['term'] ?? null;
                    $query = $section['query'] ?? null;
                    $term_link = $term instanceof WP_Term ? get_category_link($term) : '';
                    ?>

                    <?php if (! $term instanceof WP_Term || ! $query instanceof WP_Query) : ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <section class="discover-section" aria-labelledby="momsy-discover-section-<?php echo esc_attr((string) $term->term_id); ?>">
                        <div class="discover-section__header">
                            <h2 id="momsy-discover-section-<?php echo esc_attr((string) $term->term_id); ?>" class="discover-section__title">
                                <?php echo esc_html($term->name); ?>
                            </h2>

                            <?php if (! is_wp_error($term_link) && is_string($term_link) && '' !== $term_link) : ?>
                                <a class="discover-section__link" href="<?php echo esc_url($term_link); ?>">
                                    <?php esc_html_e('Hepsini Gor', 'momsy'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="discover-carousel" aria-label="<?php echo esc_attr($term->name); ?>">
                            <?php echo momsy_render_home_discover_cards($query); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php else : ?>
                <?php echo momsy_render_home_discover_empty_state(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
        </div>
    </section>

    <nav class="mobile-bottom-nav mobile-bottom-nav--discover" aria-label="<?php esc_attr_e('Mobil alt menu', 'momsy'); ?>">
        <a class="mobile-bottom-nav__item" href="<?php echo esc_url($posts_page_url); ?>">
            <span class="mobile-bottom-nav__icon"><?php momsy_the_icon('calendar'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Bugun', 'momsy'); ?></span>
        </a>

        <a class="mobile-bottom-nav__item is-active" href="<?php echo esc_url(home_url('/')); ?>" aria-current="page">
            <span class="mobile-bottom-nav__icon"><?php momsy_the_icon('compass'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Kesfet', 'momsy'); ?></span>
        </a>

        <a class="mobile-bottom-nav__item" href="<?php echo esc_url(is_string($tools_url) ? $tools_url : $posts_page_url); ?>">
            <span class="mobile-bottom-nav__icon"><?php momsy_the_icon('briefcase'); ?></span>
            <span class="mobile-bottom-nav__label"><?php esc_html_e('Araclar', 'momsy'); ?></span>
        </a>
    </nav>
</main>

<?php get_footer(); ?>
