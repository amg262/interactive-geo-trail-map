<?php
/**
* 
* Plugin Name: Email to Download Itinerary
* Plugin URI:  http://www.orionweb.net
* Version:     1.0.0
* Description: A plugin that helps you to collect email before downloading intineraries.
* Author:      Orion Group LLC
* Author URI:  http://www.orionweb.net
*
* EMAIL TO DOWNLOAD ITINERARY MODULE
*/
if( ! defined( 'ED_HACK_MSG' ) ) define( 'ED_HACK_MSG', __( 'Sorry cowboy! This is not your place', 'email-to-download' ) );

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( ED_HACK_MSG );

/**
 * Defining constants
 */
if( ! defined( 'ED_VERSION' ) ) define( 'ED_VERSION', '2.0.2' );
if( ! defined( 'ED_MENU_POSITION' ) ) define( 'ED_MENU_POSITION', 104 );
if( ! defined( 'ED_PLUGIN_DIR' ) ) define( 'ED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if( ! defined( 'ED_FILES_DIR' ) ) define( 'ED_FILES_DIR', ED_PLUGIN_DIR . 'ed-files' );
if( ! defined( 'ED_PLUGIN_URI' ) ) define( 'ED_PLUGIN_URI', plugins_url( '', __FILE__ ) );
if( ! defined( 'ED_FILES_URI' ) ) define( 'ED_FILES_URI', ED_PLUGIN_URI . '/ed-files' );
if( ! defined( 'ED_POST_TYPE' ) ) define( 'ED_POST_TYPE', 'itinerary' );

require_once ED_FILES_DIR . '/classes/class.shortcodes.php';
require_once ED_FILES_DIR . '/helper.php';

// Including builder
require_once( 'neptune-builder/builder-loader.php' );

$has_taxonomy = True;

/**
* Flushing permalinks for CPTs on DEACTIVATE
*/
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
* Flushing permalinks for CPTs ON ACTIVATE
*/
register_activation_hook( __FILE__, 'etdi_flush_rewrites' );

/**
* Flushing permalinks
*/
function etdi_flush_rewrites() {
    flush_rewrite_rules();
}

if( ! class_exists( 'ED_Plugin' ) ) {
    /**
     * Class ED_Plugin
     * @since 1.0.1
     *
     * The main class for the plugin
     */
    class ED_Plugin{
        
        /**
         * Singleton Instance
         * @since 1.0.1
         *
         * @access private static
         */
        private static $_instance;
        
        /**
         * Constant settings key
         * @since 1.0.1
         *
         * @access private
         */
        const OPTIONS_KEY = 'etd_settings_option_key';
        
        /**
         * Post type object
         * @since 1.0.1
         *
         * @access public
         */
        public $ed;
        
        /**
         * The global $wpdb instance
         * @since 1.0.1
         *
         * @access private
         */
        private $_db;
        
        /**
         * The table name where emails will be saved
         * @since 1.0.1
         *
         * @access private
         */
        private $_tbl_name;
        
        /**
         * Options object of Settings
         * @since 1.0.1
         *
         * @access public
         */
        public $_options;
        
        /**
         * Defaut settings data
         * @since 1.0.1
         *
         * @access public
         */
        public $_defaults;
        
        /**
         * Download page slug
         * @since 1.0.1
         *
         * @access private
         */
        private $_slug;
        
        /**
         * Class constructor
         * @since 1.0.1
         *
         * @see https://codex.wordpress.org/Function_Reference/get_page_by_path
         *
         * @access public
         */
        public function __construct() {
            
            global $wpdb;
            $this->_db = $wpdb;
            $this->_tbl_name = $this->_db->prefix . 'etd_manager';
            
            if( ! defined( 'ED_DOWNLOAD_PAGE_SLUG' ) ){
                $page = get_page_by_path( 'download' );
                
                if( $page ) {
                    $this->_slug = 'etd-download';
                }else{
                    $this->_slug = 'download';
                }
            }else{
                $this->_slug = ED_DOWNLOAD_PAGE_SLUG;
            }
            
            /**
             * Setting default settings data
             */
            $this->_defaults = array(
                                    'dw_expire' => 24,
                                    'dw_page' => '',
                                    'notify_admin_email' => get_option( 'admin_email' ),
                                    'notify_email_subject' => __( 'Download Link', 'email-to-download' ),
                                    'email_content' => __( 'Dear User,

Thank you for downloading our product. We have generated the download link for you. Please click on the following link to download the product. Please note that, this link will be active for {{download_expire}} hour(s).

Download here: {{download_link}}
', 'email-to-download' )
                                    );

            $this->_options = new NP_OPTIONS( self::OPTIONS_KEY, $this->_defaults );
            
            add_action( 'init', array( &$this, 'init' ) );
            add_action( 'init', array( &$this, 'add_dw_rewrite_tag' ) );
            add_action( 'admin_init', array( &$this, 'install_required_tables' ) );
            //register_activation_hook( __FILE__, array( &$this, 'add_dw_rewrite_tag' ) );
        }
        
        /**
         * Get class singleton instance
         * @since 1.0.1
         *
         * @access public
         *
         * @return Class Instance
         */
        public static function get_instance() {
            if ( ! self::$_instance instanceof ED_Plugin ) {
                self::$_instance = new ED_Plugin();
            }
            
            return self::$_instance;
        }
        
        /**
         * Add download rewrite tag
         * @since 1.0.1
         *
         * @see https://codex.wordpress.org/Rewrite_API/add_rewrite_tag
         * @see https://codex.wordpress.org/Rewrite_API/add_rewrite_rule
         * @see https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
         *
         * @access public
         */
        public function add_dw_rewrite_tag() {
            
            add_rewrite_tag( "%{$this->_slug}%", '(\w+)' );
            add_rewrite_rule(
		"^{$this->_slug}/(\w+)",
		'index.php?' . $this->_slug . '=$matches[1]',
		'top'
            );
            
            //flush_rewrite_rules();
        }
        
        /**
         * Install required tables
         * @since 1.0.1
         *
         * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
         *
         * @access public
         */
        public function install_required_tables() {
            $charset_collate = $this->_db->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS {$this->_tbl_name} (
              id INT(200) NOT NULL AUTO_INCREMENT,
              file_url TEXT,
              primary_time DATETIME,
              email VARCHAR(200),
              download_id INT(200),
              is_subscribed VARCHAR(20),
              UNIQUE KEY id (id)
            )";
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
        
        /**
         * Plugin initialization, register post type
         * @since 1.0.1
         *
         * @access public
         */
        public function init() {
            
            /**
             * Filter for ed_download post type
             * @since 1.0.1
             *
             * @param   ed_download     string  ED Download Post Type
             */
            $post_type = apply_filters(
                                    'ed_post_type',
                                    'itinerary'
                                );
            
            $labels = array(
        		'name'               => _x( 'Itineraries', 'post type general name', 'email-to-download' ),
        		'singular_name'      => _x( 'Itinerary', 'post type singular name', 'email-to-download' ),
        		'menu_name'          => _x( 'Trail Itineraries', 'admin menu', 'email-to-download' ),
        		'name_admin_bar'     => _x( 'Itinerary', 'add new on admin bar', 'email-to-download' ),
        		'add_new'            => _x( 'Add New', 'download', 'email-to-download' ),
        		'add_new_item'       => __( 'Add New Itinerary', 'email-to-download' ),
        		'new_item'           => __( 'New Itinerary', 'email-to-download' ),
        		'edit_item'          => __( 'Edit Itinerary', 'email-to-download' ),
        		'view_item'          => __( 'View Itinerary', 'email-to-download' ),
        		'all_items'          => __( 'All Itineraries', 'email-to-download' ),
        		'search_items'       => __( 'Search Itineraries', 'email-to-download' ),
        		'parent_item_colon'  => __( 'Parent Itineraries:', 'email-to-download' ),
        		'not_found'          => __( 'No Itineraries found.', 'email-to-download' ),
        		'not_found_in_trash' => __( 'No Itineraries found in Trash.', 'email-to-download' )
                );
            
            /**
             * Filter for ed_download post type labels
             * @since 1.0.1
             *
             * @param   $labels   array   An array of arguments of the post type $labels param
             */
            $labels = apply_filters(
                                    'ed_post_type_label',
                                    $labels
                                );
            
            $args = array(
                'description'        => __( 'Description.', 'email-to-download' ),
        		'exclude_from_search'=> true,
        		'publicly_queryable' => true,
        		'show_ui'            => true,
        		'show_in_menu'       => true,
                'show_in_nav_menus'  => false,
                'show_in_admin_bar'  => false,
        		'query_var'          => true,
        		'rewrite'            => true,
        		'capability_type'    => 'post',
        		'has_archive'        => false,
        		'hierarchical'       => false,
        		'menu_position'      => 5005,
        		'supports'           => array( 'title', 'editor')
                );
            
            /**
             * Filter for ed_download post type arguments
             * @since 1.0.1
             *
             * @param   $args   array   An array of arguments of the post type $args param
             */
            $args = apply_filters(
                                'ed_post_type_args',
                                $args
                            );


            /**
            * Taxonomy for Itinerary categories
            */
            /*$tax_labels = array( 
                'name' => _x( 'Itinerary Category', 'itinerary-category' ),
                'singular_name' => _x( 'Itinerary Category', 'itinerary-category' ),
                'search_items' => _x( 'Search Itinerary Categories', 'itinerary-category' ),
                'popular_items' => _x( 'Popular Itinerary Categories', 'itinerary-category' ),
                'all_items' => _x( 'All Itinerary Categories', 'itinerary-category' ),
                'parent_item' => _x( 'Parent Itinerary Category', 'itinerary-category' ),
                'parent_item_colon' => _x( 'Parent Itinerary Category:', 'itinerary-category' ),
                'edit_item' => _x( 'Edit Itinerary Category', 'itinerary-category' ),
                'update_item' => _x( 'Update Itinerary Category', 'itinerary-category' ),
                'add_new_item' => _x( 'Add New', 'itinerary-category' ),
                'new_item_name' => _x( 'New Itinerary Category', 'itinerary-category' ),
                'separate_items_with_commas' => _x( 'Separate Itinerary Categories with commas', 'itinerary-category' ),
                'add_or_remove_items' => _x( 'Add or remove Itinerary Categories', 'itinerary-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used Itinerary Category', 'itinerary-category' ),
                'menu_name' => _x( 'Categories', 'itinerary-category' ),
            );

            $tax_args = array( 
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_tagcloud' => true,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => true,
                'query_var' => true
            );

            $tax_array = array( 'itinerary-category' );*/
            
            $meta_boxes = array(
                                array(
                                    'id' => 'ed_uploader',
                                    'title' => 'Uplaod your file',
                                    'callback' => array( &$this, 'ed_uploader_cb' ),
                                    'context' => 'normal',
                                    'priority' => 'high'
                                )
                            );
            
            $this->ed = new NP_CPT( $post_type, $labels, $args, True, '', $meta_boxes );
            
            // Registering ed_download post type
            if( count( ( array ) $this->ed->np_error->get_error_messages( 'cpt_error' ) ) < 1 ){
                $this->ed->register();
            }else{
                echo "<pre>";
                print_r( $this->ed->np_error );
                echo "</pre>";
            }
            
            /**
             * ShortCode handler
             */
            $shortcodes_list = array( 'ed_download_file' => 'ed_download_file' );
            $shortcodes_builder = array(
                            'builder' => true,
                            'label' => __( 'Email to Download ShortCode', 'etb' ),
                            'icon' => 'fa fa-download',
                            'codes' => array(
                                'ed_download_file' => '[ed_download_file] ' . __( 'Downloader ShortCode', 'etb' )
                            ),
                            'atts' => array(
                                'ed_download_file' => array(
                                    array(
                                        'name' => __( 'Select a Download', 'email-to-download' ),
                                        'term' => 'id',
                                        'type' => 'post_type',
                                        'default' => '',
                                        'options' => array(
                                            'post_type' => $this->ed->get_post_type()
                                        )
                                    ),
                                    array(
                                        'name' => __( 'Show Title?', 'email-to-download' ),
                                        'term' => 'title',
                                        'type' => 'radio',
                                        'default' => 'yes',
                                        'options' => array(
                                            'yes',
                                            'no'
                                        )
                                    ),
                                    array(
                                        'name' => __( 'Show Content?', 'email-to-download' ),
                                        'term' => 'content',
                                        'type' => 'radio',
                                        'default' => 'yes',
                                        'options' => array(
                                            'yes',
                                            'no'
                                        )
                                    ),
                                    array(
                                        'name' => __( 'Choose a style', 'email-to-download' ),
                                        'term' => 'style',
                                        'type' => 'select',
                                        'default' => 'normal',
                                        'options' => array(
                                            'normal',
                                            'popup',
                                            'slide'
                                        )
                                    )
                                )
                            )
                        );
            
            $shortcodes = new NP_SHORTCODES( $shortcodes_list, $shortcodes_builder );
            $shortcodes->register();
            
            /**
             * Calling other hooks
             */
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts_cb' ) );
            add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts_cb' ) );
            add_action( 'save_post_' . $this->ed->get_post_type() , array( &$this, 'ed_save_meta_box_data' ) );
            add_action( 'wp_ajax_etd_ajax_dw_submit', array( &$this, 'etd_ajax_dw_submit_cb' ) );
            add_action( 'wp_ajax_nopriv_etd_ajax_dw_submit', array( &$this, 'etd_ajax_dw_submit_cb' ) );
            add_action( 'admin_menu', array( &$this, 'ed_settings_menu' ) );
            add_action( 'admin_action_ed_settings_save', array( &$this, 'ed_settings_save_cb' ) );
            add_action( 'template_redirect', array( &$this, 'ed_check_dw_link' ) );
            add_filter( 'query_vars', array( &$this, 'register_ed_query_vars' ), 1 );
            add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
            //add_action( 'admin_notices', array( &$this, 'mc_admin_notices' ) );
            add_action( 'init', array( &$this, 'etd_load_textdomain' ), 999 );
        }
        
        /**
         * Translators
         * 
         * NOT NEEDED FOR NOW
         */
        public function etd_load_textdomain() {
            load_plugin_textdomain( 'email-to-download', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }
        
        /*public function mc_admin_notices() {
            ?>
            <div class="updated notice is-dismissible">
                <p><?php _e( 'Want to integrate mailchimp with Email to Download plugin? <a href="https://duogeek.com/products/plugins/mailchimp-integration-email-to-download/" target="_blank">Build your email list in Mailchimp now!</a>', 'my-text-domain' ); ?></p>
            </div>
            <?php
        }*/
        
        /**
         * Enqueue required file in admin
         * @since 1.0.1
         *
         * @access public
         */
        public function admin_enqueue_scripts_cb() {
            
            wp_enqueue_media();
            
            wp_enqueue_style(
                            'ed-admin-css',
                            ED_FILES_URI . '/assets/css/ed-admin.css',
                            '',
                            ED_VERSION
                        );
            
            wp_enqueue_script(
                              'ed-admin-js',
                              ED_FILES_URI . '/assets/js/ed-admin.js',
                              array( 'jquery' ),
                              ED_VERSION,
                              true
                            );
        }
        
        /**
         * Enqueue required file in front end
         * @since 1.0.1
         *
         * @access public
         */
        public function wp_enqueue_scripts_cb() {
            
            wp_enqueue_style(
                            'ed-front-css',
                            ED_FILES_URI . '/assets/css/ed-front.css',
                            '',
                            ED_VERSION
                        );
            wp_enqueue_style(
                        'ed-colorbox-css',
                        ED_PLUGIN_URI . '/neptune-builder/assets/colorbox/colorbox.css',
                        '',
                        NP_BUILDER_VERSION
                    );
            wp_enqueue_script(
                            'ed-colorbox-script',
                            ED_PLUGIN_URI . '/neptune-builder/assets/colorbox/jquery.colorbox-min.js',
                            array( 'jquery' ),
                            ED_VERSION,
                            true
                        );
            wp_register_script(
                              'ed-front-js',
                              ED_FILES_URI . '/assets/js/ed-front.js',
                              array( 'jquery' ),
                              ED_VERSION,
                              true
                            );

            wp_localize_script( 'ed-front-js', 'obj', array(
                                                            'adminAjax' => admin_url( 'admin-ajax.php' ),
                                                            'ajaxNonce' => wp_create_nonce( 'ed-ajax-form-submit' ),
                                                            'emailError' => __( 'Please provide a correct email address', 'email-to-download' ),
                                                            'successMSG' => sprintf( __( 'Thank you! Please check your inbox (or spam) for download link email. This link will be expired in %d hour(s).<br><br><a href='.get_home_url().'>&#8592&nbsp;Back</a>', 'email-to-download' ), $this->_options->get_option( 'dw_expire' ) )
                                                        ) );

            wp_enqueue_script( 'ed-front-js' );
        }
        
        /**
         * Nonce validator
         * @since 1.0.1
         *
         * @access private
         *
         * @param   $name   string  Name of the nonce field
         * @param   $value  string  Value of the nonce to be checked
         *
         * @return          bool    True if validated, otherwise false
         */
        private function validate_nonce( $name, $value ){
            if ( ! isset( $_POST[$name] ) ) {
                return false;
            }
    
            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST[$name], $value ) ) {
                return false;
            }
            
            return true;
        }
        
        /**
         * Show admin notices when needed
         * @since 1.0.1
         *
         * @access public
         */
        public function admin_notices() {
            if ( empty( $_REQUEST['msg'] ) ) {
		return;
	    }
            
            $msg = urldecode( $_REQUEST['msg'] );
            if( $msg ) {
            ?>
            <div class="<?php echo isset( $_REQUEST['res'] ) ? $_REQUEST['res'] : 'updated' ?>">
                <p>
                    <?php echo urldecode( $_REQUEST['msg'] ); ?>
                </p>
            </div>
            <?php
            }
        }
        
        /**
         * Uploader meta box for ed_download post type
         * @since 1.0.1
         *
         * @access public
         *
         * @param   $post   object      Post object
         */
        public function ed_uploader_cb( $post ) {
            wp_nonce_field( 'ed_save_meta_box_data', 'ed_meta_box_nonce' );
            $ed_file = get_post_meta( $post->ID, '_ed_file_', true );
            //$ed_file_id = get_post_meta( $post->ID, '_ed_file_id_', true );
            ?>
            <table cellpadding="5" cellspacing="5">
                <tr>
                    <th><?php _e( 'Uplaod your file or give the file URL:', 'email-to-download' ) ?></th>
                    <td>
                        <input type="text" size="50" name="ed_download[item]" id="ed_item" value="<?php echo isset( $ed_file ) ? $ed_file : '' ?>">
                        <!--input type="hidden" name="ed_download[id]" id="ed_id" value="<?php echo isset( $ed_file_id ) ? $ed_file_id : '' ?>"-->
                        <button type="button" class="button button-secondary" id="ed_uploader_btn"><?php _e( 'Upload File' ) ?></button>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        /**
         * Save the meta data
         * @since 1.0.1
         *
         * @access public
         *
         * @param   $post_id    int     The ID of the Post object
         */
        public function ed_save_meta_box_data( $post_id ) {
            
            if( ! $this->validate_nonce( 'ed_meta_box_nonce', 'ed_save_meta_box_data' ) ) return;
    
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            
            if ( ! isset( $_POST['ed_download'] ) ) {
		return;
            }
            
            $ed_file = sanitize_text_field( $_POST['ed_download']['item'] );
            //$ed_file_id = sanitize_text_field( $_POST['ed_download']['id'] );
            
            update_post_meta( $post_id, '_ed_file_', $ed_file );
            //update_post_meta( $post_id, '_ed_file_id_', $ed_file_id );
            
        }
        
        /**
         * Add settings menu
         * @since 1.0.1
         *
         * @access public
         */
        public function ed_settings_menu() {
            
            add_submenu_page( 'edit.php?post_type=' . $this->ed->get_post_type(), __( 'Email to Download Settings', 'email-to-download' ), __( 'Settings', 'email-to-download' ), 'manage_options', 'email-to-download-settings', array( &$this, 'email_to_download_settings' ) );
            add_submenu_page( 'edit.php?post_type=' . $this->ed->get_post_type(), __( 'Downloads', 'email-to-download' ), __( 'Downloads', 'email-to-download' ), 'manage_options', 'email-to-download-leads', array( &$this, 'email_to_download_leads' ) );
        }
        
        /**
         * Show the subscribers list
         * @since 1.0.0
         *
         * @return void
         */
        public function email_to_download_leads() {
            $sql = "SELECT email, primary_time, is_subscribed FROM {$this->_tbl_name} GROUP BY(email)";
            $emails = $this->_db->get_results( $sql, OBJECT );
            
            ?>
            <div class="wrap">
                <h2><?php _e( 'View Leads', 'email-to-download' ) ?></h2>
                <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <table class="wp-list-table widefat">
                                    <thead>
                                        <tr>
                                            <th><?php _e( 'Email', 'email-to-download' ) ?></th>
                                            <th><?php _e( 'Date', 'email-to-download' ) ?></th>
                                            <th><?php _e( 'Subscribed', 'email-to-download' ) ?></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><?php _e( 'Email', 'email-to-download' ) ?></th>
                                            <th><?php _e( 'Date', 'email-to-download' ) ?></th>
                                            <th><?php _e( 'Subscribed', 'email-to-download' ) ?></th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php $i = 0; foreach( $emails as $email ) { $i++; ?>
                                        <tr class="<?php echo $i % 2 == 0 ? 'alternate' : ''; ?>">
                                            <td><?php echo $email->email; ?></td>
                                            <td><?php echo NP_HELPER::get_date_time_value( strtotime( $email->primary_time ), true, true ); ?></td>
                                            <td><?php echo $email->is_subscribed; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php //echo ed_get_sidebar(); ?>
                        </div>
                    </div>
            </div>
            <?php
        }
        
        /**
         * The settings page for this plugin
         * @since 1.0.1
         *
         * @access public
         */
        public function email_to_download_settings() {
            
            $options = $this->_options->get_options();
            $pages = get_pages();
            
            ?>
            <div class="wrap">
                <h2><?php _e( 'Email to Download Settings', 'email-to-download' ) ?></h2>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <form action="<?php echo admin_url( '?action=ed_settings_save&noheader=true' ) ?>" method="post">
                                    <?php wp_nonce_field( 'ed_save_settings_data', 'ed_save_settings_nonce' ); ?>
                                    <div class="postbox">
                                        <h3 class="hndle"><?php _e( 'General Settings', 'email-to-download' ) ?></h3>
                                        <div class="inside">
                                            <table class="form-table">
                                                
                                                <?php
                                                    $form = array(
                                                                'wrap' => 'tr',
                                                                'elements' => array(
                                                                    array(
                                                                        'label'         => __( 'Download link will expire at:', 'email-to-download' ),
                                                                        'type'          => 'text',
                                                                        'name'          => 'ed_settings[dw_expire]',
                                                                        'default'      => $this->_options->get_option( 'dw_expire' ),
                                                                        'value'         => $this->_options->get_option( 'dw_expire' ),
                                                                        'placeholder'   => false,
                                                                        'options'       => array(),
                                                                        'attr'          => 'size="5"'
                                                                    ),
                                                                    
                                                                    array(
                                                                        'label'         => __( 'Select a page for expire message:', 'email-to-download' ),
                                                                        'type'          => 'post_type',
                                                                        'name'          => 'ed_settings[dw_page]',
                                                                        'default'      => $this->_options->get_option( 'dw_page' ),
                                                                        'value'         => $this->_options->get_option( 'dw_page' ),
                                                                        'placeholder'   => false,
                                                                        'options'       => array( 'post_type' => 'page' ),
                                                                        'attr'          => ''
                                                                    ),
                                                                    /*array(
                                                                        'label'         => __( 'Notification email sent to:', 'email-to-download' ),
                                                                        'type'          => 'text',
                                                                        'name'          => 'ed_settings[notify_admin_email]',
                                                                        'default'      => $this->_options->get_option( 'notify_admin_email' ),
                                                                        'value'         => $this->_options->get_option( 'notify_admin_email' ),
                                                                        'placeholder'   => false,
                                                                        'options'       => array(),
                                                                        'attr'          => 'size="50"'
                                                                    ),*/
                                                                    array(
                                                                        'label'         => __( 'Notification email Subject:', 'email-to-download' ),
                                                                        'type'          => 'text',
                                                                        'name'          => 'ed_settings[notify_email_subject]',
                                                                        'default'      => $this->_options->get_option( 'notify_email_subject' ),
                                                                        'value'         => $this->_options->get_option( 'notify_email_subject' ),
                                                                        'placeholder'   => false,
                                                                        'options'       => array(),
                                                                        'attr'          => 'size="50"'
                                                                    ),
                                                                    array(
                                                                        'label'         => __( 'Notification email content:', 'email-to-download' ),
                                                                        'type'          => 'textarea',
                                                                        'name'          => 'ed_settings[email_content]',
                                                                        'default'      => $this->_options->get_option( 'email_content' ),
                                                                        'value'         => $this->_options->get_option( 'email_content' ),
                                                                        'placeholder'   => false,
                                                                        'options'       => array(),
                                                                        'attr'          => 'cols="70" rows="10"'
                                                                    )
                                                                )
                                                            );
                                                    
                                                    echo NP_FORM_HELPER::build_form( $form );
                                                ?>
                                                
                                                <tr>
                                                    <td colspan="2">
                                                        <input type="submit" name="ed_settings[submit]" class="button button-primary" value="<?php _e( 'Save Settings', 'email-to-download' ) ?>" />
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- This is sidebar -->
                            
                            <?php //echo ed_get_sidebar(); ?>
                            
                            <!-- End of sidebar -->
                            
                        </div>
                    </div>
                
            </div>
            <?php
        }
        
        /**
         * Save the settings data from the form
         * @since 1.0.1
         *
         * @access public
         */
        public function ed_settings_save_cb() {
            
            if( ! $this->validate_nonce( 'ed_save_settings_nonce', 'ed_save_settings_data' ) ) return;
            
            $this->_options->set_options( $_POST['ed_settings'] );
            
            wp_redirect( admin_url( 'edit.php?post_type='.ED_POST_TYPE.'&page=email-to-download-settings&res=updated&msg=' . urlencode( __( 'Settings saved successfully.' ) ) ) );
        }
        
        /**
         * Handles ajax form submission
         * Save the information in the database and send email to member
         * 
         * @since 1.0.1
         *
         * @see https://codex.wordpress.org/Function_Reference/check_ajax_referer
         *
         * @access public
         */
        public function etd_ajax_dw_submit_cb() {
            
            check_ajax_referer( 'ed-ajax-form-submit', '_wpnonce' );
            
            $ed_attachment_url = sanitize_text_field( $_POST['ed_attachment_url'] );
            $ed_download_id = sanitize_text_field( $_POST['ed_download_id'] );
            $ed_email = sanitize_text_field( $_POST['ed_email'] );
            $ed_subscribed = $_POST['ed_subscribed'];


            $sql = "SELECT * FROM {$this->_tbl_name} ORDER BY id DESC LIMIT 1";
            $q = $this->_db->get_results( $sql, ARRAY_A );
            if( count( $q ) < 1 ) $identifier = 1;
            else $identifier = $q[0]['id'];
            
            $link = site_url( '/' . $this->_slug . '/' ) . $this->mask_download_link( $ed_attachment_url, $identifier );
            $this->_db->insert(
		$this->_tbl_name,
		array(
			'file_url' => $link,
			'primary_time' => current_time( 'mysql' ),
                        'email' => $ed_email,
			'download_id' => $ed_download_id,

            'is_subscribed' => $ed_subscribed
		)
            );
            
            $post = get_post( $ed_download_id );
            
            wp_mail(
                $ed_email,
                str_replace( '{{download_name}}', $post->post_title, $this->_options->get_option( 'notify_email_subject' ) ),
                str_replace( array( '{{download_expire}}', '{{download_link}}' ), array( $this->_options->get_option( 'dw_expire' ), $link ), $this->_options->get_option( 'email_content' ) )
                );
            
            /**
             * Action when the email is sent to the user
             * including download link and other set message
             * @since 1.0.1
             *
             * @param   $ed_attachment_url      sting   The attachment URL
             * @param   $ed_download_id         int     The download page ID
             * @param   $ed_email               string  The email of the user
             * @param   $link                   string  The download link sent to the user
             */
            do_action(
                'ed_subscribed_to_download',
                $ed_attachment_url,
                $ed_download_id,
                $ed_email,
                $link
            );
            
            die();
            
        }
        
        /**
         * Check the download link and take proper action
         * @since 1.0.1
         *
         * @access public
         */
        public function ed_check_dw_link() {
            
            $dw = get_query_var( $this->_slug );
            if( ! empty( $dw ) ){
                
                $file_url = site_url( '/' . $this->_slug . '/' ) . $dw;
                
                $sql = "SELECT * FROM {$this->_tbl_name} WHERE file_url='{$file_url}'";
                $q = $this->_db->get_results( $sql, ARRAY_A );
                
                $primary_time = $q[0]['primary_time'];
                $post_id = $q[0]['download_id'];
                $current_time = current_time( 'mysql' );
                
                // Check if expiration time exists
                $diff = strtotime( $current_time ) - strtotime( $primary_time );
                
                if( $diff < $this->_options->get_option( 'dw_expire' ) * 60 * 24 ){
                    $counter = get_post_meta( $post_id, 'ed_product_count', true );
                    if( ! $counter ) $counter = 0;
                    $counter++;
                    update_post_meta( $post_id, 'ed_product_count', $counter );
                    
                    $url = explode( 'a0o0a', $dw );
                    $url = etd_base64_url_decode( $url[1] );
                    
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary"); 
		    header('HTTP/1.0 200 OK', true, 200);
                    header("Content-disposition: attachment; filename=\"" . basename( $url ) . "\""); 
                    readfile( $url );
                }else{
                    wp_redirect( get_permalink( $this->_options->get_option( 'dw_page' ) ) );
                    exit;
                }
            }
            
        }
        
        /**
         * Registering custom query variable
         * @since 1.0.1
         *
         * @param   $vars   array   An array of existing query variables
         *
         * @access public
         */
        public function register_ed_query_vars( $vars ){
            array_push( $vars, $this->_slug );
            return $vars;
        }
        
        /**
         * Masking download URL
         * @since 1.0.1
         *
         * @param   $link           string  The download which will be encoded
         * @param   $identifier     string  An unique identifier
         *
         * @access public
         */
        public function mask_download_link( $link, $identifier = '' ){
            $salt = $identifier . 'a0o0a';
            $random = $this->random_string();
            
            /**
             * Filter the output of masked URL
             * @since 1.0.1
             *
             * @param       $random . $salt . base64_encode( $link )    string  The masked download URL
             * @param       $random                                     string  A random number
             * @param       $salt                                       string  An unique salt
             * @param       $link                                       string  Download link before masking
             * @param       $identifier                                 string  An unique identifier
             */
            return apply_filters(
                                'mask_download_link',
                                $random . $salt . etd_base64_url_encode( $link ),
                                $random,
                                $salt,
                                $link,
                                $identifier
                                );
        }
        
        /**
         * Generate random string
         * @since 1.0.1
         *
         * @param   $length     int    Length of random number
         *
         * @access public
         */
        public function random_string( $length = 5 ) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen( $characters );
            $randomString = '';
            for ( $i = 0; $i < $length; $i++ ) {
                $randomString .= $characters[rand( 0, $charactersLength - 1 )];
            }
            return $randomString;
        }
        
    }
    
    function ed() {
        return ED_Plugin::get_instance();
    }
    
    // Initial the plugin
    ed();
    
}

function etd_base64_url_encode($input) {
 return str_replace(array('+','/','='), array('-', '_', 'UUUU'), base64_encode($input));
}

function etd_base64_url_decode($input) {
 return base64_decode(str_replace(array('-', '_', 'UUUU'), array('+','/','='), $input));
}