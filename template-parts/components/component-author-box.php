<section class="author-box">
    <div class="author-inline">
        <?php echo get_avatar(get_the_author_meta('ID'), 56); ?>
        <div>
            <h3><?php the_author(); ?></h3>
            <p><?php echo esc_html(get_the_author_meta('description') ?: __('Momsy editörü', 'momsy')); ?></p>
        </div>
    </div>
</section>
