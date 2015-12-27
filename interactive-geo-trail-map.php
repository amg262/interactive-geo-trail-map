<?php 
/*
                                      `..,,,,..`           
                                `....,,,,,,,,,,,,:::`      
                             ........,,,,.`       `.:::`   
                          `..........                   ,` 
                       ```.......`      `.,,,,,,::,.       
                     `````.....     `,,,,,,,,,,,::::::,    
                   ```````..`    `...,,,,,,,,,,,::::::::,  
                 ``````````    ......,,,,,,,,,,,:::::::::, 
               ``````````    ........,`          .:::::::: 
               ````````    ........                :::::::.
               ```````    .......                  ::::::::
               ``````   ``.....`                   ::::::::
               `````   ```....                     :::::::,
               ````    ``....        `            :::::::: 
              ````    ```....        `,`         ::::::::: 
              ```     ```....        ,,,        :::::::::. 
              ```    ````....`     `,,,,       ::::::::::  
              ```     ```...........,,,,     .,:::::::::   
              ```     ```...........,,`     ,,,::::::::    
              ```      ``...........`     ,,,,,:::::::`    
              ```       `........`      ,,,,,,,::::::      
              ````                    ,,,,,,,,,:::::       
              `````                .,,,,,,,,,,,:::,        
              ```````          ....,,,,,,,,,,,,::          
              ``````````...........,,,,,,,,,,,,.           
              ``````````...........,,,,,,,,,,.             
             ```````````...........,,,,,,,,.               
             ```````````...........,,,,,,.                 
             ```````````...........,,,,                    
             ```````````...........,`                      
             `        ``.      ..                          
   .;;;;;;,    ;;;;;;:  ` ,;;;    .;;;;;;,    ;;;;    .;;; 
 :;;;;;;;;;;;  ;;;;;;;;;  ,;;;  :;;;;;;;;;;;  ;;;;;:  .;;; 
,;;;,    .;;;; ;;;. ;;;;  ,;;; .;;;,    `;;;; ;;;;;;;`.;;; 
;;;;      ;;;; ;;;;;;;:   ,;;; :;;;      ;;;; ;;;;:;;;;;;; 
 ;;;;:..:;;;;` ;;;:;;;;   ,;;;  ;;;;:..:;;;;` ;;;;  ;;;;;; 
  :;;;;;;;;;   ;;;. ;;;;. ,;;;   :;;;;;;;;;   ;;;;   `;;;; 

----------------------------------------------------------
----------------------------------------------------------

* Plugin Name: Interactive Geo Trail Map
* Plugin URI:  https://andrewgunn.xyz/interactive-geo-trail-map
* Version:     1.0.0
* Description: A fully loaded interactive geo map maker with functionality to allow users to submit geo located trail story and condition posts, require email to download trail itinerary documents, and custom pins and features tailored interactive trail maps.
* Author:      Orion Group LLC
* Author URI:  http://www.orionweb.net
* License:     GNU General Public License (Version 2 - GPLv2)
* Text Domain: interactive-geo-trail-map
*/
if ( ! defined( 'ABSPATH' ) ) die( 'No way, jose!' );

/**
 * Defining constants
 */
require_once( 'inc/class-ui-pagecreator.php' );
require_once( dirname( __FILE__ ).'/geo-mashup-trail-story/geo-mashup-trail-story.php' );
require_once( dirname( __FILE__ ).'/geo-mashup/geo-mashup.php' );
require_once( dirname( __FILE__ ).'/geo-mashup-custom/geo-mashup-custom.php' );
require_once( dirname( __FILE__ ).'/email-to-download-itinerary/email-to-download-itinerary.php' );
define( 'IGTM_NAME', 'interactive-geo-trail-map' );
//define( 'IGTM_PDF_URL', 'http://andrewgunn.xyz/wp-content/uploads/2015/10/Interactive-Geo-Trail-Map.pdf' ); 
/**
* Flushing permalinks for CPTs on DEACTIVATE
*/
//register_deactivation_hook( __FILE__, 'flush_permalinks' );

/**
* Flushing permalinks for CPTs ON ACTIVATE
*/
register_activation_hook( __FILE__, 'setup_plugin_data' );
//register_activation_hook( __FILE__, 'create_misc_pages' );
//add_action( 'admin_init', 'flush_permalinks' );


function setup_plugin_data() {

 // $key = 'delete_posts';
 // $term = get_option('trail_story_options');
 // $var = $term[$key];

  //if ($var != "") {
    //var_dump(get_option('trail_story_options'));
 //   UIPageCreator::run_page_creator();
  //}
  flush_rewrite_rules();
}

/**
* Adding Settings link to plugin page
*/
add_filter( 'plugin_action_links', 'igtm_plugin_links', 10, 5 );

function igtm_plugin_links( $actions, $plugin_file )
{
	static $plugin;

	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);

		if ($plugin == $plugin_file) {

			$settings = array(
							  //'stories' => '<a href="edit.php?post_type=trail-story">' . __('Stories', 'General') . '</a>',
							  //'conditions' => '<a href="edit.php?post_type=trail-condition">' . __('Conditions', 'General') . '</a>',
							  //'docs' => '<a href="./docs/Interactive-Geo-Trail-Map.pdf/' ).'">' . __('Docs', 'General') . '</a>',
                //'help' => '<a href="/trail-map-support/">' . __('Help', 'General') . '</a>',
                //'support' => '<a href="edit.php">' . __('Support', 'General') . '</a>',
                //'trail_map' => '<a target="_blank" href="/trail-map/">' . __('View Map', 'General') . '</a>',
                'map' => '<a href="admin.php?page=interactive-geo-trail-map%2Fgeo-mashup%2Fgeo-mashup.php">' . __('Map', 'General') . '</a>',
							  'settings' => '<a href="admin.php?page=geo-trail-map">' . __('Settings', 'General') . '</a>',

							  );

    			$actions = array_merge($settings, $actions);
		}

		return $actions;
}

