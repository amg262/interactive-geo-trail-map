<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );


/*
* Custom Post Type for Trail Story
*/
//add_action( 'init', 'register_cpt_trail_segment' );

function register_cpt_trail_segment() {

    $count = wp_count_posts('trail-segment');
    $pending_count = $count->pending;

    $labels = array( 
        'name' => _x( 'Trail Segment', 'trail-segment' ),
        'singular_name' => _x( 'Trail Segment', 'trail-segment' ),
        'add_new' => _x( 'Add New', 'trail-segment' ),
        'add_new_item' => _x( 'Add New Trail Segment', 'trail-segment' ),
        'edit_item' => _x( 'Edit Trail Segment', 'trail-segment' ),
        'new_item' => _x( 'New Trail Segment', 'trail-segment' ),
        'view_item' => _x( 'View Trail Segments', 'trail-segment' ),
        'all_items'          => __( 'All Trail Segments', 'trail-segment' ),
        'search_items' => _x( 'Search Trail Segments', 'trail-segment' ),
        'not_found' => _x( 'No Trail Segments found', 'trail-segment' ),
        'not_found_in_trash' => _x( 'No Trail Segments found in Trash', 'trail-segment' ),
        'parent_item_colon' => _x( 'Parent Trail Segments:', 'trail-segment' ),
        'menu_name' => _x( 'Trail Segments', 'trail-segment' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ), // 'custom-fields' , 'excerpt' 
        'taxonomies' => array(  'trail-segments' ), //, 'post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5004,
        'menu_icon' => 'dashicons-admin-site', //dashicons-location-alt 'dashicons-post-status', 'dashicons-media-audio'
        'has_archive' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'trail-segment', $args );
}


/**
* Taxonomy for Trail Story CPT categories
*/
add_action( 'init', 'register_txn_trail_segment' );

function register_txn_trail_segment() {

    $labels = array( 
        'name' => _x( 'Trail Segment', 'trail-segments' ),
        'singular_name' => _x( 'Trail Segment', 'trail-segments' ),
        'search_items' => _x( 'Search Trail Segments', 'trail-segments' ),
        'popular_items' => _x( 'Popular Trail Segments', 'trail-segments' ),
        'all_items' => _x( 'All Trail Segments', 'trail-segments' ),
        'parent_item' => _x( 'Parent Trail Segment', 'trail-segments' ),
        'parent_item_colon' => _x( 'Parent Trail Segment:', 'trail-segments' ),
        'edit_item' => _x( 'Edit Trail Segment', 'trail-segments' ),
        'update_item' => _x( 'Update Trail Segment', 'trail-segments' ),
        'add_new_item' => _x( 'Add New', 'trail-segments' ),
        'new_item_name' => _x( 'New Trail Segment', 'trail-segments' ),
        'separate_items_with_commas' => _x( 'Separate Trail Segments with commas', 'trail-segments' ),
        'add_or_remove_items' => _x( 'Add or remove Trail Segments', 'trail-segments' ),
        'choose_from_most_used' => _x( 'Choose from the most used Trail Segment', 'trail-segments' ),
        'menu_name' => _x( 'Trail Segments', 'trail-segments' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_tagcloud' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => array( 'slug' => 'trail-segments' ),
        'query_var' => true
    );

    register_taxonomy( 'trail-segments', array('trail-story', 'trail-condition', 'trail-route', 'itinerary', 'trail-segment' ), $args );

    /*if (! has_term( 'iata-trail-segment', 'trail-segments' )) {
        wp_insert_term( 'Segments','trail-segments', array('Ice Age Trail Segments', 'iata-trail-segment') );
    }*/

}
