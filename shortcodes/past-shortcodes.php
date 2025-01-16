<?php 
/**
 * Shortcode for displaying past shows and events.
 *
 * Usage: 
 * [showmanager_past shows="event-archives"]
 * [showmanager_past shows="show-archives"]
 */

function showmanager_past_shortcode($atts) {
    $atts = shortcode_atts(array(
        'shows' => 'show-archives'
    ), $atts, 'showmanager_past');

    // Check if the 'shows' attribute is for events or shows
    $args = array(
        'post_type' => 'shows',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => array(
            array(
                'taxonomy' => 'show-type',
                'field' => 'slug',
                'terms' => $atts['shows']
            )
        )
    );

    // Run the query
    $pastShowsOrEvents = new WP_Query($args);
    ob_start();

    if ($pastShowsOrEvents->have_posts()) {
        while ($pastShowsOrEvents->have_posts()) : $pastShowsOrEvents->the_post();
            $custom = get_post_meta(get_the_ID());
            $writer = isset($custom["writer"][0]) ? $custom["writer"][0] : '';
            $director = isset($custom["director"][0]) ? $custom["director"][0] : '';
            $address = isset($custom["address"][0]) ? $custom["address"][0] : '';
            $city = isset($custom["city"][0]) ? $custom["city"][0] : '';
            $state = isset($custom["state"][0]) ? $custom["state"][0] : '';
            $dates = isset($custom["month"][0]) ? $custom["month"][0] . " " . $custom["dates"][0] . ", " . $custom["year"][0] : '';
            $dates2 = isset($custom["month2"][0]) && isset($custom["dates2"][0]) && isset($custom["year2"][0]) ? $custom["month2"][0] . " " . $custom["dates2"][0] . ", " . $custom["year2"][0] : null;
            $info = isset($custom["info"][0]) ? $custom["info"][0] : '';
            ?>
            <article class="mb-5">
                <div class="show-info row">
                    <div class="col-lg-4">
                        <div class="post-thumb">
                            <?php echo get_the_post_thumbnail(get_the_ID(), 'shows-image', array('class' => 'd-flex justify-content-center mx-auto mb-4')); ?>
                        </div>
                    </div>    
                    <div class="col-lg-8 pl-5 pr-5">
                        <h3 class="widgettitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ($writer): ?>
                            <p class="pb-0 mb-0">Written by: <?php echo esc_html($writer); ?></p>
                        <?php endif; ?>
                        <?php if ($director): ?>
                            <p class="pb-0">Directed by: <?php echo esc_html($director); ?></p>
                        <?php endif; ?>
                        <p class="address mt-2">
                                  <?php  if ($address): echo esc_html($address) . "<br />"; endif; ?>
                                  <?php if (!$state && $city): echo esc_html($city); endif; 
                                    if ($city && $state): echo esc_html($city) . ", " . esc_html($state) . "<br><br>"; endif; ?>
                                  <?php if ($dates && $dates !== ' , '): echo esc_html($dates); endif; ?>
                                  <?php if ($dates2 && $dates2 !== ' , '): echo '<br />' . esc_html($dates2) . "<br />"; endif; ?>
                                  <?php if ($info): echo '<br/>' . esc_html($info) . "<br />"; endif; ?>
                        </p>
                        <?php the_excerpt(); ?>
                    </div>
                </div>
            </article>
        <?php endwhile;
        wp_reset_postdata();
    }

    return ob_get_clean();
}
add_shortcode('showmanager_past', 'showmanager_past_shortcode');
?>
