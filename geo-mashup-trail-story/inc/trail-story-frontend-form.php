<?php
/**
 * Creates the Front End form for users to create a Tail Story post
 * 
 * @package Geo Mashup Trail Story Add-On
*/
// Exit if accessed directly
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/**
* Shortcode for display frontend user trail story input
*/
add_shortcode( 'frontend_trail_story_map', 'trail_story_post_form_handler' );
//add_action( 'wp_footer', 'trail_story_post_g_form_handler', 10 );


function trail_story_post_form_handler(){

    GeoMashupPostUIFrontend::get_instance()->GeoMashupPostUIFrontend();

    trail_story_post_form();

}
/**
* Function to print the front end user post form
*/
function trail_story_post_form() {
    // Create form 
    GeoMashupPostUIFrontend::get_instance()->print_form();

}

/**
 * A base class for managing user interfaces for collecting and storing location.
 *
 * This could be extended to make the existing editor work for new objects in a separate plugin.
 *
 * @package GeoMashup
 * @since 1.3
 * @access public
 */
class GeoMashupUIFrontend {
	/**
	 * Retrieve a single instaniated subclass by name.
	 *
	 * @since 1.3
	 * @access public
	 * @static
	 *
	 * @param string $name The class name of the manager.
	 * @return GeoMashupUIFrontend The singleton object.
	 */
	function &get_instance( $name ) {
		static $instances = array();

		if ( ! isset( $instances[$name] ) ) {
			$instances[$name] = new $name();
		}
		return $instances[$name];
	}

	/**
	 * Queue UI styles to match the jQuery version.
	 * 
	 * @since 1.3
	 * @access public
	 */
	function enqueue_jquery_styles() {
		wp_enqueue_style( 'jquery-smoothness', trailingslashit( GEO_MASHUP_URL_PATH ) . 'jquery-ui.1.7.smoothness.css', false, GEO_MASHUP_VERSION, 'screen' );
	}

	/**
	 * Queue styles and scripts for the location editor form.
	 *
	 * @since 1.3
	 * @access public
	 */
	function enqueue_form_client_items() {	

		wp_enqueue_style( 'geo-mashup-edit-form', trailingslashit( GEO_MASHUP_URL_PATH ) . 'edit-form.css', false, GEO_MASHUP_VERSION, 'screen' );

		wp_enqueue_script( 'google-jsapi' );
		wp_enqueue_script( 'geo-mashup-location-editor', 
			GEO_MASHUP_URL_PATH . '/geo-mashup-location-editor.js', 
			array( 'jquery', 'google-jsapi' ), 
			GEO_MASHUP_VERSION );
	}

