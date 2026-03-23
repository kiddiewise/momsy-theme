<?php
if (! defined('ABSPATH')) {
    exit;
}

$guard = momsy_get_content_builder_guard_state();

if (! $guard['allowed']) {
    status_header((int) $guard['status_code']);
}

get_header();
?>

<main id="content" class="page-shell momsy-builder-page section-space">
    <section class="container">
        <?php if ($guard['allowed']) : ?>
            <div
                id="momsy-content-builder-root"
                class="momsy-builder-root"
                data-page-id="<?php echo esc_attr((string) get_queried_object_id()); ?>"
            ></div>
            <noscript>
                <article class="page-card momsy-builder-guard">
                    <span class="section-kicker"><?php esc_html_e('İçerik üretimi', 'momsy'); ?></span>
                    <h1><?php esc_html_e('JavaScript gerekli', 'momsy'); ?></h1>
                    <p><?php esc_html_e('Builder arayüzü JavaScript ile çalışır. Devam etmek için tarayıcında JavaScript\'i etkinleştir.', 'momsy'); ?></p>
                </article>
            </noscript>
        <?php else : ?>
            <article class="page-card momsy-builder-guard">
                <span class="section-kicker"><?php esc_html_e('Korumalı alan', 'momsy'); ?></span>
                <h1><?php echo esc_html($guard['title']); ?></h1>
                <p><?php echo esc_html($guard['message']); ?></p>
                <div class="momsy-builder-guard__actions">
                    <a class="button-primary" href="<?php echo esc_url($guard['action_url']); ?>">
                        <?php echo esc_html($guard['action_text']); ?>
                    </a>
                </div>
            </article>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>
