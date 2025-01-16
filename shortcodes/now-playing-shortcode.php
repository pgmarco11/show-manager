<?php
/**
 * Shortcode for displaying featured shows.
 *
 * Usage: [showmanager_featured]
 */

function show_manager_shortcode($atts) {
    // Query arguments for "now-playing" shows
    $args = array(
        'post_type' => 'shows',
        'posts_per_page' => 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'show-type',
                'field'    => 'slug',
                'terms'    => 'now-playing'  // Hardcoded to 'now-playing'
            )
        )
    );

    $featuredShow = get_posts($args);
    
    ob_start(); // Start output buffering

    foreach ($featuredShow as $post) : setup_postdata($post);
        $custom = get_post_meta($post->ID);
        $writer = isset($custom["writer"][0]) ? $custom["writer"][0] : '';
        $director = isset($custom["director"][0]) ? $custom["director"][0] : '';
        $address = isset($custom["address"][0]) ? $custom["address"][0] : '';
        $city = isset($custom["city"][0]) ? $custom["city"][0] : '';
        $state = isset($custom["state"][0]) ? $custom["state"][0] : '';
        $ticket = isset($custom["ticket"][0]) ? $custom["ticket"][0] : '';
        $price = isset($custom["price"][0]) ? $custom["price"][0] : '';
        $dates = isset($custom["month"][0]) ? $custom["month"][0] . " " . $custom["dates"][0] . ", " . $custom["year"][0] : '';
        $dates2 = (isset($custom["month2"][0]) && isset($custom["dates2"][0]) && isset($custom["year2"][0])) ? $custom["month2"][0] . " " . $custom["dates2"][0] . ", " . $custom["year2"][0] : '';
        $time = isset($custom["time"][0]) ? $custom["time"][0] . " " . $custom["ampm"][0] : '';
        $info = isset($custom["info"][0]) ? $custom["info"][0] : '';
        ?>
        <article class="mb-4">
            <div class="row">
                <div class="col-lg-4">
                    <div class="post-thumb">
                        <?php echo get_the_post_thumbnail($post->ID, 'shows-image', array('class' => 'd-flex justify-content-center mx-auto mb-4')); ?>
                    </div>
                </div>
                <div class="col-lg-8 pl-5 pr-5">
                    <div class="now-playing">
                        <h3><a href="<?php echo get_the_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a></h3>
                        <?php if ($writer) echo "<p>Written by: $writer</p>"; ?>
                        <?php if ($director) echo "<p>Directed by: $director</p>"; ?>   
                        <p class="address">
                                   <?php  if ($address): echo esc_html($address) . "<br />"; endif; ?>
                                  <?php if (!$state && $city): echo esc_html($city); endif; 
                                    if ($city && $state): echo esc_html($city) . ", " . esc_html($state) . "<br><br>"; endif; ?>
                                  <?php if ($dates && $dates !== ' , '): echo esc_html($dates); endif; ?>
                                  <?php if ($dates2 && $dates2 !== ' , '): echo '<br />' . esc_html($dates2) . "<br />"; endif; ?>
                                  <?php if ($time): echo esc_html($time) . "<br />"; endif; ?>
                        </p>
                        <p>Price: <?php echo $price; ?></p>
                        <a class="btn tickets" href="<?php echo esc_url($ticket); ?>" target="_blank" title="buy tickets">Buy Tickets</a>
                        <?php if ($info): echo '<br/>' . esc_html($info) . "<br />"; endif; ?>
                        <div class="textwidget mt-4">
                            <?php get_the_excerpt($post->ID); ?>
                        </div>
                    </div>
                </div>
            </div>
        </article>
        <?php
    endforeach;
    
    wp_reset_postdata(); // Reset post data
    return ob_get_clean(); // Return the buffered content
}

add_shortcode('showmanager_featured', 'show_manager_shortcode');
?>
