<?php get_header(); ?>
<main id="content" class="container section-space not-found-shell">
    <section class="empty-card empty-card--centered">
        <span class="section-kicker"><?php esc_html_e('404', 'momsy'); ?></span>
        <h1><?php esc_html_e('Bu içerik şu anda görünmüyor', 'momsy'); ?></h1>
        <p><?php esc_html_e('Aradığınız sayfa taşınmış, silinmiş veya bağlantı süresi dolmuş olabilir.', 'momsy'); ?></p>
        <a class="button-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Ana sayfaya dön', 'momsy'); ?></a>
    </section>
</main>
<?php get_footer(); ?>
