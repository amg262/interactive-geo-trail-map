<?php 
/**
* Defined contants
*/
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
define('TRAIL_STORY_DIR_PATH', dirname( __FILE__ ));
define('TRAIL_STORY_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define('TRAIL_STORY_GEO_MASHUP_DIRECTORY', dirname( GEO_MASHUP_PLUGIN_NAME ) );
define('TRAIL_STORY_URL_PATH', trim( plugin_dir_url( __FILE__ ), '/' ) );

/**
* Including files in other directories
*/
include_once('inc/class-trail-story-settings.php');
include_once('inc/class-trail-story-db.php');
include_once('inc/trail-story-frontend-form.php');
include_once('inc/script-styles.php');
include_once('inc/cpt-trail-route.php');
include_once('inc/cpt-trail-story.php');
include_once('inc/cpt-trail-condition.php');
include_once('inc/cpt-trail-segment.php');

/**
* Flushing permalinks for CPTs on DEACTIVATE
*/

add_action( 'admin_init', 'trail_story_flush_rewrites' );

function trail_story_flush_rewrites() {
	//register_cpt_itinerary();
    register_cpt_trail_route();
	register_cpt_trail_story();
	register_cpt_trail_condition();
    register_txn_trail_segment();
   // register_txn_trail_collection();
	flush_rewrite_rules();
}


/**
* Enqueue the plugins JS and CSS files
*/
add_action( 'init', 'register_admin_trail_story_scripts' );

function register_admin_trail_story_scripts() {
    wp_register_script( 'admin_trail_story_js', plugins_url('interactive-geo-trail-map/geo-mashup-trail-story/inc/admin-trail-story.js'), array('jquery'));
    wp_register_style( 'admin_trail_story_css', plugins_url('interactive-geo-trail-map/geo-mashup-trail-story/inc/admin-trail-story.css'));
    wp_enqueue_script( 'admin_trail_story_js' );
    wp_enqueue_style( 'admin_trail_story_css' );
}

/**
* Filter for user displayed map
*/
add_filter( 'geo_mashup_load_user_editor', 'trail_story_filter_geo_mashup_load_user_editor' );

function trail_story_filter_geo_mashup_load_user_editor( $enabled ) {
    
    // Get access to the current WordPress object instance
    global $wp;

    // Get the base URL
    $current_url = home_url(add_query_arg(array(),$wp->request));

    // Add WP's redirect URL string
    $current_url = $current_url . $_SERVER['REDIRECT_URL'];

    // Retrieve the current post's ID based on its URL
    $id = url_to_postid($current_url);

    $post_thing = get_post($id);
    
    $enabled = has_shortcode( $post_thing->post_content, 'frontend_trail_story_map') ? true : false;
    return $enabled;
    
}