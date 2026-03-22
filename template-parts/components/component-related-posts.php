<section class="related-posts-box">
    <h3><?php esc_html_e('İlgili yazılar', 'momsy'); ?></h3>
    <div class="mini-post-list">
        <?php
        $related = new WP_Query([
            'posts_per_page' => 3,
            'post__not_in'   => [get_the_ID()],
            'category__in'   => wp_get_post_categories(get_the_ID()),
        ]);
        if ($related->have_posts()) :
            while ($related->have_posts()) : $related->the_post();
                get_template_part('template-parts/cards/card', 'mini');
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
</section>
