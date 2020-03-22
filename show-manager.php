<?php

/*

Plugin Name: Show Manager
description: A plugin to manage shows & events
Version: 1
Author: Peter Giammarco

*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function show_manager_register() {
	
	//Arguments to create post type
	$labels = array(
		'label'	=> __('Shows &amp; Events'),
		'singular_label' => __('Shows &amp; Events'),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		'has_archive' => true,
		'supports' => array('title', 'editor', 'thumbnail'),
		'rewrite' => array('slug' => 'shows', 'with_front' => true),	
	);

	//Register type and custom taxonomy for type
	register_post_type('shows', $labels );

		$args = array(
		'hierarchical'          => true,
		'labels'                => 'Show Types',
		'singular_label'		=> 'Show Type',
		'query_var'             => true,
		'rewrite'               => true,
		'slug' 					=> 'show-type',
		'register_meta_box_cb'  => 'show_manager_add_meta',
	);

	register_taxonomy( 'show-type', 'shows', $args );
}

add_action('init', 'show_manager_register');

	if (function_exists('add_theme_support')) {
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(220,150);
		add_image_size('show-image', 580, 380, true);

	}

	add_action("admin_init", "show_manager_add_meta");

	function show_manager_add_meta() {
		add_meta_box('show-meta', 'Show Options', 'show_manager_meta_options', 'shows', 'normal', 'high');
	}

	function show_manager_meta_options() {
		global $post;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
		
		$custom = get_post_meta($post->ID);

        if(isset($custom["writer"])){
                    $writer= $custom["writer"][0];
            }
        if(isset($custom["director"])){
        			$director= $custom["director"][0];
        	}
        if(isset($custom["address"])){
                    $address= $custom["address"][0];
            }
        if(isset($custom["city"])){
                    $city= $custom["city"][0];
            }
        if(isset($custom["state"])){
                    $state= $custom["state"][0];
            }
        if(isset($custom["ticket"])){
                    $ticket= $custom["ticket"][0];
            }
        if(isset($custom["price"])){
                    $price= $custom["price"][0];
            }            
        if(isset($custom["month"])){
                    $month= $custom["month"][0];
            } else {
            	$month= "";
            }
        if(isset($custom["dates"])){
                    $dates= $custom["dates"][0];
            }
        if(isset($custom["year"])){
                    $year= $custom["year"][0];
            }

        if(isset($custom["month2"])){
                    $month2= $custom["month2"][0];
            } else {
            	$month2= "";
            }
        if(isset($custom["dates2"])){
                    $dates2= $custom["dates2"][0];
            }
        if(isset($custom["year2"])){
                    $year2= $custom["year2"][0];
            }
		if(isset($custom["time"])){
                    $time= $custom["time"][0];
            } else {
            	$time = "";
            }
        if(isset($custom["info"])){
                    $info= $custom["info"][0];
         } 
        if(isset($custom["ampm"])){
                    $ampm= $custom["ampm"][0];
         } else {
         	$ampm = "";
         }   

   ?>

		<style type="text/css">
			<?php include('show-manager.css'); ?>
		</style>

		<div class="show_manager_extras">
		<div><label>Writer:</label><input name="writer" value="<?php echo isset($writer) ? $writer : ''; ?>" /></div>
		<div><label>Director:</label><input name="director" value="<?php echo isset($director) ? $director : ''; ?>" /></div>
		<div><label>Address:</label><input name="address" value="<?php echo isset($address) ? $address : ''; ?>" /></div>
		<div><label>City:</label><input name="city" value="<?php echo isset($city) ? $city : 'West Warwick'; ?>" /></div>
		<div><label>State:</label><input name="state" value="<?php echo isset($state) ? $state : 'RI'; ?>" /></div>
		<div><label>Ticket Link:</label><input name="ticket" value="<?php echo isset($ticket) ? $ticket : ''; ?>" /></div>
		<div><label>Price:</label><input name="price" value="<?php echo isset($price) ? $price : '$'; ?>" /></div>

		<div><label class="bold">First Date</label></div><br/>

		<div><label>Month:</label><select name="month"><?php
		for($m=0;$m<13;$m++) {
			$months = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
			?><option value="<?php echo $months[$m] ?>" <?php selected($month, $months[$m]); ?>><?php echo $months[$m] ?></option> 
			<?php
			}
			?>
		</select><span class="req"> *</span>
		</div>
		<div><label>Dates:</label><input id="dates" name="dates" value="<?php echo isset($dates) ? $dates : ''; ?>" /><span class="req"> *</span></div>
		<div><label>Year:</label><input id="year" name="year" value="<?php echo isset($year) ? $year : ''; ?>" /><span class="req"> *</span></div>

		<div><label  class="bold">Second Date</label></div><br/>

		<div><label>Month:</label><select name="month2"><?php
		for($mt=0;$mt<13;$mt++) {
			$months2 = array("","January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
			?><option value="<?php echo $months2[$mt] ?>" <?php selected($month2, $months2[$mt]); ?>><?php echo $months2[$mt] ?></option> 
			<?php
			}
			?>
		</select>
		</div>
		<div><label>Dates:</label><input id="dates2" name="dates2" value="<?php echo isset($dates2) ? $dates2 : ''; ?>" /></div>
		<div><label>Year:</label><input id="year2" name="year2" value="<?php echo isset($year2) ? $year2 : ''; ?>" /></div><br/>


		<div><label>Time:</label>
		<select name="time"><?php
				for($hours=1; $hours<=12; $hours++){
				for($mins=0; $mins<60; $mins+=30){
				$selected = $hours . ':' . str_pad($mins,2,'0',STR_PAD_LEFT);
				?>	<option value="<?php echo $selected ?>" <?php selected($time, $selected); ?>> <?php echo $hours . ':' . str_pad($mins,2,'0',STR_PAD_LEFT) ?></option>


				<?php
				}
				}
				?>
		</select>

		<select name="ampm">
			<option value="AM" <?php selected($ampm, 'AM'); ?> >AM</option>
			<option value="PM" <?php selected($ampm, 'PM'); ?> >PM</option>
		</select>
								

		</div>
		<div><label>Additional Notes:</label><input name="info" value="<?php echo isset($info) ? $info : ''; ?>" /></div>
		</div>
<?php
		}

		add_action('save_post', 'show_manager_save_extras');

		function show_manager_save_extras( $post_id ){
			global $post;
		
			if ( defined ( 'DOING_AUTOSAVE' )  && DOING_AUTOSAVE ) {
				//do not remove this
				return;
				} else {
				if ( isset($_POST['writer']) ) {
					update_post_meta($post_id, "writer", $_POST["writer"]);
				}
				if ( isset($_POST['director']) ) {
					update_post_meta($post_id, "director", $_POST["director"]);
				}
				if ( isset($_POST['address']) ) {
					update_post_meta($post_id, "address", $_POST["address"]);
				}
				if ( isset($_POST['city']) ) {
					update_post_meta($post_id, "city", $_POST["city"]);
				}
				if ( isset($_POST['state']) ) {
					update_post_meta($post_id, "state", $_POST["state"]);
				}
				if ( isset($_POST['ticket']) ) {
					update_post_meta($post_id, "ticket", $_POST["ticket"]);
				}
				if ( isset($_POST['price']) ) {
					update_post_meta($post_id, "price", $_POST["price"]);
				}
				if ( isset($_POST['month']) ) {
					update_post_meta($post_id, "month", $_POST["month"]);
				}
				if ( isset($_POST['dates']) ) {
					update_post_meta($post_id, "dates", $_POST["dates"]);
				}
				if ( isset($_POST['year']) ) {
					update_post_meta($post_id, "year", $_POST["year"]);
				}

				if ( isset($_POST['month2']) ) {
					update_post_meta($post_id, "month2", $_POST["month2"]);
				}
				if ( isset($_POST['dates2']) ) {
					update_post_meta($post_id, "dates2", $_POST["dates2"]);
				}
				if ( isset($_POST['year2']) ) {
					update_post_meta($post_id, "year2", $_POST["year2"]);
				}

				if ( isset($_POST['time']) ) {
					update_post_meta($post_id, "time", $_POST["time"]);
				}
				if ( isset($_POST['ampm']) ) {
					update_post_meta($post_id, "ampm", $_POST["ampm"]);
				}
				if ( isset($_POST['info']) ) {
					update_post_meta($post_id, "info", $_POST["info"]);
				}

			}

		}

		add_filter("manage_edit-shows_columns", "show_manager_edit_columns");

		function show_manager_edit_columns($columns) {
			$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"created" => "Created Date",
				"description" => "Description",
				"address" => "Address",
				"dates" => "Show Dates",
				"time" => "Time",
				"ticket" => "Ticket",
				"price" => "Price",
				"cat" => "Category",
				);
			return $columns;
		}

		add_action("manage_shows_posts_custom_column", "show_manager_custom_columns");

		function show_manager_custom_columns($column){
			global $post;
			$custom = get_post_custom();
			switch ($column)
			{

				case "created":
					$created = get_the_date();
					echo $created;
					break;
				case "description":
					the_excerpt();
					break;
				case "address":
					$address= $custom["address"][0] . '<br/>';
					$address.= $custom["city"][0].','.$custom["state"][0];
					echo $address;
					break;
				case "ticket":
					$ticket= $custom["ticket"][0];
					echo $ticket;
					break;
				case "price":
					$price= $custom["price"][0];
					echo $price;
					break;
				case "dates":
					$dates= $custom["month"][0] . " " . $custom["dates"][0] . ", " . $custom["year"][0];
					if($custom["month2"][0] == null || $custom["dates2"][0] == null || $custom["year2"][0] == null ) {
							$dates2 = null;							
						} else {							
							$dates2= $custom["month2"][0] . " " . $custom["dates2"][0] . ", " . $custom["year2"][0];
					}
					echo $dates. '<br/>';
					echo $dates2;
					break;
				case "time":
					$time= $custom["time"][0] . " " . $custom["ampm"][0];
					echo $time;
					break;
				case "cat":
					$category = get_the_term_list($post->ID, 'show-type');
					echo $category;
					break;
				}
			}


			add_filter( 'manage_edit-shows_sortable_columns', 'show_sortable_columns' );

			function show_sortable_columns( $columns ) {

				$columns['created'] = 'created';
				
				return $columns;
			}

			add_action( 'pre_get_posts', 'shows_orderby' );
			function shows_orderby( $query ) {
			    if( ! is_admin() )
			        return;
			 
			    $orderby = $query->get( 'created');
			 
			    if( 'slice' == $orderby ) {
			        $query->set('meta_key','created');
			        $query->set('orderby','meta_value_num');
			    }

			}
			
	
?>