	/**
	 * Determine the appropriate action from posted data.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function get_submit_action() {

		$action = null;

		if ( isset( $_POST['geo_mashup_add_location'] ) or isset( $_POST['geo_mashup_update_location'] ) ) {

			// Clients without javascript may need server side geocoding
			if ( ! empty( $_POST['geo_mashup_search'] ) and isset( $_POST['geo_mashup_no_js'] ) and 'true' == $_POST['geo_mashup_no_js'] ) {

				$action = 'geocode';

			} else {

				$action = 'save';

			}

		} else if ( isset( $_POST['geo_mashup_changed'] ) and 'true' == $_POST['geo_mashup_changed'] and ! empty( $_POST['geo_mashup_location'] ) ) {

			// The geo mashup submit button wasn't used, but a change was made and the post saved
			$action = 'save';
				 
		} else if ( isset( $_POST['geo_mashup_delete_location'] ) ) {

			$action = 'delete';

		} else if ( ! empty( $_POST['geo_mashup_location_id'] ) and empty( $_POST['geo_mashup_location'] ) ) {

			// There was a location, but it was cleared before this save
			$action = 'delete';

		}
		return $action;
	}

	/**
	 * Save an object location from data posted by the location editor.
	 * 
	 * @since 1.3
	 * @access public
	 * @uses GeoMashupDB::set_object_location()
	 * @uses GeoMashupDB::delete_location()
	 *
	 * @param string $object_name The name of the object being edited.
	 * @param string $object_id The ID of the object being edited.
	 * @return bool|WP_Error True or a WordPress error.
	 */
	function save_posted_object_location( $object_name, $object_id ) {

		// Check the nonce
		if ( empty( $_POST['geo_mashup_nonce'] ) || !wp_verify_nonce( $_POST['geo_mashup_nonce'], 'geo-mashup-edit' ) ) {
			return new WP_Error( 'invalid_request', __( 'Object location not saved - invalid request.', 'GeoMashup' ) );
		}
		
		$action = $this->get_submit_action();

		if ( 'save' == $action or 'geocode' == $action ) {

			$date_string = $_POST['geo_mashup_date'] . ' ' . $_POST['geo_mashup_hour'] . ':' . 
				$_POST['geo_mashup_minute'] . ':00';
			$geo_date = date( 'Y-m-d H:i:s', strtotime( $date_string ) );

			$post_location = array();
			// If PHP has added slashes, WP will do it again before saving
			$post_location['saved_name'] = stripslashes( $_POST['geo_mashup_location_name'] );

			if ( 'geocode' == $action ) {

				$status = GeoMashupDB::geocode( $_POST['geo_mashup_search'], $post_location );
				if ( $status != 200 ) {
					$post_location = array();
				}

			} else {

				if ( ! empty( $_POST['geo_mashup_select'] ) ) {
					$selected_items = explode( '|', $_POST['geo_mashup_select'] );
					$post_location = intval( $selected_items[0] );
				} else { 
					$post_location['id'] = $_POST['geo_mashup_location_id'];
					list( $lat, $lng ) = split( ',', $_POST['geo_mashup_location'] );
					$post_location['lat'] = trim( $lat );
					$post_location['lng'] = trim( $lng );
					$post_location['geoname'] = $_POST['geo_mashup_geoname'];
					$post_location['address'] = stripslashes( $_POST['geo_mashup_address'] );
					$post_location['postal_code'] = $_POST['geo_mashup_postal_code'];
					$post_location['country_code'] = $_POST['geo_mashup_country_code'];
					$post_location['admin_code'] = $_POST['geo_mashup_admin_code'];
					$post_location['sub_admin_code'] = $_POST['geo_mashup_sub_admin_code'];
					$post_location['locality_name'] = $_POST['geo_mashup_locality_name'];
				}
			}
			
			if ( ! empty( $post_location ) ) {
				$error = GeoMashupDB::set_object_location( $object_name, $object_id, $post_location, true, $geo_date );
				if ( is_wp_error( $error ) ) 
					return $error;
			}

		} else if ( 'delete' == $action ) {

			$error = GeoMashupDB::delete_object_location( $object_name, $object_id );
			if ( is_wp_error( $error ) ) 
				return $error;

		} 

		return true;
	}
	
	/*function front_end_map() {
		// A lots of Stuff
	
		if ( isset($_POST['geo_mashup_add_location']) ) {
			
			print "SUBMIT";
			print "<BR>geo_mashup_add_location: ".$_POST['geo_mashup_add_location'];
			print "<BR>geo_mashup_search: ".$_POST['geo_mashup_search'];
			print "<BR>geo_mashup_no_js: ".$_POST['geo_mashup_no_js'];
			print "<BR>geo_mashup_changed: ".$_POST['geo_mashup_changed'];
			print "<BR>geo_mashup_location: ".$_POST['geo_mashup_location'];
			

			GeoMashupUIFrontend::get_submit_action();
			
		} else {
			
			include_once( GEO_MASHUP_DIR_PATH . '/edit-form.php');
			
			//print "<form action='".get_page_link(25)."' method='post'>";
			print "<form action='".get_page_link(25)."' method='post'>";
			print "	<input type='submit' name='submit' value='Enviar' />";
			geo_mashup_edit_form( 'post', 'front_map');
			print "	<input type='hidden' name='page_id' value='25' />";
			
			print "	<input id='geo_mashup_add_location' name='geo_mashup_add_location' type='submit'  value='Add Location' />";
			print "	<input id='geo_mashup_delete_location' name='geo_mashup_delete_location' type='submit' style='display:none;' value='Delete' />";
			print "	<input id='geo_mashup_update_location' name='geo_mashup_update_location' type='submit' style='display:none;' value='Save' />";
			
			print "</form>";
		
		}
	}*/

}

/**
 * A manager for user location user interfaces.
 *
 * Singleton instantiated immediately.
 *
 * @package GeoMashup
 * @since 1.3
 * @access public
 */
