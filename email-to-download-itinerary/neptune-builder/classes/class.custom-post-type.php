<?php
/**
 * Custom Post Type Class
 *
 * @Author: Ashok Kumar Nath (Bappi D Great)
 * @package: Builder
 */

if ( ! defined( 'ABSPATH' ) ) die( NP_HACK_MSG );

if( ! class_exists( 'NP_CPT' ) ) {
    /**
     * Class NP_CPT
     *
     * @since 1.0.1
     */
    class NP_CPT{
        
        /**
         * @var STRING post type name
         * @since 1.0.1
         */
        protected $post_type;
        
        /**
         * @var ARRAY post type label
         * @since 1.0.1
         */
        protected $labels = array();
        
        /**
         * @var ARRAY post type arguments
         * @since 1.0.1
         */
        protected $args = array();
        
        /**
         * @var ARRAY taxonomies for this post type
         * @since 1.0.0
         */
        protected $taxonomies = array();
        
        /**
         * @var ARRAY meta boxes for this post type
         * @since 1.0.0
         */
        protected $meta_boxes = array();
        
        /**
         * @var ARRAY WP_Error
         * @since 1.0.1
         */
        public $np_error;
        
        /**
         * Class constructor
         * 
         * @since 1.0.1
         */
        public function __construct( $post_type = '', $labels = '', $args = '', $has_taxonomy = True, $taxonomies = array(), $meta_boxes = array() ) {
            
            $this->np_error = new WP_Error();
            
            // Check if the MUST values are provided
            if( $post_type == '' || $labels == '' || $args == '' ){
                $this->np_error->add( 'cpt_error', '$post_type, $labels and $args all needs to be defined.' );
            }else{
                $this->np_error->remove( 'cpt_error' );
            }
            
            // Setup custom post type
            $labels_default = array(
                		'name'               => $post_type,
                		'singular_name'      => $post_type,
                		'menu_name'          => $post_type,
                		'name_admin_bar'     => $post_type,
                		'add_new'            => 'Add New',
                		'add_new_item'       => 'Add New ' . $post_type,
                		'new_item'           => 'New ' . $post_type,
                		'edit_item'          => 'Edit ' . $post_type,
                		'view_item'          => 'View ' . $post_type,
                		'all_items'          => 'All ' . $post_type,
                		'search_items'       => 'Search ' . $post_type,
                		'parent_item_colon'  => 'Parent ' . $post_type . ':',
                		'not_found'          => 'No ' . $post_type . ' found.',
                		'not_found_in_trash' => 'No ' . $post_type . ' found in Trash.'
                        );

            $this->labels = wp_parse_args( $labels, $labels_default );
            
            $args_default = array(
                		'labels'             => $this->labels,
                        'description'        => 'Description.',
                		'public'             => true,
                		'publicly_queryable' => true,
                		'show_ui'            => true,
                		'show_in_menu'       => true,
                		'query_var'          => true,
                		'rewrite'            => array( 'slug' => $post_type ),
                        //'taxonomies'         => array( 'post_tag' ),
                		'capability_type'    => 'post',
                		'has_archive'        => true,
                		'hierarchical'       => false,
                		'menu_position'      => 104,
                        'menu_icon'          => 'dashicons-clipboard',
                		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
                        );

            $this->args = wp_parse_args( $args, $args_default );
            $this->post_type = $post_type;

            /**
            * Adds argument to create itinerary category
            */
            if ($has_taxonomy === True) {

                $tax_domain = 'itinerary-category';

                $tax_labels = array( 
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
                    'labels' => $tax_labels,
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

                register_taxonomy( 'itinerary-category', array($post_type), $tax_args );

                if (! has_term( 'iata-trail-itinerary', 'itinerary-category' )) {
                    wp_insert_term( 'Itineraries','itinerary-category', array('Trail Itinerary Documents', 'iata-trail-itinerary') );
                }
            }

            // Check if there is any taxonomy defined
    	    if( $taxonomies == '' || ! is_array( $taxonomies ) ) $taxonomies = array();
                if( count( $taxonomies ) > 0 ){
                    foreach( $taxonomies as $tax => $val ) {
                        $tax_default = array(
                                    'hierarchical' => false,
                                    'labels' => array(
                                                'name'                       => $tax,
                                                'singular_name'              => $tax,
                                                'search_items'               => 'Search ' . $tax,
                                                'popular_items'              => 'Popular ' . $tax,
                                                'all_items'                  => 'All ' . $tax,
                                                'parent_item'                => null,
                                                'parent_item_colon'          => null,
                                                'edit_item'                  => 'Edit ' . $tax,
                                                'update_item'                => 'Update ' . $tax,
                                                'add_new_item'               => 'Add New ' . $tax,
                                                'new_item_name'              => 'New Taxonomy ' . $tax,
                                                'separate_items_with_commas' => 'Separate '  . $tax . ' with commas',
                                                'add_or_remove_items'        => 'Add or remove ' . $tax,
                                                'choose_from_most_used'      => 'Choose from the most used ' . $tax,
                                                'not_found'                  => 'No '  . $tax . ' found.',
                                                'menu_name'                  =>  $tax,
                                                ),
                                    'show_ui' => true,
                                    'show_admin_column' => false,
                                    'query_var' => true,
                                    'rewrite' => array( 'slug' => $tax ),
                                );
                        
                        $this->taxonomies[$tax] = wp_parse_args( $val, $tax_default );
                    }
                }
            
            // Check if there is any meta box defined
            if( count( $meta_boxes ) > 0 ){
                $this->meta_boxes = $meta_boxes;
                add_action( 'add_meta_boxes', array( &$this, 'np_add_meta_box' ) );
            }
            
        }
        
        /**
         * Registering a post type
         *
         * @since 1.0.0
         */
        public function register() {
            
            register_post_type( $this->post_type, $this->args );

            if( count( $this->taxonomies ) > 0 ){
                foreach( $this->taxonomies as $tax => $val ){
                    register_taxonomy( $tax, $this->post_type, $val );
                }
            }
            
            //Flush permanlinks
            flush_rewrite_rules();
        }
        
        /**
         * Add Meta Boxes
         *
         * @since 1.0.1
         */
        function np_add_meta_box() {
            if( count( $this->meta_boxes ) > 0 ){
                foreach( $this->meta_boxes as $meta ){
                    add_meta_box(
			$meta['id'],
			$meta['title'],
			$meta['callback'],
			$this->post_type,
                        isset( $meta['context'] ) ? $meta['context'] : 'side',
                        isset( $meta['priority'] ) ? $meta['priority'] : 'high'
                    );
                }
            }
        }
        
        /**
         * Get post type
         *
         * @since 1.0.1
         */
        public function get_post_type() {
            return $this->post_type;
        }
        
        /**
         * Get taxonomies
         *
         * @since 1.0.1
         */
        public function get_taxonomies( $array = true ){
            if( $array ){
                return array_keys( $this->taxonomies );
            }else{
                return implode( ', ', array_keys( $this->taxonomies ) );
            }
        }
        
    }
}