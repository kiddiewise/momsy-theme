<?php
get_header();

$discover_sections = momsy_get_home_discover_sections();
?>

<main id="content" class="momsy-app-shell momsy-discover-page">
    <section class="discover-shell container">
        <header class="discover-pagehead">
            <a class="discover-pagehead__logo-link" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Momsy Blog ana sayfa', 'momsy'); ?>">
                <img
                    class="discover-pagehead__logo"
                    src="https://blog.momsy.com.tr/wp-content/uploads/2026/05/momsy-blog-logo.png"
                    alt="<?php esc_attr_e('Momsy Blog', 'momsy'); ?>"
                    width="500"
                    height="250"
                    loading="eager"
                    fetchpriority="high"
                >
            </a>

            <span class="discover-pagehead__intro">
                <?php esc_html_e('Momsy Blog’da; hamilelik sürecinden anneliğe, bebek gelişiminden uzman önerilerine kadar sana ve bebeğine özel içerikleri keşfet. 💜', 'momsy'); ?>
            </span>
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
                                    <?php esc_html_e('Hepsini Gör', 'momsy'); ?>
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
</main>

<?php get_footer(); ?>
