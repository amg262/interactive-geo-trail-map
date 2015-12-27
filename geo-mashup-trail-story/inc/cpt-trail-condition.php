<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/**
* Allows chapters and administrators to edit and publish conditions
*/
add_action( 'admin_init', 'add_trail_condition_cap');

function add_trail_condition_cap() {
    $admins = get_role( 'chapter' );
    $admins->add_cap( 'edit_trail_condition' ); 
    $admins->add_cap( 'edit_trail_conditions' ); 
    $admins->add_cap( 'edit_other_trail_condition' ); 
    $admins->add_cap( 'publish_trail_conditions' ); 
    $admins->add_cap( 'read_trail_condition' ); 
    $admins->add_cap( 'delete_trail_condition' ); 
    //$admins->add_cap( 'delete_gallery' ); 

    $admins = get_role( 'administrator' );
    $admins->add_cap( 'edit_trail_condition' ); 
    $admins->add_cap( 'edit_trail_conditions' ); 
    $admins->add_cap( 'edit_other_trail_condition' ); 
    $admins->add_cap( 'publish_trail_conditions' ); 
    $admins->add_cap( 'read_trail_condition' ); 
    $admins->add_cap( 'delete_trail_condition' ); 
}


/*
* Custom Post Type for Trail Condition
*/
add_action( 'init', 'register_cpt_trail_condition' );

function register_cpt_trail_condition() {

    $count = wp_count_posts('trail-condition');
    $pending_count = $count->pending;

    $labels = array( 
        'name' => _x( 'Trail Condition', 'trail-condition' ),
        'singular_name' => _x( 'Trail Condition', 'trail-condition' ),
        'add_new' => _x( 'Add New', 'trail-condition' ),
        'add_new_item' => _x( 'Add New Trail Condition', 'trail-condition' ),
        'edit_item' => _x( 'Edit Trail Condition', 'trail-condition' ),
        'new_item' => _x( 'New Trail Condition', 'trail-condition' ),
        'all_items'          => __( 'All Conditions', 'trail-condition' ),
        'view_item' => _x( 'View Trail Condition', 'trail-condition' ),
        'search_items' => _x( 'Search Trail Conditions', 'trail-condition' ),
        'not_found' => _x( 'No Trail Conditions found', 'trail-condition' ),
        'not_found_in_trash' => _x( 'No Trail Conditions found in Trash', 'trail-condition' ),
        'parent_item_colon' => _x( 'Parent Trail Conditions:', 'trail-condition' ),
        'menu_name' => _x( 'Conditions', 'trail-condition' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ), // 'custom-fields' , 'excerpt' 
        'taxonomies' => array( 'trail-conditions'  ), //, 'post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5002,
        'menu_icon' => 'dashicons-cloud',//'dashicons-controls-volumeon', //'dashicons-media-audio',
        'has_archive' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        //'capability_type' => 'post',
        'capabilities' => array(
            'read' => 'read',
            'edit_post' => 'edit_trail_condition',
            'edit_posts' => 'edit_trail_conditions',
            'edit_others_posts' => 'edit_other_trail_condition',
            'publish_posts' => 'publish_trail_conditions',

            //'read_post' => 'read_trail_condition',
            'delete_post' => 'delete_trail_condition'
        ), 
    );

    register_post_type( 'trail-condition', $args );
}


/**
* Taxonomy for Trail Condition CPT categories
*/
add_action( 'init', 'register_txn_trail_conditions' );

function register_txn_trail_conditions() {

    $labels = array( 
        'name' => _x( 'Trail Condition Category', 'trail-conditions' ),
        'singular_name' => _x( 'Trail Condition Category', 'trail-conditions' ),
        'search_items' => _x( 'Search Trail Condition Categories', 'trail-conditions' ),
        'popular_items' => _x( 'Popular Trail Condition Categories', 'trail-conditions' ),
        'all_items' => _x( 'All Trail Condition Categories', 'trail-conditions' ),
        'parent_item' => _x( 'Parent Trail Condition Category', 'trail-conditions' ),
        'parent_item_colon' => _x( 'Parent Trail Condition Category:', 'trail-conditions' ),
        'edit_item' => _x( 'Edit Trail Condition Category', 'trail-conditions' ),
        'update_item' => _x( 'Update Trail Condition Category', 'trail-conditions' ),
        'add_new_item' => _x( 'Add New', 'trail-conditions' ),
        'new_item_name' => _x( 'New Trail Condition Category', 'trail-conditions' ),
        'separate_items_with_commas' => _x( 'Separate Trail Condition Categories with commas', 'trail-conditions' ),
        'add_or_remove_items' => _x( 'Add or remove Trail Condition Categories', 'trail-conditions' ),
        'choose_from_most_used' => _x( 'Choose from the most used Trail Condition Category', 'trail-conditions' ),
        'menu_name' => _x( 'Categories', 'trail-conditions' ),
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
        'rewrite' => array( 'slug' => 'condition-category' ),
        'query_var' => true
    );

    register_taxonomy( 'trail-conditions', array('trail-condition'), $args );


    if (! has_term( 'iata-conditions', 'trail-conditions' )) {
        wp_insert_term( 'Trail Conditions','trail-conditions', array('Weather & Trail Condiitons', 'iata-conditions') );
    }

    /*if (! has_term( 'iata-trail-route', 'trail-conditions' )) {
        wp_insert_term( 'Trail Route Info','trail-conditions', array('Ice Age Trail Route info', 'iata-trail-route') );
    }*/
}