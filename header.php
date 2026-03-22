<?php if (! defined('ABSPATH')) exit; ?>
<!doctype html>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr(momsy_get_theme_mode()); ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php wp_head(); ?>
</head>
<body <?php body_class('momsy-body'); ?>>
<?php wp_body_open(); ?>

<header class="site-header glass-header">
    <div class="container header-row">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-mark" aria-label="<?php bloginfo('name'); ?>">
            <?php if (has_custom_logo()) : the_custom_logo(); else : ?>
                <span class="brand-badge">M</span>
                <span class="brand-name"><?php bloginfo('name'); ?></span>
            <?php endif; ?>
        </a>

        <div class="header-actions">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="<?php esc_attr_e('Tema değiştir', 'momsy'); ?>">
                <span class="theme-toggle__sun">☀</span>
                <span class="theme-toggle__moon">☾</span>
            </button>
            <button class="icon-button mobile-menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="site-nav">
                ☰
            </button>
        </div>
    </div>

    <nav id="site-nav" class="site-nav container" aria-label="<?php esc_attr_e('Ana menü', 'momsy'); ?>">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'nav-list',
            'fallback_cb'    => 'momsy_fallback_menu',
        ]);
        ?>
    </nav>
</header>
