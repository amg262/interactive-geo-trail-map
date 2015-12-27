<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/*
* Custom Post Type for Trail Story
*/
add_action( 'init', 'register_cpt_trail_route' );

function register_cpt_trail_route() {

    $count = wp_count_posts('trail-route');
    $pending_count = $count->pending;

    $labels = array( 
        'name' => _x( 'Trail Route', 'trail-route' ),
        'singular_name' => _x( 'Trail Route', 'trail-route' ),
        'add_new' => _x( 'Add New', 'trail-route' ),
        'add_new_item' => _x( 'Add New Trail Route', 'trail-route' ),
        'edit_item' => _x( 'Edit Trail Route', 'trail-route' ),
        'new_item' => _x( 'New Trail Route', 'trail-route' ),
        'view_item' => _x( 'View Trail Routes', 'trail-route' ),
        'all_items'          => __( 'All Trail Routes', 'trail-condition' ),
        'search_items' => _x( 'Search Trail Routes', 'trail-route' ),
        'not_found' => _x( 'No Trail Routes found', 'trail-route' ),
        'not_found_in_trash' => _x( 'No Trail Routes found in Trash', 'trail-route' ),
        'parent_item_colon' => _x( 'Parent Trail Routes:', 'trail-route' ),
        'menu_name' => _x( 'Trail Routes', 'trail-route' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ), // 'custom-fields' , 'excerpt' 
        'taxonomies' => array(  'trail-routes' ), //, 'post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5003,
        'menu_icon' => 'dashicons-location-alt', //dashicons-location-alt 'dashicons-post-status', 'dashicons-media-audio'
        'has_archive' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'trail-route', $args );
}


/**
* Taxonomy for Trail Story CPT categories
*/
add_action( 'init', 'register_txn_trail_route' );

function register_txn_trail_route() {

    $labels = array( 
        'name' => _x( 'Trail Route Category', 'trail-routes' ),
        'singular_name' => _x( 'Trail Route Category', 'trail-routes' ),
        'search_items' => _x( 'Search Trail Story Categories', 'trail-routes' ),
        'popular_items' => _x( 'Popular Trail Story Categories', 'trail-routes' ),
        'all_items' => _x( 'All Trail Story Categories', 'trail-routes' ),
        'parent_item' => _x( 'Parent Trail Route Category', 'trail-routes' ),
        'parent_item_colon' => _x( 'Parent Trail Route Category:', 'trail-routes' ),
        'edit_item' => _x( 'Edit Trail Route Category', 'trail-routes' ),
        'update_item' => _x( 'Update Trail Route Category', 'trail-routes' ),
        'add_new_item' => _x( 'Add New', 'trail-routes' ),
        'new_item_name' => _x( 'New Trail Route Category', 'trail-routes' ),
        'separate_items_with_commas' => _x( 'Separate Trail Story Categories with commas', 'trail-routes' ),
        'add_or_remove_items' => _x( 'Add or remove Trail Story Categories', 'trail-routes' ),
        'choose_from_most_used' => _x( 'Choose from the most used Trail Route Category', 'trail-routes' ),
        'menu_name' => _x( 'Route Categories', 'trail-routes' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_tagcloud' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => array( 'slug' => 'route-category' ),
        'query_var' => true
    );

    register_taxonomy( 'trail-routes', array('trail-route'), $args );

    if (! has_term( 'iata-trail-route-parking', 'trail-routes' )) {
        wp_insert_term( 'Parking','trail-routes', array('Trail Parking Locations', 'iata-trail-route') );
    }
}

