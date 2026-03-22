<?php if (! defined('ABSPATH')) exit; ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h3><?php bloginfo('name'); ?></h3>
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
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
