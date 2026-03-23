<?php
if (! defined('ABSPATH')) {
    exit;
}

define('MOMSY_VERSION', '1.0.0');
define('MOMSY_DIR', get_template_directory());
define('MOMSY_URI', get_template_directory_uri());

require_once MOMSY_DIR . '/inc/theme-setup.php';
require_once MOMSY_DIR . '/inc/enqueue.php';
require_once MOMSY_DIR . '/inc/template-tags.php';
require_once MOMSY_DIR . '/inc/post-meta.php';
require_once MOMSY_DIR . '/inc/customizer.php';
require_once MOMSY_DIR . '/inc/comments.php';
require_once MOMSY_DIR . '/inc/content-builder/bootstrap.php';
