<?php if (! defined('ABSPATH')) { exit; } ?>
<footer class="site-footer">
    <div class="container footer-shell">
        <div class="footer-copy">
            <span class="section-kicker"><?php esc_html_e('Momsy İçerik Deneyimi', 'momsy'); ?></span>
            <h2><?php bloginfo('name'); ?></h2>
            <p><?php echo esc_html(get_bloginfo('description')); ?></p>
        </div>

        <div class="footer-links">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'footer-nav',
                'fallback_cb'    => false,
            ]);
            ?>
        </div>

        <div class="footer-meta">
            <p>
                <?php
                printf(
                    esc_html__('© %1$s %2$s. Tüm hakları saklıdır.', 'momsy'),
                    esc_html(wp_date('Y')),
                    esc_html(get_bloginfo('name'))
                );
                ?>
            </p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
