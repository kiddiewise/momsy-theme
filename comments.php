<?php
if (post_password_required()) {
    return;
}

$comment_count = get_comments_number();
$commenter     = wp_get_current_commenter();
$require_name  = (bool) get_option('require_name_email');
$required_mark = $require_name ? ' *' : '';
$fields        = [
    'author' => sprintf(
        '<p class="comment-form-author"><label for="author">%1$s</label><input id="author" name="author" type="text" value="%2$s" autocomplete="name" %3$s placeholder="%4$s"></p>',
        esc_html__('Adınız', 'momsy') . esc_html($required_mark),
        esc_attr($commenter['comment_author']),
        $require_name ? 'required' : '',
        esc_attr__('Adınızı yazın', 'momsy')
    ),
    'email'  => sprintf(
        '<p class="comment-form-email"><label for="email">%1$s</label><input id="email" name="email" type="email" value="%2$s" autocomplete="email" %3$s placeholder="%4$s"></p>',
        esc_html__('E-posta', 'momsy') . esc_html($required_mark),
        esc_attr($commenter['comment_author_email']),
        $require_name ? 'required' : '',
        esc_attr__('E-posta adresiniz', 'momsy')
    ),
    'url'    => sprintf(
        '<p class="comment-form-url"><label for="url">%1$s</label><input id="url" name="url" type="url" value="%2$s" autocomplete="url" placeholder="%3$s"></p>',
        esc_html__('Web sitesi', 'momsy'),
        esc_attr($commenter['comment_author_url']),
        esc_attr__('Varsa profil bağlantınız', 'momsy')
    ),
];
?>
<section id="comments" class="comments-shell">
    <div class="section-header section-header--stack">
        <div>
            <span class="section-kicker"><?php esc_html_e('Topluluk Alanı', 'momsy'); ?></span>
            <h2>
                <?php
                printf(
                    esc_html(_nx('%s yorum', '%s yorum', $comment_count, 'comment title', 'momsy')),
                    esc_html(number_format_i18n($comment_count))
                );
                ?>
            </h2>
            <p><?php esc_html_e('Annelerin deneyimlerini, sorularını ve düşüncelerini aynı akışta bir araya getirin.', 'momsy'); ?></p>
        </div>
    </div>

    <?php if (have_comments()) : ?>
        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 44,
                'callback'    => 'momsy_comment_markup',
            ]);
            ?>
        </ol>

        <div class="pagination-shell">
            <?php the_comments_pagination(); ?>
        </div>
    <?php endif; ?>

    <?php if (! comments_open() && get_comments_number()) : ?>
        <p class="comments-closed"><?php esc_html_e('Yorumlar şu anda kapalı.', 'momsy'); ?></p>
    <?php endif; ?>

    <div class="comment-form-shell">
        <?php
        comment_form([
            'class_form'           => 'comment-form momsy-comment-form',
            'class_submit'         => 'button-primary',
            'title_reply'          => __('Yorum bırak', 'momsy'),
            'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
            'title_reply_after'    => '</h3>',
            'comment_notes_before' => '<p class="comment-notes">' . esc_html__('Yorumlar yayınlanmadan önce gözden geçirilebilir. Lütfen saygılı ve destekleyici bir dil kullanın.', 'momsy') . '</p>',
            'comment_notes_after'  => '',
            'label_submit'         => __('Yorumu gönder', 'momsy'),
            'logged_in_as'         => '',
            'fields'               => $fields,
            'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__('Yorumunuz', 'momsy') . ' *</label><textarea id="comment" name="comment" cols="45" rows="6" required placeholder="' . esc_attr__('Düşüncelerinizi paylaşın', 'momsy') . '"></textarea></p>',
        ]);
        ?>
    </div>
</section>
