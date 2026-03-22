<?php get_header(); ?>
<main class="container section-space not-found-shell">
    <div class="empty-card">
        <h1><?php esc_html_e('Sayfa bulunamadı', 'momsy'); ?></h1>
        <p><?php esc_html_e('Aradığınız içerik taşınmış veya kaldırılmış olabilir.', 'momsy'); ?></p>
        <a class="button-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Ana sayfaya dön', 'momsy'); ?></a>
    </div>
</main>
<?php get_footer(); ?>
