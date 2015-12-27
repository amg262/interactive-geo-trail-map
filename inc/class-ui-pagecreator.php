<?php
/**
 * Creates the Front End form for users to create a Tail Story post
 * 
 * @package Geo Mashup Trail Story Add-On
*/
// Exit if accessed directly
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );


interface i_UIPageCreator {
    
}
/**
* PLUGIN SETTINGS PAGE
*/
class UIPageCreator {
      /**
       * Holds the values to be used in the fields callbacks
       */
  public $trail_story_options, $force_delete, $delete_db, $delete_posts;
 

    public function __construct() {

    }

    public function create_trail_page() {
      
      $args = array(
          'post_name' => 'trail-map',
          'post_type' => 'page',
          'post_status' => 'publish',
          'post_title'    => 'Trail Map',
          'post_content'  => '',
          'post_status'   => 'private',
          'post_author'   => 1,
        );

        $new_id = wp_insert_post( $args, true );

        return $new_id;

    }

    public function create_share_story_page() {
      $args = array(
        'post_name' => 'share-trail-story',
          'post_type' => 'page',
          'post_status' => 'publish',
          'post_title'    => 'Share Trail Story',
          'post_content'  => '',
          'post_status'   => 'private',
          'post_author'   => 1,
        );

        $new_id = wp_insert_post( $args, true );

        return $new_id;
    }

    public function create_trail_support_page() {
      $args = array(
        'post_name' => 'trail-map-support',
          'post_type' => 'page',
          'post_status' => 'publish',
          'post_title'    => 'Trail Map Support',
          'post_content'  => 'Hi!',
          'post_status'   => 'private',
          'post_author'   => 1,
        );

        $new_id = wp_insert_post( $args, true);

        return $new_id;
    }

    public function run_page_creator() {
      //UIPageCreator::create_trail_page();
      //UIPageCreator::create_share_story_page();
      //UIPageCreator::create_trail_support_page();
      return '';
    }
}