class GeoMashupUserUIFrontend extends GeoMashupUIFrontend {
	/**
	 * Get the single instance of this class.
	 * 
	 * @since 1.3
	 * @access public
	 * @static
	 * @uses parent::get_instance()
	 *
	 * @return GeoMashupPostUIFrontend The instance.
	 */
	function get_instance() {
		return parent::get_instance( 'GeoMashupUserUIFrontend' );
	}

	/**
	 * PHP4 Constructor
	 *
	 * @since 1.3
	 * @access private
	 */
	function GeoMashupUserUIFrontend() {
		// Global $geo_mashup_options is available, but a better pattern might
		// be to wait until init to be sure
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Initialize for use in relevant requests.
	 *
	 * init {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by WordPress.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function init() {
		global $geo_mashup_options;

		// Enable this interface when the option is set and we're on a destination page
		$enabled = is_admin() &&
		$geo_mashup_options->get( 'overall', 'located_object_name', 'user' ) == 'true' &&
		preg_match( '/(user-edit|user-new|profile).php/', $_SERVER['REQUEST_URI'] );

		// If enabled, register all the interface elements
		if ( $enabled ) { 

			// Form generation
			add_action( 'show_user_profile', array( &$this, 'print_form' ) );
			add_action( 'edit_user_profile', array( &$this, 'print_form' ) );
			// MAYBEDO: add location to registration page?

			// Form processing
			add_action( 'personal_options_update', array( &$this, 'save_user'));
			add_action( 'edit_user_profile_update', array( &$this, 'save_user'));

			$this->enqueue_jquery_styles();
			$this->enqueue_form_client_items();
		}
	}

	/**
	 * Print the user location editor form.
	 * 
	 * @since 1.3
	 * @access public
	 * @uses edit-form.php
	 */
	function print_form() {
		global $user_id;

		include_once( GEO_MASHUP_DIR_PATH . '/edit-form.php');

		if ( isset( $_GET['user_id'] ) ) {
			$object_id = $_GET['user_id'];
		} else {
			$object_id = $user_id;
		}
		echo '<h3>' . __( 'Location', 'GeoMashup' ) . '</h3>';
		geo_mashup_edit_form( 'user', $object_id, get_class( $this ) );
	}

	/**
	 * Save a posted user location.
	 * 
	 * @since 1.3
	 * @access public
	 * @uses parent::save_posted_object_location()
	 *
	 * @param id $user_id 
	 * @return bool|WP_Error
	 */
	function save_posted_object_location( $user_id ) {
		return parent::save_posted_object_location( 'user', $user_id );
	}

	/**
	 * When a user is saved, also save any posted location.
	 *
	 * save_user {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by WordPress.
	 * 
	 * @since 1.3
	 * @access private
	 * @return bool|WP_Error 
	 */
	function save_user() {
		if ( empty( $_POST['user_id'] ) ) {
			return false;
		}

		$user_id = $_POST['user_id'];

		if ( !is_numeric( $user_id ) ) {
			return $user_id;
		}

		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return $user_id;
		}

		return $this->save_posted_object_location( $user_id );
	}
}

// Instantiate
GeoMashupUserUIFrontend::get_instance();

/**
 * A manager for post/page location user interfaces.
 *
 * Singleton instantiated immediately.
 *
 * @package GeoMashup
 * @since 1.3
 * @access public
 */
class GeoMashupPostUIFrontend extends GeoMashupUIFrontend {
	/**
	 * Location found in geo_mashup_save_location shortcode.
	 * 
	 * @since 1.3
	 * @var array
	 * @access private
	 */
	var $inline_location;

	/**
	 * Get the single instance of this class.
	 * 
	 * @since 1.3
	 * @access public
	 * @static
	 * @uses parent::get_instance()
	 *
	 * @return GeoMashupPostUIFrontend The instance.
	 */
	function get_instance() {
		return parent::get_instance( 'GeoMashupPostUIFrontend' );
	}

