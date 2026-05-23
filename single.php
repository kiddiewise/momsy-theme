<?php get_header(); ?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $post_id = get_the_ID();
        $author_id = (int) get_the_author_meta('ID');
        $author_name = get_the_author();
        $author_url = get_author_posts_url($author_id);
        $author_bio = trim((string) get_the_author_meta('description', $author_id));
        $author_fallback = html_entity_decode('Momsy edit&ouml;r ekibi', ENT_QUOTES, 'UTF-8');
        $author_excerpt = $author_bio ? wp_trim_words($author_bio, 8) : $author_fallback;
        $reading_time = momsy_reading_time($post_id);
        $posts_page_url = momsy_get_posts_page_url();
        $home_url = home_url('/');
        $share_url = get_permalink($post_id);
        $topbar_logo_url = 'https://blog.momsy.com.tr/wp-content/uploads/2026/05/momsy-blog-logo.png';
        $care_notice = 'Momsy Blog içerikleri; Momsy ekibi ve alanında uzman kişiler tarafından bilgilendirme amacıyla hazırlanmıştır. Buradaki bilgiler tıbbi tanı, teşhis veya tedavi yerine geçmez. Sağlığınızla ilgili konularda mutlaka doktorunuza veya uzman sağlık profesyonellerine danışınız.';
        ?>
        <main id="content" class="single-shell single-shell--app">
            <article <?php post_class('article-page article-page--app'); ?>>
                <div class="container article-app-shell">
                    <section class="article-app-card">
                        <header class="article-app-topbar">
                            <a href="<?php echo esc_url($posts_page_url); ?>" class="article-app-icon article-app-icon--back" aria-label="<?php esc_attr_e('Yazılara geri dön', 'momsy'); ?>">
                                <span class="article-app-icon__glyph"><?php momsy_the_icon('arrow-left'); ?></span>
                                <span class="article-app-icon__label"><?php esc_html_e('Yazılara geri dön', 'momsy'); ?></span>
                            </a>

                            <a href="<?php echo esc_url($home_url); ?>" class="article-app-topbar__logo" aria-label="<?php esc_attr_e('Momsy Blog ana sayfa', 'momsy'); ?>">
                                <img src="<?php echo esc_url($topbar_logo_url); ?>" alt="<?php esc_attr_e('Momsy Blog', 'momsy'); ?>" loading="eager" fetchpriority="high">
                            </a>

                            <button
                                class="article-app-icon article-app-icon--share"
                                type="button"
                                data-share-post="<?php echo esc_url($share_url); ?>"
                                data-label-default="<?php esc_attr_e('Paylaş', 'momsy'); ?>"
                                data-label-active="<?php esc_attr_e('Kopyalandı', 'momsy'); ?>"
                                aria-label="<?php esc_attr_e('Yazıyı paylaş', 'momsy'); ?>"
                            >
                                <span class="article-app-icon__glyph"><?php momsy_the_icon('share'); ?></span>
                            </button>
                        </header>

                        <div class="article-app-media-wrap">
                            <?php if (has_post_thumbnail()) : ?>
                                <figure class="article-app-media">
                                    <?php the_post_thumbnail('full', ['loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '100vw']); ?>
                                </figure>
                            <?php else : ?>
                                <div class="article-app-media article-app-media--placeholder">
                                    <span class="media-placeholder media-placeholder--article"><?php momsy_the_icon('sparkles'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="article-app-body">
                            <h1 class="article-app-title"><?php echo esc_html(get_the_title()); ?></h1>

                            <div class="article-app-author">
                                <a class="article-app-author__identity" href="<?php echo esc_url($author_url); ?>">
                                    <?php echo get_avatar($author_id, 44); ?>
                                    <span class="article-app-author__copy">
                                        <strong><?php echo esc_html($author_name); ?></strong>
                                        <span><?php echo esc_html($author_excerpt); ?></span>
                                    </span>
                                </a>

                                <span class="article-app-author__meta"><?php echo esc_html($reading_time); ?></span>
                            </div>

                            <div class="article-content-card article-content-card--app">
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
                    </section>

                    <section class="article-support">
                        <div class="article-support__grid">
                            <?php get_template_part('template-parts/components/component', 'related-posts'); ?>
                        </div>
                    </section>
                </div>
            </article>
        </main>
    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>
