<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_comment_markup($comment, $args, $depth): void
{
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">
            <div class="comment-meta">
                <div class="author-inline">
                    <?php echo get_avatar($comment, 44); ?>
                    <div>
                        <strong><?php comment_author(); ?></strong><br>
                        <span><?php echo esc_html(get_comment_date('', $comment)); ?></span>
                    </div>
                </div>
            </div>
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
        </article>
    <?php
}
