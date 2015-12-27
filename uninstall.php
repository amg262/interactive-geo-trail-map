<?php // Get out!
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();


interface iTrailMapUninstaller {
    
}
/**
* PLUGIN SETTINGS PAGE
*/
class TrailMapUninstaller {
	    /**
	     * Holds the values to be used in the fields callbacks
	     */ 
	public $trail_story_options, $force_delete, $delete_db, $delete_posts;
	
	//$force_delete = true;
	public function __construct()
    {
    	//global $trail_story_options;
    	$trail_story_options = (array) get_option( 'trail_story_options' );
    	//delete_posts
    	//$force_delete = True;
    }

	public function delete_cpts($delete_posts, $force_delete) {

		$args = array(
			'numberposts' => -1,
			'post_type' => 'trail-route',
			'post_status' => 'any'
		);

		$posts = get_posts( $args );

		if (is_array($posts)) {

		   foreach ($posts as $post) {

		       wp_delete_post( $post->ID, $force_delete);
		       echo "Deleted Post: ".$post->title."\r\n";
		   }
		}

		$args = array(
			'numberposts' => -1,
			'post_type' => 'trail-story',
			'post_status' => 'any'
		);

		$posts = get_posts( $args );

		if (is_array($posts)) {

		   foreach ($posts as $post) {

		       wp_delete_post( $post->ID, $force_delete);
		       echo "Deleted Post: ".$post->title."\r\n";
		   }
		}

		$args = array(
			'numberposts' => -1,
			'post_type' => 'trail-condition',
			'post_status' => 'any'
		);

		$posts = get_posts( $args );

		if (is_array($posts)) {

		   foreach ($posts as $post) {

		       wp_delete_post( $post->ID, $force_delete);
		       echo "Deleted Post: ".$post->title."\r\n";
		   }
		}

		$args = array(
			'numberposts' => -1,
			'post_type' => 'itinerary',
			'post_status' => 'any'
		);

		$posts = get_posts( $args );

		if (is_array($posts)) {

		   foreach ($posts as $post) {

		       wp_delete_post( $post->ID, $force_delete);
		       echo "Deleted Post: ".$post->title."\r\n";
		   }
		}

	}

	public function run_delete_cpt() {
				//var_dump(get_option($trail_story_options["delete_posts"]));
		if (isset($trail_story_options["delete_posts"])) {

		//if ($delete_posts == true) {
			TrailMapUninstaller::delete_cpts();
		//}
		}
		return 'cpts deleted';
	}
	

	/**
	 * Drop Geo Mashup database tables.
	 * 
	 * @since 1.3
	 * @access public
	 */
	public function geo_mashup_uninstall_db() {
		global $wpdb;
		$tables = array( 'geo_mashup_administrative_names', 'geo_mashup_location_relationships', 'geo_mashup_locations' );
		foreach( $tables as $table ) {
			$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . $table );
		}
	}

	/**
	 * Delete Geo Mashup saved options.
	 * 
	 * @since 1.3
	 * @access public
	 */
	public function geo_mashup_uninstall_options() {
		delete_option( 'geo_mashup_temp_kml_url' );
		delete_option( 'geo_mashup_db_version' );
		delete_option( 'geo_mashup_activation_log' );
		delete_option( 'geo_mashup_options' );
		delete_option( 'geo_locations' );
		// Leave the google_api_key option 
		// Still belongs to this site, and may be used by other plugins
	}


	public function delete_etd_tables() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}etd_manager" );
	}

	

	//note in multisite looping through blogs to delete options on each blog does not scale. You'll just have to leave them.
	/*
	* Getting options groups
	*/
	public function delete_plugin_options() {
		$option_name = 'trail_story_options';
		delete_option( $option_name );
		delete_site_option( $option_name );

		$option_2 = 'trail_story_user_options';
		delete_option( $option_2 );
		delete_site_option( $option_2 );
	}

	public function run_data_uninstaller() {
		$del = 'delete_dbo';
		$data = get_option('trail_story_options');
		$newdata = $data[$del];

		//echo 'echo ';
		//var_dump($newdata);

		if ($newdata != "") {
		//if (isset($trail_story_options["delete_dbo"])) {
			//TrailMapUninstaller::geo_mashup_uninstall_db();
			//TrailMapUninstaller::geo_mashup_uninstall_options();
			//TrailMapUninstaller::delete_etd_tables();
			//TrailMapUninstaller::delete_plugin_options();
		}
		return "data uninstalled";
	}

}

if( is_admin() )
    $uninstaller = new TrailMapUninstaller();
    $uninstaller->run_delete_cpt();
    $uninstaller->run_data_uninstaller();
/*
* For site options in multisite

delete_site_option( $option_name );  


$option_name_2 = 'etd_settings_option_key';
/*
* Delee options

delete_option( $option_name_2 );

/*
* For site options in multisite

delete_site_option( $option_name_2 );*/
