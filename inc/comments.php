<?php
if (! defined('ABSPATH')) {
    exit;
}

function momsy_comment_markup($comment, $args, $depth): void
{
    if ('pingback' === $comment->comment_type || 'trackback' === $comment->comment_type) {
        ?>
        <li <?php comment_class('comment comment--pingback'); ?> id="comment-<?php comment_ID(); ?>">
            <article class="comment-body">
                <p>
                    <?php esc_html_e('Geri bildirim:', 'momsy'); ?>
                    <?php comment_author_link(); ?>
                </p>
            </article>
        </li>
        <?php
        return;
    }
    ?>
    <li <?php comment_class('comment-card'); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">
            <header class="comment-meta">
                <div class="author-inline">
                    <?php echo get_avatar($comment, 44); ?>
                    <div>
                        <strong class="comment-author-name"><?php echo esc_html(get_comment_author($comment)); ?></strong>
                        <time datetime="<?php echo esc_attr(get_comment_time('c')); ?>" class="comment-date">
                            <?php echo esc_html(get_comment_date('', $comment)); ?>
                        </time>
                    </div>
                </div>
            </header>

            <div class="comment-content">
                <?php if ('0' === $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation"><?php esc_html_e('Yorumunuz onay bekliyor.', 'momsy'); ?></p>
                <?php endif; ?>

                <?php comment_text(); ?>
            </div>

            <footer class="comment-footer">
                <?php
                comment_reply_link(
                    array_merge(
                        $args,
                        [
                            'add_below'  => 'comment',
                            'depth'      => $depth,
                            'max_depth'  => $args['max_depth'],
                            'reply_text' => esc_html__('Yanıtla', 'momsy'),
                            'before'     => '<span class="comment-reply-link-wrapper">',
                            'after'      => '</span>',
                        ]
                    )
                );
                ?>
            </footer>
        </article>
    </li>
    <?php
}