	/**
	 * PHP4 Constructor
	 *
	 * @since 1.3
	 * @access private
	 */
	function GeoMashupPostUIFrontend() {
		// Global $geo_mashup_options is available, but a better pattern might
		// be to wait until init to be sure
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Initialize for use in relevant requests.
	 *
	 * init {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by WordPress.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function init() {
		global $geo_mashup_options;

		// Uploadable geo content type expansion always enabled
		add_filter( 'upload_mimes', array( &$this, 'upload_mimes' ) );

		// Enable this interface when the option is set 
		$enabled = $geo_mashup_options->get( 'overall', 'located_object_name', 'post' ) == 'true';
		
		if ( $enabled ) { 
			
			
			//CARREGA DEPENDÊNCIAS (CSS E JS)
			include_once( GEO_MASHUP_DIR_PATH . '/edit-form.php');
			$this->enqueue_jquery_styles();
			$this->enqueue_form_client_items();
			wp_enqueue_script( 'jquery-ui-datepicker', trailingslashit( GEO_MASHUP_URL_PATH ) . 'jquery-ui.1.7.datepicker.min.js', array( 'jquery', 'jquery-ui-core'), '1.7' );
			

// Queue inline location handlers

			// Pre-save filter checks saved content for inline location tags
			add_filter( 'content_save_pre', array( &$this, 'content_save_pre') );

			// Save post handles both inline and form processing
			add_action( 'save_post', array( &$this, 'save_post'), 10, 2 );

			// Browser upload processing
			add_filter( 'wp_handle_upload', array( &$this, 'wp_handle_upload' ) );

			// If we're on a post editing page, queue up the form interface elements
			if ( is_admin() && preg_match( '/(post|page)(-new|).php/', $_SERVER['REQUEST_URI'] ) ) {

				// Form generation
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

				$this->enqueue_jquery_styles();
				$this->enqueue_form_client_items();

				wp_enqueue_script( 'jquery-ui-datepicker', trailingslashit( GEO_MASHUP_URL_PATH ) . 'jquery-ui.1.7.datepicker.min.js', array( 'jquery', 'jquery-ui-core'), '1.7' );

			} else if ( strpos( $_SERVER['REQUEST_URI'], 'async-upload.php' ) > 0 ) {

				// Flash upload display
				add_filter( 'media_meta', array( &$this, 'media_meta' ), 10, 2 );

			} else if ( strpos( $_SERVER['REQUEST_URI'], 'upload.php' ) > 0 ) {

				// Browser upload display
				add_action( 'admin_print_scripts', array( &$this, 'admin_print_scripts' ) );

			}
		} // end if enabled
	}

	/**
	 * Add a location meta box to the post and page editors.
	 * 
	 * admin_menu {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by Wordpress.
	 *
	 * @since 1.3
	 * @access private
	 */
	function admin_menu() {
		// Not adding a menu, but at this stage add_meta_box is defined, so we can add the location form
		add_meta_box( 'geo_mashup_post_edit', __( 'Location', 'GeoMashup' ), array( &$this, 'print_form' ), 'post', 'advanced' );
		add_meta_box( 'geo_mashup_post_edit', __( 'Location', 'GeoMashup' ), array( &$this, 'print_form' ), 'page', 'advanced' );
	}
	/**
	 * Print the post editor form.
	 * 
	 * @since 1.3
	 * @access public
	 * @uses edit-form.php
	 */
	function print_post_form() {
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Front End Post') ) :
	    endif;		
	}

	/**
	 * Print the post editor form.
	 * 
	 * @since 1.3
	 * @access public
	 * @uses edit-form.php
	 */
	function print_form() {
		global $post_ID;
		global $user_ID;
		global $is_valid;
		global $is_created;
		global $is_error;

		//$kml_url = 'http://iceagetrail.staging.wpengine.com/wp-content/uploads/2015/11/iat-connector.kml';
    	include_once( GEO_MASHUP_DIR_PATH . '/edit-form.php' );
		
		/*$args = array('echo' => 0);
		wp_list_categories( $args );
		if ( $cats = wp_list_categories( $args ) ) {
			print $cats[0];
		}*/

		$trail_story_post_category = !is_array( $_POST['quick_post_cat'] ) ? array( $_POST['quick_post_cat'] ) : $_POST['quick_post_cat'];
		$trail_story_user_id = !is_null( $user_ID ) ? $user_ID : '';
		
		if ( $_POST['geo_mashup_add_location'] ) {
			$post = array(
				'post_author'		=> $user_ID, //The user ID number of the author.
				//'post_category'		=> $trail_story_post_category, //Add some categories. Apparently doesn't work
				'post_content'		=> $_POST['quick_post_content'], //The full text of the post.
				'post_date'			=> date_i18n( 'Y-m-d H:i:s' ), //The time post was made.
				//'post_date_gmt'	=> Y-m-d H:i:s, //The time post was made, in GMT.
				'post_status' 		=> 'pending', //Set the status of the new post. Pode ser acertada via Admin
				'post_title'		=> $_POST['quick_post_title'], //The title of your post.
				'post_type'			=> 'trail-story', //Sometimes you want to post a page.
				//'tags_input'		=> [  ], //For tags.
				'tax_input'		    => array( 'trail-story-category' => 'trail-stories' ) //For taxonomies.
			);
			
			//include_once( 'class-trail-story-db.php' );
			// Insert the post into the database
			$post_ID = wp_insert_post( $post );
			$is_valid = true;
			$term = get_term_by( 'slug', 'trail-stories', 'trail-story-category' );
			//var_dump($term);
			wp_set_post_terms( $post_ID, $term->term_id, 'trail-story-category');
			wp_set_post_terms( $post_ID, $_POST['quick_post_cat'], 'trail-segments');

			//include_once( 'class-trail-story-db.php' );
			//$gtm_db = new TrailStoryDB();
			//$my_class = new My_Plugin_Class; 
			//add_action( 'save_post', array( $my_class, 'save_posts_hook' ) );
			//public function create_gtm_postmeta( $post_id, $name, $email_phone, $is_newsletter, 
                                          //$is_email_list, $is_media_release) {

		       ///die();


		

			if ( isset( $_POST['story_post_thumbnail'], $_POST['post_id'] ) 
				&& wp_verify_nonce( $_POST['story_post_thumbnail'], 'upload_post_thumbnail' )) {
					// The nonce was valid and the user has the capabilities, it is safe to continue.

					// These files need to be included as dependencies when on the front end.
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
					
					// Let WordPress handle the upload.
					// Remember, 'my_image_upload' is the name of our file input in our form above.
					$attachment_id = media_handle_upload( 'story_post_thumbnail', $post_ID );
					
					if ( is_wp_error( $attachment_id ) ) {
						// There was an error uploading the image.

					} else {
						// The image was uploaded successfully!
					}

				}
					//$term = get_category_by_slug('trail-stories'); 
		  			//$id = $term->term_id;
				//if ($post_ID > 0) { wp_redirect(home_url()); }
			//wp_set_post_categories( $post_ID, $id );
			set_post_thumbnail( $post_ID, $attachment_id );
		}

		if (($post_ID != 0) && ($term->term_id != 0)) {
			global $wpdb;
		      global $table;
		      
		     // $tbl_name = 'gtm_postmeta';
		      $table = $wpdb->prefix . 'gtm_postmeta';

		      $wpdb->insert($table, 
		          array( 
		              'post_id' => $post_ID,
		              'primary_time' => current_time( 'mysql' ),
		              'name' => $_POST['quick_post_name'],
		              'email_phone' => $_POST['quick_post_email_phone'],
		              'is_newsletter' => $_POST['opt_news'],
		              'is_email_list' => $_POST['opt_email'],
		              'is_media_release' =>  $_POST['opt_media'],
		              'is_active' => 'on'
		          ));
		      
		      if (($wpdb->insert_id) > 0) {
		      	$is_created = true;
		      } else {
		      	$is_error = true;
		      }
	    }
		// Check that the nonce is valid, and the user can edit this post.
		//if (isset($_POST['submit'])) { wp_redirect( home_url().'/story-submitted' ); exit; }
			//var_dump($post62);
		//--- IF FORM - Ou algo que permita maior customização
		$filename[0] = TEMPLATEPATH . '/geo_mashup_ui_frontend_form.php';
		$filename[1] = GEO_MASHUP_DIR_PATH . '/geo_mashup_ui_frontend_form.php';
		
		if ( file_exists($filename[0]) ) {
			
			include_once($filename[0]);
			
		} elseif ( file_exists($filename[1]) ) {
			
			include_once($filename[1]);
			
		} else {
		?>
	<?php if (( $is_valid == true ) && ( $is_created == true ) &&
			 (($is_valid != null ) && ($is_created != null))) {
			$message .= '<p id="message"><span class="icon success">'.
						'&nbsp;</span><strong class="submit">'.
						'Your story has been submitted for approval!&nbsp;&nbsp;&nbsp;'.
						'<a href="/share-trail-story/">Reload&nbsp;&#x21bb;</a></strong></p>';
			echo $message;

		} elseif (($is_valid == true) && ($is_created == null)) {
			$message .= '<p id="message"><span class="icon non-success">'.
						'&nbsp;</span><strong class="submit">'.
						'<b>Invalid submission:</b> All form fields are required.&nbsp;&nbsp;&nbsp;'.
						'<a href="/share-trail-story/">Reload&nbsp;&#x21bb;</a></strong></p>';
			echo $message;
		} else {
			$message .= "";
		} 

		
		?>
<form id="trail-story-post" method="post" name="trail_story_post" enctype="multipart/form-data">
	<!--<input id="geo_mashup_kml_url" name="geo_mashup_kml_url" type="hidden" value="<?php echo $kml_url; ?>" />-->
	<br/>
	<fieldset name="name">
	    <!--<label for="quick_post_title" class="quick_post_label" style="">Title:</label>-->
	    <input type="text" name="quick_post_name" id="quick_post_name" style="" value="" placeholder="Your Name*"/>
    </fieldset>
    <fieldset name="email">
	    <!--<label for="quick_post_title" class="quick_post_label" style="">Title:</label>-->
	    <input type="text" name="quick_post_email_phone" id="quick_post_email_phone" style="" value="" placeholder="Email Address or Phone Number*"/>
    </fieldset>
	<fieldset name="name">
	    <!--<label for="quick_post_title" class="quick_post_label" style="">Title:</label>-->
	    <input type="text" name="quick_post_title" id="quick_post_title" style="" value="" placeholder="Story Title*"/>
    </fieldset>
    
    <!--<input type='button' id="quick_post_load" style="" value="Visual Editor" title="Visual Editor" /> -->
    <fieldset style="max-width:500px; width:100%" name="category">
	    <?php wp_dropdown_categories( 'tab_index=10&taxonomy=trail-segments&orderby=title&hide_empty=0&echo=1&id=&quick_post_cat&name=quick_post_cat' ); ?>
    
    </fieldset>
    <br>
    <fieldset name="content">
	    <!--<label for="quick_post_content" class="quick_post_label" style="">Content:</label>-->
	    <textarea name="quick_post_content" rows="4" id="quick_post_content" style="" placeholder="Describe your trail story*"></textarea>
    </fieldset>
 	<br/><br/>
	<input type="file" name="story_post_thumbnail" id="story_post_thumbnail" multiple="false" value="Upload Photos" />
	<input type="hidden" name="post_id" id="post_id" value="" />
	<?php wp_nonce_field( 'upload_post_thumbnail', 'story_post_thumbnail' ); ?>

<?php
?>
    <br/><br/>
    <?php //if (function_exists('wp_multi_file_uploader')) { wp_multi_file_uploader(); } ?>
    <?php //echo //do_shortcode('[wordpress_file_upload]'); ?>
    <div style="clear:both;height:0;"></div>
    <br/>
    <?php geo_mashup_edit_form( 'post', -1, get_class( $this ) ); ?>
    <br/><hr>

    <input type="checkbox" name="opt_news" id="opt_news" />&nbsp;<label style="display:inline;" for="opt_news">Yes, opt-in and receive our newsletter.</label><br>

    <input type="checkbox" name="opt_email" id="opt_email" />&nbsp;<label style="display:inline;" for="opt_email">Yes, send email announcements on Ice Age Trail Alliance news, volunteers events and more.</label><br>
    
    <input style="margin:0;" name="opt_media" id="opt_media" type="checkbox" />&nbsp;<label style="display:inline;" for="opt_media">By submitting this form, I agree to allow my photo, video or film likeness, or reproductions thereof made through any media, including electronic media, to be copyrighted and used for any legitimate purpose by the IATA or its assigns. To the fullest extent allowed by law, I hereby waive any rights of publicity or privacy that I may have in my name or likeness.</label>
    <br/>
    <fieldset name="submit">
    	<input type="submit" id="geo_mashup_add_location" style="margin-right:5%; border-radius:30px;" name="geo_mashup_add_location" value="Submit" title="Post" />
    </fieldset>

</form> 
		<?php
		} //--- IF FORM
	}


	/**
	 * Save a posted post or page location.
	 * 
	 * @since 1.3
	 * @access public
	 * @uses parent::save_posted_object_location()
	 *
	 * @param id $post_id 
	 * @return bool|WP_Error
	 */
	function save_posted_object_location( $post_id ) {
		return parent::save_posted_object_location( 'post', $post_id );
	}

	/**
	 * When a post is saved, save any posted location for it.
	 * 
	 * save_post {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by WordPress.
	 *
	 * @since 1.3
	 * @access private
	 * @uses GeoMashupDB::set_object_location()
	 *
	 * @param id $post_id 
	 * @param object $post 
	 * @return bool|WP_Error
	 */
	function save_post($post_id, $post) {
		if ( 'revision' == $post->post_type ) {
			return;
		}

		// WP has already saved the post - allow location saving without added capability checks
		if ( !empty( $this->inline_location ) ) {
			GeoMashupDB::set_object_location( 'post', $post_id, $this->inline_location );
			$this->inline_location = null;
		}


		update_option('geo_mashup_temp_kml_url','');

		return $this->save_posted_object_location( $post_id );
	}

	/**
	 * Extract inline save location shortcodes from post content before it is saved.
	 *
	 * content_save_pre {@link http://codex.wordpress.org/Plugin_API/Filter_Reference filter}
	 * called by Wordpress.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function content_save_pre( $content ) {
		// Piggyback on the shortcode interface to find inline tags [geo_mashup_save_location ...] 
		add_shortcode( 'geo_mashup_save_location', 'is_null' );
		$pattern = get_shortcode_regex( );
		return preg_replace_callback('/'.$pattern.'/s', array( &$this, 'replace_save_pre_shortcode' ), $content);
	}

	/**
	 * Store the inline location from a save location shortcode before it is removed.
	 * 
	 * @since 1.3
	 * @access private
	 *
	 * @param array $shortcode_match 
	 * @return The matched content, or an empty string if it was a save location shortcode.
	 */
	function replace_save_pre_shortcode( $shortcode_match ) {
		$tag_index = array_search( 'geo_mashup_save_location',  $shortcode_match ); 
		if ( $tag_index !== false ) {
			// There is an inline location - save the attributes
			$this->inline_location = shortcode_parse_atts( stripslashes( $shortcode_match[$tag_index+1] ) );
			// Remove the tag
			$content = '';
		} else {
			// Whatever was matched, leave it be
			$content = $shortcode_match[0];
		}
		return $content;
	}

	/**
	 * Add Flash-uploaded KML to the location editor map.
	 *
	 * media_meta {@link http://codex.wordpress.org/Plugin_API/Filter_Reference filter}
	 * called by WordPress.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function media_meta( $content, $post ) {
		// Only chance to run some javascript after a flash upload?

		if (strlen($post->guid) > 0) {
			$content .= '<script type="text/javascript"> ' .
				'if (parent.GeoMashupLocationEditor) parent.GeoMashupLocationEditor.loadKml(\''.$post->guid.'\');' .
				'</script>';
		}
		return $content;
	}

	/**
	 * Add Browser-uploaded KML to the location editor map.
	 *
	 * admin_print_scripts {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by WordPress.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function admin_print_scripts( $not_used ) {
		// Load any uploaded KML into the search map - only works with browser uploader
		
		// See if wp_upload_handler found uploaded KML
		$kml_url = get_option( 'geo_mashup_temp_kml_url' );
		//$kml_url = 'http://iceagetrail.staging.wpengine.com/wp-content/uploads/2015/11/iat-connector.kml';
		if (strlen($kml_url) > 0) {
			// Load the KML in the location editor
			echo '
				<script type="text/javascript"> 
					if (parent.GeoMashupLocationEditor) parent.GeoMashupLocationEditor.loadKml(\'' . $kml_url . '\');
				</script>';
			update_option( 'geo_mashup_temp_kml_url', '' );
		}
	}

	/**
	 * Add geo mime types to allowable uploads.
	 * 
	 * upload_mimes {@link http://codex.wordpress.org/Plugin_API/Filter_Reference filter}
	 * called by WordPress.
	 *
	 * @since 1.3
	 * @access private
	 */
	function upload_mimes( $mimes ) {
		
		$mimes['jpg|jpeg|jpe'] = 'image/jpeg';
		$mimes['gif'] = 'image/gif';
		$mimes['png'] = 'image/png';
		$mimes['kmz'] = 'application/vnd.google-earth.kmz';
		$mimes['gpx'] = 'application/octet-stream';
		return $mimes;
	}

	/**
	 * If an upload is KML, put the URL in an option to be loaded in the response 
	 * 
	 * wp_handle_upload {@link http://codex.wordpress.org/Plugin_API/Filter_Reference filter}
	 * called by WordPress.
	 *
	 * @since 1.3
	 * @access private
	 */
	function wp_handle_upload( $args ) {
		// TODO: use transient API instead of option
		update_option( 'geo_mashup_temp_kml_url', '' );
		if ( is_array( $args ) && isset( $args['file'] ) ) {
			if ( stripos( $args['file'], '.kml' ) == strlen( $args['file'] ) - 4 ) {
				update_option( 'geo_mashup_temp_kml_url', $args['url'] );
			}
		}
		return $args;
	}
}

// Instantiate
GeoMashupPostUIFrontend::get_instance();

/**
 * A manager for comment location user interfaces.
 *
 * Singleton instantiated immediately.
 *
 * @package GeoMashup
 * @since 1.3
 * @access public
 */
class GeoMashupCommentUIFrontend {
	/**
	 * Get the single instance of this class.
	 * 
	 * @since 1.3
	 * @access public
	 * @static
	 *
	 * @return GeoMashupPostUIFrontend The instance.
	 */
	function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new GeoMashupCommentUIFrontend();
		}
		return $instance;
	}

