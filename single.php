<?php get_header(); ?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $post_id        = get_the_ID();
        $author_id      = (int) get_the_author_meta('ID');
        $author_name    = get_the_author();
        $author_url     = get_author_posts_url($author_id);
        $author_bio     = get_the_author_meta('description', $author_id);
        $reading_time   = momsy_reading_time($post_id);
        $posts_page_url = momsy_get_posts_page_url();
        ?>
        <main id="content" class="single-shell">
            <article <?php post_class('article-page'); ?>>
                <header class="article-hero container">
                    <div class="article-phone-card">
                        <div class="article-cover-panel">
                            <div class="article-topbar article-topbar--overlay">
                                <a href="<?php echo esc_url($posts_page_url); ?>" class="back-chip back-chip--overlay">
                                    <span class="back-chip__icon"><?php momsy_the_icon('arrow-left'); ?></span>
                                    <span><?php esc_html_e('Tüm yazılar', 'momsy'); ?></span>
                                </a>
                            </div>

                            <?php if (has_post_thumbnail()) : ?>
                                <figure class="article-cover article-cover--app">
                                    <?php the_post_thumbnail('full', ['loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 767px) 100vw, 52rem']); ?>
                                </figure>
                            <?php else : ?>
                                <div class="media-placeholder media-placeholder--article"><?php momsy_the_icon('sparkles'); ?></div>
                            <?php endif; ?>

                            <div class="article-cover-gradient"></div>

                            <div class="article-cover-copy">
                                <div class="article-status-group">
                                    <?php momsy_post_category_badges($post_id, 1); ?>
                                    <span class="status-pill"><?php esc_html_e('Öne Çıkan', 'momsy'); ?></span>
                                </div>

                                <h1 class="article-title"><?php echo esc_html(get_the_title()); ?></h1>
                            </div>

                            <div class="article-floating-actions">
                                <a class="floating-circle" href="#comments" aria-label="<?php esc_attr_e('Yorumlar', 'momsy'); ?>">
                                    <?php momsy_the_icon('chat'); ?>
                                </a>
                                <button class="floating-circle" type="button" data-share-post="<?php echo esc_url(get_permalink($post_id)); ?>" data-label-default="<?php esc_attr_e('Paylaş', 'momsy'); ?>" data-label-active="<?php esc_attr_e('Kopyalandı', 'momsy'); ?>" aria-label="<?php esc_attr_e('Paylaş', 'momsy'); ?>">
                                    <?php momsy_the_icon('share'); ?>
                                </button>
                                <button class="floating-circle" type="button" data-save-post="<?php echo esc_attr((string) $post_id); ?>" data-label-default="<?php esc_attr_e('Kaydet', 'momsy'); ?>" data-label-active="<?php esc_attr_e('Kaydedildi', 'momsy'); ?>" aria-label="<?php esc_attr_e('Kaydet', 'momsy'); ?>" aria-pressed="false">
                                    <?php momsy_the_icon('bookmark'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="article-sheet">
                            <div class="article-author-row article-author-row--sheet">
                                <a class="author-inline author-inline--feature author-inline--sheet" href="<?php echo esc_url($author_url); ?>">
                                    <?php echo get_avatar($author_id, 52); ?>
                                    <span class="author-inline__copy">
                                        <strong><?php echo esc_html($author_name); ?></strong>
                                        <span><?php echo esc_html($author_bio ? wp_trim_words($author_bio, 12) : __('Momsy editör ekibi', 'momsy')); ?></span>
                                    </span>
                                </a>

                                <div class="article-meta-stack">
                                    <span class="meta-inline"><?php momsy_the_icon('calendar'); ?><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time></span>
                                    <span class="meta-inline"><?php momsy_the_icon('clock'); ?><?php echo esc_html($reading_time); ?></span>
                                    <span class="meta-inline"><?php momsy_the_icon('eye'); ?><?php echo esc_html(number_format_i18n(momsy_get_post_views($post_id))); ?></span>
                                </div>
                            </div>

                            <?php if (has_excerpt()) : ?>
                                <p class="article-lead article-lead--sheet"><?php echo esc_html(get_the_excerpt()); ?></p>
                            <?php endif; ?>

                            <div class="article-stats-card article-stats-card--sheet">
                                <?php momsy_post_stats(); ?>
                            </div>

                            <div class="article-content-card article-content-card--sheet">
                                <?php the_content(); ?>
                                <?php
                                wp_link_pages([
                                    'before' => '<nav class="post-page-nav" aria-label="' . esc_attr__('Sayfa gezinmesi', 'momsy') . '"><span class="post-page-nav__label">' . esc_html__('Sayfalar', 'momsy') . '</span>',
                                    'after'  => '</nav>',
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </header>

                <section class="article-support container">
                    <div class="article-support__grid">
                        <?php get_template_part('template-parts/components/component', 'author-box'); ?>
                        <?php get_template_part('template-parts/components/component', 'related-posts'); ?>
                    </div>
                </section>

                <section class="article-discussion container">
                    <?php comments_template(); ?>
                </section>
            </article>
        </main>
    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>
