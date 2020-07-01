<?php
/**
 * Returns the main instance of lead_collector to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object lead_collector
 */
function lead_collector() {
	$instance = lead_collector::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = lead_collector_Settings::instance( $instance );
	}

	return $instance;
}

lead_collector();

// Create post type customer
lead_collector()->create_customer_post_type();

add_shortcode( 'lead_form', array(lead_collector(), 'lead_form_shortcode') );

// Handle ajax of shortcode form post
add_action( 'wp_ajax_lead_form_custom_action', array(lead_collector(),'lead_form_custom_action') );
add_action( 'wp_ajax_nopriv_lead_form_custom_action', array(lead_collector(),'lead_form_custom_action') );

// Handle columns customization of post type customer
add_filter( 'manage_customer_posts_columns', array(lead_collector(),'set_custom_customer_edit_post_columns' ) );
add_action( 'manage_customer_posts_custom_column' , array(lead_collector(),'custom_customer_admin_column'), 10, 2 );
    
