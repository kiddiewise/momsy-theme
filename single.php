<?php get_header(); ?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $post_id         = get_the_ID();
        $author_id       = (int) get_the_author_meta('ID');
        $author_name     = get_the_author();
        $author_url      = get_author_posts_url($author_id);
        $author_bio      = trim((string) get_the_author_meta('description', $author_id));
        $author_fallback = html_entity_decode('Momsy edit&ouml;r ekibi', ENT_QUOTES, 'UTF-8');
        $author_excerpt  = $author_bio ? wp_trim_words($author_bio, 18) : $author_fallback;
        $reading_time    = momsy_reading_time($post_id);
        $posts_page_url  = momsy_get_posts_page_url();
        $primary_term    = momsy_get_post_primary_category($post_id);
        $care_notice     = 'Momsy Blog içerikleri; Momsy ekibi ve alanında uzman kişiler tarafından bilgilendirme amacıyla hazırlanmıştır. Buradaki bilgiler tıbbi tanı, teşhis veya tedavi yerine geçmez. Sağlığınızla ilgili konularda mutlaka doktorunuza veya uzman sağlık profesyonellerine danışınız. 💜';
        ?>
        <main id="content" class="single-shell">
            <article <?php post_class('article-page article-page--editorial'); ?>>
                <header class="article-hero article-hero--editorial">
                    <div class="article-cover-panel article-cover-panel--editorial">
                        <div class="article-topbar article-topbar--overlay">
                            <a href="<?php echo esc_url($posts_page_url); ?>" class="back-chip back-chip--overlay">
                                <span class="back-chip__icon"><?php momsy_the_icon('arrow-left'); ?></span>
                                <span><?php esc_html_e('Tüm yazılar', 'momsy'); ?></span>
                            </a>

                            <button class="icon-button icon-button--overlay theme-toggle" type="button" data-theme-toggle aria-label="<?php esc_attr_e('Tema değiştir', 'momsy'); ?>">
                                <span class="icon-button__icon icon-button__icon--sun"><?php momsy_the_icon('sun'); ?></span>
                                <span class="icon-button__icon icon-button__icon--moon"><?php momsy_the_icon('moon'); ?></span>
                            </button>
                        </div>

                        <?php if (has_post_thumbnail()) : ?>
                            <figure class="article-cover article-cover--app article-cover--editorial">
                                <?php the_post_thumbnail('full', ['loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '100vw']); ?>
                            </figure>
                        <?php else : ?>
                            <div class="media-placeholder media-placeholder--article"><?php momsy_the_icon('sparkles'); ?></div>
                        <?php endif; ?>

                        <div class="article-cover-gradient article-cover-gradient--editorial"></div>

                        <div class="container article-cover-copy-wrap">
                            <div class="article-cover-copy article-cover-copy--editorial">
                                <div class="article-cover-badges">
                                    <?php if ($primary_term instanceof WP_Term) : ?>
                                        <?php $primary_term_link = get_category_link($primary_term); ?>
                                        <?php if (! is_wp_error($primary_term_link)) : ?>
                                            <a class="article-cover-copy__eyebrow" href="<?php echo esc_url($primary_term_link); ?>">
                                                <?php echo esc_html($primary_term->name); ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <span class="article-meta-chip">
                                        <span class="article-meta-chip__icon"><?php momsy_the_icon('clock'); ?></span>
                                        <span><?php echo esc_html($reading_time); ?></span>
                                    </span>
                                </div>

                                <h1 class="article-title article-title--cover"><?php echo esc_html(get_the_title()); ?></h1>

                                <?php if (has_excerpt()) : ?>
                                    <p class="article-cover-summary"><?php echo esc_html(get_the_excerpt()); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </header>

                <section class="article-reading-shell">
                    <div class="container article-sheet-wrap">
                        <div class="article-sheet article-sheet--editorial">
                            <div class="article-intro-grid">
                                <div class="article-author-card">
                                    <div class="article-author-card__top">
                                        <a class="author-inline author-inline--feature author-inline--sheet author-inline--verified" href="<?php echo esc_url($author_url); ?>">
                                            <?php echo get_avatar($author_id, 56); ?>
                                            <span class="author-inline__copy">
                                                <span class="author-inline__name-row">
                                                    <strong><?php echo esc_html($author_name); ?></strong>
                                                    <span class="author-verified-badge" aria-hidden="true">
                                                        <?php momsy_the_icon('verified'); ?>
                                                    </span>
                                                    <span class="screen-reader-text"><?php esc_html_e('Onaylı yazar', 'momsy'); ?></span>
                                                </span>
                                                <span class="author-inline__role"><?php echo esc_html($author_fallback); ?></span>
                                            </span>
                                        </a>

                                        <div class="article-author-card__meta">
                                            <span class="article-meta-chip article-meta-chip--soft">
                                                <span class="article-meta-chip__icon"><?php momsy_the_icon('clock'); ?></span>
                                                <span><?php echo esc_html($reading_time); ?></span>
                                            </span>
                                        </div>
                                    </div>

                                    <p class="article-author-card__bio"><?php echo esc_html($author_excerpt); ?></p>
                                </div>

                                <aside class="article-trust-card" aria-label="<?php esc_attr_e('Okuma deneyimi notu', 'momsy'); ?>">
                                    <span class="article-trust-card__eyebrow"><?php esc_html_e('Momsy okuma deneyimi', 'momsy'); ?></span>
                                    <h2 class="article-trust-card__title"><?php esc_html_e('Sade, ferah ve dikkat dağıtmayan bir akış', 'momsy'); ?></h2>
                                    <p class="article-trust-card__body"><?php esc_html_e('Bu sayfa; tarihi, yorumları ve gereksiz etkileşim alanlarını kaldırarak makaleye odaklanmanız için yeniden tasarlandı.', 'momsy'); ?></p>
                                </aside>
                            </div>

                            <div class="article-content-card article-content-card--sheet article-content-card--editorial">
                                <?php the_content(); ?>
                                <?php
                                wp_link_pages([
                                    'before' => '<nav class="post-page-nav" aria-label="' . esc_attr__('Sayfa gezinmesi', 'momsy') . '"><span class="post-page-nav__label">' . esc_html__('Sayfalar', 'momsy') . '</span>',
                                    'after'  => '</nav>',
                                ]);
                                ?>
                            </div>

                            <aside class="article-message-banner" aria-label="<?php esc_attr_e('Momsy bilgilendirme notu', 'momsy'); ?>">
                                <span class="article-message-banner__eyebrow"><?php esc_html_e('Momsy\'den bir mesaj', 'momsy'); ?></span>
                                <p><?php echo esc_html($care_notice); ?></p>
                            </aside>
                        </div>
                    </div>
                </section>

                <section class="article-support container">
                    <div class="article-support__grid">
                        <?php get_template_part('template-parts/components/component', 'related-posts'); ?>
                    </div>
                </section>
            </article>
        </main>
    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>
