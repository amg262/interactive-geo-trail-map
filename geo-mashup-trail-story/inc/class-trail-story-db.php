<?php
/**
 * Creates the Front End form for users to create a Tail Story post
 * 
 * @package Geo Mashup Trail Story Add-On
*/
// Exit if accessed directly
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );


interface iTrailStoryDB {
    
}
/**
* PLUGIN SETTINGS PAGE
*/
class TrailStoryDB {
      /**
       * Holds the values to be used in the fields callbacks
       */
private $trail_story_options;
 private $_db;
 private $_tbl_name;
        
        /**
         * The table name where emails will be saved
         * @since 1.0.1
         *
         * @access private
         */
        

    public function __construct() {
        global $wpdb;
        $this->_db = $wpdb;
        $this->_tbl_name = $this->_db->prefix . 'gtm_postmeta';
        
        add_action( 'admin_init', array( &$this, 'install_db' ) );
        //add_action( 'save_post')
    }
    public function install_db() {
      //public function install_required_tables() {
        $charset_collate = $this->_db->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->_tbl_name} (
          id INT(200) NOT NULL AUTO_INCREMENT,
          post_id INT(200),
          primary_time DATETIME,
          name VARCHAR(200),
          email_phone VARCHAR(200),
          is_newsletter VARCHAR(20),
          is_email_list VARCHAR(20),
          is_media_release VARCHAR(20),
          is_active VARCHAR(20) DEFAULT 'on',
          UNIQUE KEY id (id)
        )";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
   // }
    }
    public function create_gtm_postmeta( $post_id, $name, $email_phone, $is_newsletter, 
                                          $is_email_list, $is_media_release) {
      global $wpdb, $table;
     // $tbl_name = 'gtm_postmeta';
      $table = $wpdb->prefix . 'gtm_postmeta';

      $wpdb->insert($table, 
          array( 
              'post_id' => $post_id,
              'primary_time' => current_time( 'mysql' ),
              'name' => $name,
              'email_phone' => $email_phone,
              'is_newsletter' => $is_newsletter,
              'is_email_list' => $is_email_list,
              'is_media_release' => $is_media_release,
              'is_active' => 'on'
          ));
      
      var_dump($wpdb->insert_id);
       ///die();
    }
    //public function create_record_data($post_id, ) {
  
}

$story_db = new TrailStoryDB();


