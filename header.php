<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr(momsy_get_active_theme_mode()); ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php wp_head(); ?>
</head>
<body <?php body_class('momsy-body'); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('İçeriğe geç', 'momsy'); ?></a>

<header class="site-header glass-header" data-site-header>
    <div class="container">
        <div class="header-panel">
            <div class="header-row">
                <div class="brand-cluster">
                    <?php if (has_custom_logo()) : ?>
                        <div class="brand-mark brand-mark--logo"><?php the_custom_logo(); ?></div>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-mark" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                            <span class="brand-badge">M</span>
                            <span class="brand-name"><?php bloginfo('name'); ?></span>
                        </a>
                    <?php endif; ?>
                    <span class="brand-pill"><?php esc_html_e('Topluluk Blogu', 'momsy'); ?></span>
                </div>

                <div class="header-actions">
                    <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="<?php esc_attr_e('Tema değiştir', 'momsy'); ?>">
                        <span class="icon-button__icon icon-button__icon--sun"><?php momsy_the_icon('sun'); ?></span>
                        <span class="icon-button__icon icon-button__icon--moon"><?php momsy_the_icon('moon'); ?></span>
                    </button>
                    <button class="icon-button mobile-menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="site-nav" aria-label="<?php esc_attr_e('Menüyü aç', 'momsy'); ?>">
                        <span class="icon-button__icon"><?php momsy_the_icon('menu'); ?></span>
                    </button>
                </div>
            </div>

            <nav id="site-nav" class="site-nav" aria-label="<?php esc_attr_e('Ana menü', 'momsy'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-list',
                    'fallback_cb'    => 'momsy_fallback_menu',
                ]);
                ?>
            </nav>
        </div>
    </div>
</header>
