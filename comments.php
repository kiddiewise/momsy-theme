<?php
if (post_password_required()) {
    return;
}
?>
<section id="comments" class="comments-shell">
    <div class="section-header">
        <h2><?php comments_number(__('Yorumlar', 'momsy'), __('1 Yorum', 'momsy'), __('% Yorum', 'momsy')); ?></h2>
    </div>

    <?php if (have_comments()) : ?>
        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 44,
                'callback'   => 'momsy_comment_markup',
            ]);
            ?>
        </ol>
        <?php the_comments_pagination(); ?>
    <?php endif; ?>

    <?php comment_form([
        'class_submit'  => 'button-primary',
        'title_reply'   => __('Yorum bırak', 'momsy'),
        'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="6" required placeholder="' . esc_attr__('Düşüncelerinizi paylaşın', 'momsy') . '"></textarea></p>',
    ]); ?>
</section>
