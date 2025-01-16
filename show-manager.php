<?php
/**
 * Plugin Name: Show Manager
 * Description: A plugin to manage shows and events with custom post types and taxonomies.
 * Version: 1.0
 * Author: Peter Giammarco
 */

// Include shortcodes
include('shortcodes/now-playing-shortcode.php');
include('shortcodes/past-shortcodes.php');
include('shortcodes/upcoming-shortcodes.php');

function shows_manager_enqueue() {
    wp_enqueue_style('shows-main', plugin_dir_url(__FILE__) . 'css/shows-main.css', array(), '1.0', false);
}
add_action( 'wp_enqueue_scripts', 'shows_manager_enqueue', 20 );

// Register custom post type and taxonomy
function show_manager_register() {
    // Register post type
    $labels = array(
        'name'               => __('Shows & Events'),
        'singular_name'      => __('Show & Event'),
        'menu_name'          => __('Shows & Events'),  // Explicitly set the menu name
        'all_items'          => __('All Shows & Events'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New Show & Event'),
        'edit_item'          => __('Edit Show & Event'),
        'new_item'           => __('New Show & Event'),
        'view_item'          => __('View Show & Event'),
        'search_items'       => __('Search Shows & Events'),
        'not_found'          => __('No Shows & Events found'),
        'not_found_in_trash' => __('No Shows & Events found in Trash'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'capability_type'    => 'post',
        'hierarchical'       => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'rewrite'            => array('slug' => 'shows', 'with_front' => true),
        'show_in_menu'       => true,
    );

    register_post_type('shows', $args);

    // Register taxonomy
    $argstax = array(
        'hierarchical'       => true,
        'labels'             => array(
            'name'          => 'Show Types',
            'singular_name' => 'Show Type',
        ),
        'query_var'          => true,
        'rewrite'            => array('slug' => 'show-type'),
        'register_meta_box_cb' => 'show_manager_add_meta',
    );

    register_taxonomy('show-type', 'shows', $argstax);
}
add_action('init', 'show_manager_register');

// ** Generates the permalink for custom post type 'shows' for search
add_filter('post_type_link', 'custom_post_permalink', 10, 2);

function custom_post_permalink($permalink, $post) {
    if ($post->post_type == 'shows') {
        $year = get_the_date('Y', $post->ID);
        $postname = $post->post_name;
        $permalink = home_url('/shows/' . $year . '/' . $postname . '/');
    }
    return $permalink;
}

// Add theme support for thumbnails
function show_manager_setup() {
    if (function_exists('add_theme_support')) {
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(220, 150);
        add_image_size('show-image', 580, 380, true);
    }
}
add_action('after_setup_theme', 'show_manager_setup');


// Add meta box
function show_manager_add_meta() {
    add_meta_box('show-meta', 'Show Options', 'show_manager_meta_options', 'shows', 'normal', 'high');
}
add_action('admin_init', 'show_manager_add_meta');

// Display meta box content
function show_manager_meta_options() {
    global $post;
    $custom = get_post_meta($post->ID);

    // Meta fields
    $fields = ['writer', 'director', 'address', 'city', 'state', 'ticket', 'price', 'month', 'dates', 'year', 'month2', 'dates2', 'year2', 'time', 'ampm', 'info'];
    $meta_values = [];

    foreach ($fields as $field) {
        $meta_values[$field] = isset($custom[$field]) ? $custom[$field][0] : '';
    }

    // Include CSS for the meta box (assumes CSS is in the plugin folder)
    echo '<style type="text/css">';
    include(plugin_dir_path(__FILE__) . 'css/show-manager.css');
    echo '</style>';

    // Meta box form fields
    ?>
    <div class="show_manager_extras">
        <div><label>Writer:</label><input name="writer" value="<?php echo esc_attr($meta_values['writer']); ?>" /></div>
        <div><label>Director:</label><input name="director" value="<?php echo esc_attr($meta_values['director']); ?>" /></div>
        <div><label>Address:</label><input name="address" value="<?php echo esc_attr($meta_values['address']); ?>" /></div>
        <div><label>City:</label><input name="city" value="<?php echo esc_attr($meta_values['city']); ?>" /></div>
        <div><label>State:</label><input name="state" value="<?php echo esc_attr($meta_values['state']); ?>" /></div>
        <div><label>Ticket Link:</label><input name="ticket" value="<?php echo esc_attr($meta_values['ticket']); ?>" /></div>
        <div><label>Price:</label><input name="price" value="<?php echo esc_attr($meta_values['price']); ?>" /></div>

        <!-- Date Fields -->
        <div><label class="bold">First Date</label></div><br/>
        <div><label>Month:</label><select name="month"><?php
            for ($m = 1; $m <= 12; $m++) {
                $months = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                ?><option value="<?php echo $months[$m]; ?>" <?php selected($meta_values['month'], $months[$m]); ?>><?php echo $months[$m]; ?></option><?php
            }
        ?></select></div>
        <div><label>Dates:</label><input name="dates" value="<?php echo esc_attr($meta_values['dates']); ?>" /></div>
        <div><label>Year:</label><input name="year" value="<?php echo esc_attr($meta_values['year']); ?>" /></div>

        <!-- Second Date Fields -->
        <div><label class="bold">Second Date</label></div><br/>
        <div><label>Month:</label><select name="month2"><?php
            for ($mt = 1; $mt <= 12; $mt++) {
                $months2 = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                ?><option value="<?php echo $months2[$mt]; ?>" <?php selected($meta_values['month2'], $months2[$mt]); ?>><?php echo $months2[$mt]; ?></option><?php
            }
        ?></select></div>
        <div><label>Dates:</label><input name="dates2" value="<?php echo esc_attr($meta_values['dates2']); ?>" /></div>
        <div><label>Year:</label><input name="year2" value="<?php echo esc_attr($meta_values['year2']); ?>" /></div>

        <div><label>Time:</label>
            <select name="time"><?php
                for ($hours = 1; $hours <= 12; $hours++) {
                    for ($mins = 0; $mins < 60; $mins += 30) {
                        $selected = $hours . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
                        ?><option value="<?php echo $selected; ?>" <?php selected($meta_values['time'], $selected); ?>><?php echo $hours . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT); ?></option><?php
                    }
                }
            ?></select>
            <select name="ampm">
                <option value="AM" <?php selected($meta_values['ampm'], 'AM'); ?>>AM</option>
                <option value="PM" <?php selected($meta_values['ampm'], 'PM'); ?>>PM</option>
            </select>
        </div>

        <div><label>Additional Notes:</label><input name="info" value="<?php echo esc_attr($meta_values['info']); ?>" /></div>
    </div>
    <?php
}

// Save meta box data
function show_manager_save_extras($post_id) {
    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Save data
    $fields = ['writer', 'director', 'address', 'city', 'state', 'ticket', 'price', 'month', 'dates', 'year', 'month2', 'dates2', 'year2', 'time', 'ampm', 'info'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'show_manager_save_extras');

// Customize admin columns
function show_manager_edit_columns($columns) {
    return array(
        "cb"       => "<input type=\"checkbox\" />",
        "title"    => "Title",
        "created"  => "Created Date",
        "address"  => "Address",
        "dates"    => "Show Dates",
        "time"     => "Time",
        "ticket"   => "Ticket",
        "price"    => "Price",
        "cat"      => "Category",
    );
}
add_filter("manage_edit-shows_columns", "show_manager_edit_columns");

// Display custom column content
function show_manager_custom_columns($column) {
    global $post;
    $custom = get_post_custom($post->ID);

    switch ($column) {
        case "address":
            echo isset($custom["address"][0]) ? $custom["address"][0] : '';
            break;
        case "dates":
            echo isset($custom["month"][0]) ? $custom["month"][0] . " " . $custom["dates"][0] : '';
            break;
        case "time":
            echo isset($custom["time"][0]) ? $custom["time"][0] : '';
            break;
        case "ticket":
            echo isset($custom["ticket"][0]) ? '<a href="' . $custom["ticket"][0] . '" target="_blank">Buy Tickets</a>' : '';
            break;
        case "price":
            echo isset($custom["price"][0]) ? $custom["price"][0] : '';
            break;
        case "cat":
            $terms = get_the_terms($post->ID, "show-type");
            if ($terms) {
                $names = array_map(function ($term) {
                    return $term->name;
                }, $terms);
                echo implode(', ', $names);
            }
            break;
    }
}
add_action("manage_shows_posts_custom_column", "show_manager_custom_columns");
?>
