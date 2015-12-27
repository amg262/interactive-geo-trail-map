<?php
/**
 * The Geo Mashup Custom class. Adds custom fucntionality
 */
if ( !class_exists( 'GeoMashupCustom' ) ) {
class GeoMashupCustom {
    var $files = array();
    var $found_files;
    var $dir_path;
    var $url_path;
    var $basename;
    var $warnings = '';

    /**
     * PHP4 Constructor
     */
    function GeoMashupCustom() {

        // Initialize members
        $this->dir_path = dirname( __FILE__ );
        $this->basename = plugin_basename( __FILE__ );
        $plugin_name = substr( $this->basename, 0, strpos( $this->basename, '/', 1 ) );
        $dir_name = substr( $this->basename, 0, strpos( $this->basename, '/', strlen($plugin_name)+1 ) );
        $this->url_path = trailingslashit( WP_PLUGIN_URL ) . $dir_name;
        load_plugin_textdomain( 'GeoMashupCustom', 'wp-content/plugins/'.$plugin_name, $plugin_name );
        
        // Inventory custom files
        if ( $dir_handle = @ opendir( $this->dir_path ) ) {
            $self_file = basename( __FILE__ );
            while ( ( $custom_file = readdir( $dir_handle ) ) !== false ) {
                if ( $self_file != $custom_file && !strpos( $custom_file, '-sample' ) && !is_dir( $custom_file ) ) {
                    $this->files[$custom_file] = trailingslashit( $this->url_path ) . $custom_file;
                }
            }
        }

        // Scan Geo Mashup after it has been loaded
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

        // Output messages
        add_action( 'after_plugin_row_' . $this->basename, array( $this, 'after_plugin_row' ), 10, 2 );
    }

    /**
     * Once all plugins are loaded, we can examine Geo Mashup.
     */
    function plugins_loaded() {
        if ( defined( 'GEO_MASHUP_DIR_PATH' ) ) {
            // Check version
            if ( GEO_MASHUP_VERSION <= '1.2.4' ) {
                $this->warnings .= __( 'Custom files can be stored safely in this plugin folder, but will only be used by versions of Geo Mashup later than 1.2.4.', 'GeoMashupCustom' ) .
                    '<br/>';
            }
            $this->found_files = get_option( 'geo_mashup_custom_found_files' );
            if ( empty( $this->found_files ) ) {
                $this->found_files = $this->rescue_files();
                update_option( 'geo_mashup_custom_found_files', $this->found_files );
            }
        }
    }

    /**
     * Rescue known custom files from the Geo Mashup folder.
     */
    function rescue_files() {
        $results = array( 'ok' => array(), 'failed' => array() );
        $check_files = array( 'custom.js', 'map-style-default.css', 'info-window.php', 'full-post.php', 'user.php', 'comment.php' );
        foreach( $check_files as $file ) {
            if ( !isset( $this->files[$file] ) ) {
                $endangered_file = trailingslashit( GEO_MASHUP_DIR_PATH ) . $file;
                if ( is_readable( $endangered_file ) ) {
                    $safe_file = trailingslashit( $this->dir_path ) . $file; 
                    if ( copy( $endangered_file, $safe_file ) ) {
                        $this->file[$file] = trailingslashit( $this->url_path ) . $file;
                        array_push( $results['ok'], $file );
                    } else {
                        array_push( $results['failed'], $file );
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Display any messages after the plugin row.
     * 
     * @param object $plugin_data Plugin data.
     * @param string $context 'active', 'inactive', etc.
     */
    /*function after_plugin_row( $plugin_data = null, $context = '' ) {
        if ( !empty( $_GET['geo_mashup_custom_list'] ) ) {
            echo '<tr><td colspan="5">' . __( 'Current custom files: ', 'GeoMashupCustom') .
                implode( ', ', array_keys( $this->files ) ) . '</td></tr>';
        }
        if ( !empty( $this->warnings ) ) {
            echo '<tr><td colspan="5">' . $this->warnings . '</td></tr>';
        }
    }*/

    /**
     * Get the URL of a custom file if it exists.
     *
     * @param string $file The custom file to check for.
     * @return URL or false if the file is not found.
     */
    function file_url( $file ) {
        $url = false;
        if ( isset( $this->files[$file] ) ) {
            $url = $this->files[$file];
        }
        return $url;
    }

} // end Geo Mashup Custom class

// Instantiate
$geo_mashup_custom = new GeoMashupCustom();

} // end if Geo Mashup Custom class exists

function trail_story_locations_json_filter( $json_properties, $queried_object ) {

    global $trail_story_options;
    global $geo_mashup_options;

    $trail_story_options = (array) get_option( 'trail_story_options' );

    $post_id = $queried_object->object_id;

    $post = get_post($post_id);

    $taxonomies = get_object_taxonomies($post);
    $term = wp_get_post_terms( $post_id, $taxonomies );

    $post_type = get_post_type( $post_id );
    $allowed_post_types = $geo_mashup_options->get( 'overall', 'located_post_types' );
    
    if ( $trail_story_options[ 'trail-story-add-icon-text-box-'. $term[0]->slug ] ) {

        $json_properties['link_to_image_icon'] = $trail_story_options[ 'trail-story-add-icon-text-box-'. $term[0]->slug ];
        return $json_properties;

    } elseif ( in_array( $post_type, $allowed_post_types ) ) {

        $json_properties['link_to_image_icon'] = $trail_story_options[ 'trail-story-add-icon-text-box-'. $post_type ];
        return $json_properties;

    } else { 
            
        $json_properties[''];
        return $json_properties;

    }

}

add_filter( 'geo_mashup_locations_json_object','trail_story_locations_json_filter', 10, 2 );

function trail_story_locations_custom_kmls() {
    global $trail_story_options, $geo_mashup_options, $translation_array;

    // get strail story options as array
    $trail_story_options = (array) get_option( 'trail_story_options' );
    // get Geo Mashup's included taxonomies
    $include_taxonomies = $geo_mashup_options->get( 'overall', 'include_taxonomies' );
    //get All post types
    $post_types = get_post_types( array( 'show_ui' => true ), 'objects' );
    // define empty array to be filled later
    $translation_array = array();

    // loop through all post types
    foreach( $post_types as $post_type ) :

        // if the post type is active in Geo Mashup's options
        if ( in_array( $post_type->name, $geo_mashup_options->get( 'overall', 'located_post_types' ) ) ) {

            //set the key to the post type
            $post_type_key = 'trail-story-add-icon-text-box-' . $post_type->name;

            if ( !empty( $include_taxonomies ) ) :
                // loop through each taxonomy
                foreach( $include_taxonomies as $include_taxonomy ) :

                    if( in_array( $include_taxonomy, get_object_taxonomies( $post_type->name ) ) ):

                        // get Geo Mashup's term options
                        $taxonomy_options = $geo_mashup_options->get( 'global_map', 'term_options', $include_taxonomy );
                        // get terms showing empty ones
                        $terms = get_terms( $include_taxonomy, array( 'hide_empty' => false ) );

                        // if it's an array
                        if ( is_array($terms) ) :
                            // loop through each term object
                            foreach( $terms as $term ) :
                                
                                // set key to option name
                                $term_key = 'trail-story-add-icon-text-box-' . $term->slug;

                                // if the option is not an empty string and if it isn't null
                                if( $trail_story_options[$term_key] !== '' && !is_null( $trail_story_options[$term_key] ) ){

                                    // push the option into the array
                                    $translation_array[$include_taxonomy][$term->term_id] = $trail_story_options[$term_key];

                                // if the option is empty or is null
                                } elseif( $trail_story_options[$post_type_key] !== '' && !is_null( $trail_story_options[$post_type_key] ) ) {

                                    // push the option into the array
                                    $translation_array[$include_taxonomy][$term->term_id] = $trail_story_options[$post_type_key];

                                }

                            endforeach;

                        endif;

                    endif;

                endforeach;

            endif;

        }

    endforeach;    

    // Localize the script with new data
    // TODO: Make KMLs/KMZs pull from database
    /*$translation_array = array(
        'kml_layer_1' => 'http://dev.cloudground.net/wp-content/uploads/2015/11/iat_full.kmz',
        'kml_layer_2' => 'http://dev.cloudground.net/wp-content/uploads/2015/11/parking.kml',
        'kml_layer_3' => 'http://dev.cloudground.net/wp-content/uploads/2015/11/iat.kml',
        'image_icon'  => 'http://plugins.dev/wp-content/uploads/2015/10/conversation-map-icon.png',
    );*/
    
    // Localize the array to custom.js
    wp_localize_script( 'geo-mashup-custom', 'icons', $translation_array );

}

add_action( 'geo_mashup_render_map', 'trail_story_locations_custom_kmls' );

add_image_size( 'locationinfo-thumbnail', 70, 70, true );