	/**
	 * PHP4 Constructor
	 *
	 * @since 1.3
	 * @access private
	 */
	function GeoMashupCommentUIFrontend() {
		// Global $geo_mashup_options is available, but a better pattern might
		// be to wait until init to be sure
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Initialize for use in relevant requests.
	 *
	 * init {@link http://codex.wordpress.org/Plugin_API/Action_Reference action}
	 * called by WordPress.
	 * 
	 * @since 1.3
	 * @access private
	 */
	function init() {
		global $geo_mashup_options;


		// If enabled, register all the interface elements
		if ( !is_admin() && $geo_mashup_options->get( 'overall', 'located_object_name', 'comment' ) == 'true' ) { 

			// Form generation
			add_action( 'comment_form', array( &$this, 'print_form' ) );

			// Form processing
			add_action( 'comment_post', array( &$this, 'save_comment'), 10, 2 );

			// Google JSAPI provides client location by IP
			wp_enqueue_script( 'google-jsapi' );
			wp_enqueue_script( 'geo-mashup-loader' );
		}
	}

	/**
	 * Print the comment location editor form.
	 *
	 * @since 1.3
	 * @access public
	 */
	function print_form()
	{
		// If there's a logged in user with a location, use that as a default.
		// The client-side location will override it if available
		$default_lat = $default_lng = '';
		$user = wp_get_current_user();
		if ( $user ) {
			$location = GeoMashupDB::get_object_location( 'user', $user->ID );
			if ( $location ) {
				$default_lat = $location->lat;
				$default_lng = $location->lng;
			}
		}

		// Print the form
		$input_format = '<input id="geo_mashup_%s_input" name="comment_location[%s]" type="hidden" value="%s" />';
		printf( $input_format, 'lat', 'lat', $default_lat );
		printf( $input_format, 'lng', 'lng', $default_lng );
		printf( $input_format, 'country_code', 'country_code', '' );
		printf( $input_format, 'locality_name', 'locality_name', '' );
		printf( $input_format, 'address', 'address', '' );
	}

	/**
	 * When a comment is saved, save any posted location with it.
	 *
	 * save_comment {@link http://codex.wordpress.org/Plugin_API/Filter_Reference filter}
	 * called by WordPress.
	 *
	 * @since 1.3
	 * @access private
	 * @uses GeoMashupDB::set_object_location()
	 */
	function save_comment( $comment_id = 0, $approval = '' ) {
		if ( !$comment_id || 'spam' === $approval || empty( $_POST['comment_location'] ) || !is_array( $_POST['comment_location'] ) ) {
			return false;
		}

		GeoMashupDB::set_object_location( 'comment', $comment_id, $_POST['comment_location'] );
	}
}

// Instantiate
GeoMashupCommentUIFrontend::get_instance();


////////////////////////////////////////////////////
//add_shortcode('front_end_map', 'GeoMashupUIFrontend::front_end_map' );
//add_shortcode('front_end_map', 'GeoMashupUIFrontend::GeoMashupUserUIFrontend()' );
//GeoMashupUIFrontend::GeoMashupUserUIFrontend();


?>