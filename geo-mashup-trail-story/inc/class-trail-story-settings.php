<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
interface iTrailStorySettings {
    public function __construct();
    public function add_trail_story_menu_page();
    public function create_trail_story_menu_page();
    public function page_init();
    public function sanitize( $input );
    public function print_option_info();
    public function trail_story_option_callback();

}
/**
* PLUGIN SETTINGS PAGE
*/
class TrailStorySettings {
    /**
     * Holds the values to be used in the fields callbacks
     */
    public $trail_story_options;
    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_trail_story_menu_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
    /**
     * Add options page
     */
    public function add_trail_story_menu_page() {
        $count = wp_count_posts('trail-story');
        $pending_count = $count->pending;
        // This page will be under "Settings"add_submenu_page( 'tools.php', 'SEO Image Tags', 'SEO Image Tags', 'manage_options', 'seo_image_tags', 'seo_image_tags_options_page' );
        
        add_menu_page(
            'Geo Trail Map',
            'Geo Trail Map',
            'manage_options',
            'geo-trail-map',
            array( $this, 'create_trail_story_menu_page' ),
            plugins_url('interactive-geo-trail-map/geo-mashup-trail-story/assets/icon-20x20.png'), 5000
        );
    
     
        add_filter( 'add_menu_classes', array( $this, 'add_target_to_links') );
        add_filter( 'add_menu_classes', array( $this, 'show_pending_number_stories') );
        add_filter( 'add_menu_classes', array( $this, 'show_pending_number_conditions') );
        add_filter( 'add_menu_classes', array( $this, 'show_pending_number_itinerary') );
        add_filter( 'add_menu_classes', array( $this, 'show_pending_number_trail_route') );
       // add_filter( 'add_menu_classes', array( $this, 'show_pending_number_trail_segment') );
        
        
    }
    
    public function add_target_to_links($menu) {
        
        // loop through $menu items, find match, add indicator
        foreach( $menu as $menu_key => $menu_data ) {
            $menu[$menu_key][0] .= "<span class='geo-trail-map-menu'>&nbsp;</span>";
        }
        return $menu;
    }
    /**
    * Shows pending for trail stories
    */
    public function show_pending_number_stories( $menu ) {
        $type = "trail-story";
        $status = "pending";
        $num_posts = wp_count_posts( $type, 'readable' );
        $pending_count = 0;
        if ( !empty($num_posts->$status) )
            $pending_count = $num_posts->$status;
        // build string to match in $menu array
        if ($type == 'post') {
            $menu_str = 'edit.php';
        } else {
            $menu_str = 'edit.php?post_type=' . $type;
        }
        // loop through $menu items, find match, add indicator
        foreach( $menu as $menu_key => $menu_data ) {
            if( $menu_str != $menu_data[2] )
                continue;
            $menu[$menu_key][0] .= " <span class='awaiting-mod count-$pending_count'><span class='pending-count'>" . number_format_i18n($pending_count) . '</span></span>';
        }
        return $menu;
    }
    /**
    * Shows pending for trail conditions
    */
    public function show_pending_number_conditions( $menu ) {
        $type = "trail-condition";
        $status = "pending";
        $num_posts = wp_count_posts( $type, 'readable' );
        $pending_count = 0;
        if ( !empty($num_posts->$status) )
            $pending_count = $num_posts->$status;
        // build string to match in $menu array
        if ($type == 'post') {
            $menu_str = 'edit.php';
        } else {
            $menu_str = 'edit.php?post_type=' . $type;
        }
        // loop through $menu items, find match, add indicator
        foreach( $menu as $menu_key => $menu_data ) {
            if( $menu_str != $menu_data[2] )
                continue;
            $menu[$menu_key][0] .= " <span class='awaiting-mod count-$pending_count'><span class='pending-count'>" . number_format_i18n($pending_count) . '</span></span>';
        }
        return $menu;
    }
    /**
    * Shows pending for trail itinerares
    */
    public function show_pending_number_itinerary( $menu ) {
        $type = "itinerary";
        $status = "pending";
        $num_posts = wp_count_posts( $type, 'readable' );
        $pending_count = 0;
        if ( !empty($num_posts->$status) )
            $pending_count = $num_posts->$status;
        // build string to match in $menu array
        if ($type == 'post') {
            $menu_str = 'edit.php';
        } else {
            $menu_str = 'edit.php?post_type=' . $type;
        }
        // loop through $menu items, find match, add indicator
        foreach( $menu as $menu_key => $menu_data ) {
            if( $menu_str != $menu_data[2] )
                continue;
            $menu[$menu_key][0] .= " <span class='awaiting-mod count-$pending_count'><span class='pending-count'>" . number_format_i18n($pending_count) . '</span></span>';
        }
        return $menu;
    }
    /**
    * Shows pending for trail itinerares
    */
    public function show_pending_number_trail_route( $menu ) {
        $type = "trail-route";
        $status = "pending";
        $num_posts = wp_count_posts( $type, 'readable' );
        $pending_count = 0;
        if ( !empty($num_posts->$status) )
            $pending_count = $num_posts->$status;
        // build string to match in $menu array
        if ($type == 'post') {
            $menu_str = 'edit.php';
        } else {
            $menu_str = 'edit.php?post_type=' . $type;
        }
        // loop through $menu items, find match, add indicator
        foreach( $menu as $menu_key => $menu_data ) {
            if( $menu_str != $menu_data[2] )
                continue;
            $menu[$menu_key][0] .= " <span class='awaiting-mod count-$pending_count'><span class='pending-count'>" . number_format_i18n($pending_count) . '</span></span>';
        }
        return $menu;
    }

    /**
     * Options page callback
     */
    public function create_trail_story_menu_page() {
        // Set class property
        $this->trail_story_options = get_option( 'trail_story_option' );
        ?>
        <div class="wrap">
            <h1>Interactive Geo Trail Map</h1>
            <form method="post" action="options.php">

            <?php
                // This prints out all hidden setting fields
                settings_fields( 'trail_story_options_group' );
                do_settings_sections( 'trail-story-options-admin' );
                submit_button('Save All Options');
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
        global $geo_mashup_options;
        register_setting(
            'trail_story_options_group', // Option group
            'trail_story_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'trail_story_options_section', // ID
            '', // Title
            array( $this, 'print_option_info' ), // Callback
            'trail-story-options-admin' // Page
        );

        add_settings_section(
            'trail_story_option', // ID
            '', // Title
            array( $this, 'trail_story_option_callback' ), // Callback
            'trail-story-options-admin', // Page
            'trail_story_options_section' // Section
        );
      
       
    }
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        /*if( isset( $input['trail_story_option'] ) )
            $new_input['trail_story_option'] = absint( $input['trail_story_option'] );
        if( isset( $input['trail_story_setting'] ) )
            $new_input['trail_story_setting'] = absint( $input['trail_story_setting'] );
        */
        return $input;
    }
    /**
     * Print the Section text
     */
    public function print_option_info() { ?>
        <div id="plugin-info-header" class="plugin-info header">
            <div class="plugin-info content">
                <hr>
                    <h3><strong>Interactve Geo Trail Mapâ„¢</strong> developed by <a target="_blank" href="http://www.orionweb.net">Orion Group LLC</a>.</h3> 
                    <h4>For help or support please email <a href="mailto:support@orionweb.net">support@orionweb.net</a></h4>
                <hr>
                <div class="credits">
                    <div>
                        <h4>Lead Developers</h4>
                        <a target="_blank" href="http://andrewgunn.xyz">Andrew Gunn</a>
                        &nbsp;â€¡&nbsp;
                        <a target="_blank" href="https:/github.com/fallen-rve">Ryan Van Ess</a>
                    </div>
                    <hr>
                    <!--<div>
                        <h4>Testing and Development</h4>
                        <a target="_blank" href="https://profiles.wordpress.org/jvalcq/">Jon Valcq</a>
                        &nbsp;â€¡&nbsp;
                        <a target="_blank" href="#">Josh Selk</a>
                    </div>
                    <hr>
                    <div>
                        <h4>Project Manager</h4>
                        <a target="_blank" href="http://kevinabarnes.com">Kevin Barnes</a>
                    </div>
                    <hr> -->
                    <br>
                </div>
            </div>
    <?php }

    /**
     * Get the settings option array and print one of its values
     */
    public function trail_story_option_callback() {
        //Get plugin options
        global $trail_story_options, $geo_mashup_options;
        // Enqueue Media Library Use
        wp_enqueue_media();
        
        // Get trail story options
        $trail_story_options = (array) get_option( 'trail_story_options' ); 
        //var_dump($trail_story_options);?>
        <div>
            <div>
                <h3>Plugin Uninstallation</h3>
                <hr>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <?php //$key = 'delete_data'; ?>
                            <th scope="row">
                                Uninstall Posts
                            </th>
                            <td>
       
                                <fieldset><?php $key = 'delete_posts'; 
                                    //var_dump($trail_story_options[$key]);?>
                                    <label for="trail_story_options[<?php echo $key; ?>]">
                                        <input id='trail_story_options[<?php echo $key; ?>]' name="trail_story_options[<?php echo $key; ?>]" type="checkbox" value="1" <?php checked(1, $trail_story_options[$key], true ); ?> />
                                        Delete all posts, attachments, and plugin settings when uninstalled.
                                    </label>
                                <p class="description">Use this as a factory restore.</p>
                                
                            </td>
                        </tr>
                        <!--<tr>
                            <?php //$key = 'delete_data'; ?>
                            <th scope="row">
                                Force Deletion
                            </th>
                            <td>
                                <fieldset><?php //$key = 'delete_forced'; 
                                    //var_dump($trail_story_options["delete_forced"]); ?>

                                    <label for="trail_story_options[<?php //echo $key; ?>]">
                                        <input id='trail_story_options[<?php //echo $key; ?>]' name="trail_story_options[<?php echo $key; ?>]" type="checkbox" value="1" <?php checked(1, $trail_story_options[$key], true ); ?> />
                                        Bypass trash can and delete permanently.
                                    </label>
                                <!--<p class="description">Use this as a factory restore.</p>
                                
                            </td>
                        </tr>-->
                        <tr>
                            <th scope="row">
                                Uninstall Database
                            </th>
                            <td>
                                <fieldset><?php $key = 'delete_dbo'; 
                                    //var_dump(isset($trail_story_options["delete_dbo"])); ?>

                                    <label for="trail_story_options[<?php echo $key; ?>]">
                                        <input id='trail_story_options[<?php echo $key; ?>]' name="trail_story_options[<?php echo $key; ?>]" type="checkbox" value="1" <?php checked(1, $trail_story_options[$key], true ); ?> />
                                        Drop all database tables and plugin data when uninstalled.
                                    </label>
                                <p class="description"><span class="icon warn">&nbsp;</span>Use at your own risk. A backup is recommended beforehand.</p>
                                </fieldset>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                </p>
                <!--<p>
                    <?php //$key = 'text'; ?>
                    <label for="trail_story_options[text]">Text</label>
                    <input id='trail_story_options[text]' name="trail_story_options[text]" type="text"
                     value="<?php //echo $trail_story_options[$key]; ?>" />
                    
                </p>-->
                <?php submit_button('Save All Options'); ?>
            </div>
            <p><hr></p>
            <div id="custom-kml">
                <h3><strong>Custom KML Layers</strong></h3>
                <p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <?php //$key = 'delete_data'; ?>
                            <th scope="row">
                                Enable Custom KMLs
                            </th>
                            <td>
       
                                <fieldset><?php $key = 'enable_kmls'; 
                                            //var_dump($trail_story_options[$key]); ?>

                                    <label for="trail_story_options[<?php echo $key; ?>]">
                                        <input id='trail_story_options[<?php echo $key; ?>]' name="trail_story_options[<?php echo $key; ?>]" type="checkbox" value="1" <?php checked(1, $trail_story_options[$key], true ); ?> />
                                        
                                    </label>
                                <p class="description">You know you wanna.</p>
                                
                            </td>
                        </tr>
                        <tr>
                            <?php //$key = 'delete_data'; ?>
                            <th scope="row">
                                KML File URL
                            </th>
                            <td>
                                <fieldset><?php $key = 'custom_kml_layers';
                                                                            //var_dump($trail_story_options[$key]); ?>

                                    <label for="trail_story_options[<?php echo $key; ?>]">
                                    Sepearte URLs by commma
                                    </label>
                                    <input type="text" style="width:700px; height:100px;" name="trail_story_options[<?php echo $key; ?>]" id="trail_story_options[<?php echo $key; ?>]" value="<?php echo $trail_story_options[$key]; ?>" />
                                <!--<p class="description">Use this as a factory restore.</p>-->
                                
                            </td>
                        </tr>
                    </tbody>
                </table></p>
                <?php submit_button('Save All Options'); ?>
            </div>
            <p><hr></p>
            <div>
                <h2><strong>Trail Map Options</strong></h2>
            <div class="icon-wrapper">
                <div class="icon-header">

                    <div class="icon-header-post-type-title">
                        <h3><?php _e( 'Custom Map Markers', 'geo-mashup-trail-story-add-on' ); ?></h3>
                    </div>

                    <div class="icon-header-image-title">
                        <h3><?php _e('Image', 'geo-mashup-trail-story-add-on') ?></h3>
                    </div>

                </div>

                <h3><?php _e('Post Types','geo-mashup-trail-story-add-on'); ?></h3>

        <?php foreach( get_post_types( array( 'show_ui' => true ), 'objects' ) as $post_type ) : ?>
            <?php if ( in_array( $post_type->name, $geo_mashup_options->get( 'overall', 'located_post_types' ) ) ) { ?>

                <div class="icon-content">
                    <div class="<?php echo $post_type->name; ?>">
                        <div class="icon-post-type">
                            <h4><?php _e( esc_html( $post_type->labels->name ) ); ?></h4>
                        </div>
                        <div class="icon-image-buttons">

                            <?php $key = 'trail-story-add-icon-text-box-' . $post_type->name; ?>

                            <div class="trail-story-icon-button-container">
                                <?php // Holster for Image url (Hidden) ?>
                                <input type="hidden" 
                                       id="trail-story-add-icon-text-box-<?php echo $post_type->name; ?>" 
                                       class="trail-story-icon-url" 
                                       name="trail_story_options[trail-story-add-icon-text-box-<?php echo $post_type->name; ?>]" 
                                       value="<?php echo $trail_story_options['trail-story-add-icon-text-box-'. $post_type->name ]; ?>"/>

                                <?php // Button to add Image icon ?>
                                <input type="button" 
                                       id="trail-story-add-icon-button-<?php echo $post_type->name; ?>" 
                                       class="button trail-story-add-icon-button <?php echo $trail_story_options[$key] ? 'hidden' : ''; ?>" 
                                       name="trail-story-add-icon-button" 
                                       value="<?php _e( 'Add Image', 'geo-mashup-trail-story-add-on' ); ?>"/>

                                <?php // Button to remove Image icon ?>
                                <input type="button" 
                                       id="trail-story-remove-icon-button-<?php echo $post_type->name; ?>" 
                                       class="button trail-story-remove-icon-button <?php echo !$trail_story_options[$key] ? 'hidden' : ''; ?>" 
                                       name="trail-story-add-icon-button" 
                                       value="<?php _e( 'Remove Image', 'geo-mashup-trail-story-add-on' ); ?>"/>

                                       <div style="clear:both;height:0;"></div>
                            </div>

                            <div class="trail-story-icon-image-container">

                            <?php if( $trail_story_options[ $key ] ){ ?>

                                <img src="<?php echo esc_attr( $trail_story_options[ $key ] ); ?>" alt="" style="max-width:100%;" />

                            <?php } ?>

                            </div>

                        </div>
                        <div style="clear:both;height:0;"></div>
                    </div>
                </div>
            <?php } ?>
        <?php endforeach; // post types ?>

        <?php $include_taxonomies = $geo_mashup_options->get( 'overall', 'include_taxonomies' ); ?>

        <h3 style="text-decoration:underline;"><?php _e('Post Type Categories','geo-mashup-trail-story-add-on'); ?></h3>

        <?php if ( !empty( $include_taxonomies ) and !defined( 'GEO_MASHUP_DISABLE_CATEGORIES' ) ) : ?>
            <?php foreach( $include_taxonomies as $include_taxonomy ) : ?>

                <?php $taxonomy_object = get_taxonomy( $include_taxonomy ); ?>
                <?php $taxonomy_options = $geo_mashup_options->get( 'global_map', 'term_options', $include_taxonomy ); ?>
                <?php $terms = get_terms( $include_taxonomy, array( 'hide_empty' => false ) ); ?>

                <h3 style="font-weight:bold;"><?php echo $taxonomy_object->label; ?></h3>

                <?php if ( is_array($terms) ) : ?>
                    <?php foreach( $terms as $term ) : ?>
                        <div class="icon-content">
                            <div class="<?php echo $term->slug; ?>">
                                <div class="icon-post-type">
                                    <h4><?php _e( esc_html( $term->name ) ); ?></h4>
                                </div>
                                <div class="icon-image-buttons">
                            
                                    <?php $key = 'trail-story-add-icon-text-box-' . $term->slug; ?>

                                    <div class="trail-story-icon-button-container">
                                        <?php // Holster for Image url (Hidden) ?>
                                        <input type="hidden" 
                                               id="trail-story-add-icon-text-box-<?php echo $term->slug; ?>" 
                                               class="trail-story-icon-url" 
                                               name="trail_story_options[trail-story-add-icon-text-box-<?php echo $term->slug; ?>]" 
                                               value="<?php echo $trail_story_options['trail-story-add-icon-text-box-'. $term->slug ]; ?>"/>

                                        <?php // Button to add Image icon ?>
                                        <input type="button" 
                                               id="trail-story-add-icon-button-<?php echo $term->slug; ?>" 
                                               class="button trail-story-add-icon-button <?php echo $trail_story_options[$key] ? 'hidden' : ''; ?>" 
                                               name="trail-story-add-icon-button" 
                                               value="<?php _e( 'Add Image', 'geo-mashup-trail-story-add-on' ); ?>"/>

                                        <?php // Button to remove Image icon ?>
                                        <input type="button" 
                                               id="trail-story-remove-icon-button-<?php echo $term->slug; ?>" 
                                               class="button trail-story-remove-icon-button <?php echo !$trail_story_options[$key] ? 'hidden' : ''; ?>" 
                                               name="trail-story-add-icon-button" 
                                               value="<?php _e( 'Remove Image', 'geo-mashup-trail-story-add-on' ); ?>"/>

                                               <div style="clear:both;height:0;"></div>
                                    </div>

                                    <div class="trail-story-icon-image-container">

                                    <?php if( $trail_story_options[ $key ] ){ ?>

                                        <img src="<?php echo esc_attr( $trail_story_options[ $key ] ); ?>" alt="" style="max-width:100%;" />

                                    <?php } ?>

                                    </div>

                                </div>
                                <div style="clear:both;height:0;"></div>
                            </div>
                        </div>

                    <?php endforeach; // terms as term ?>   
                <?php endif; ?>
            
            <?php endforeach; // include taxonomies as included taxonomy ?>
        <?php endif; ?>
            <div style="clear:both;height:0;"></div>
        </div>
        <br>
        <hr>
        <h4><a target="_blank" href="<?php //echo IGTM_PDF_URL; ?>">Download</a> plugin How-To Guide and Documentation</h4>
        <div class="mapsmarker">Find more Map icons at <a href="https://mapicons.mapsmarker.com/" target="_blank">Maps Marker's</a> website</div>

    <?php }
    // TODO: Input fields for KMLs
    /**
     * Get the settings option array and print one of its values
     */
    /*public function trail_story_setting_callback() {
        //Get plugin options
        global $trail_story_options, $geo_mashup_options;
        $trail_story_options = (array) get_option( 'trail_story_settings' );
        if( isset( $trail_story_options['trail_story_option'] ) ) { ?>
            <input type="checkbox" id="trail_story_settings" name="trail_story_settings[trail_story_setting]" value="1" <?php checked( 1, $trail_story_options['trail_story_setting'], false ); ?> />
        
        <?php } else { ?>
            <input type="checkbox" id="trail_story_settings" name="trail_story_settings[trail_story_setting]" value="1" <?php checked( 1, $trail_story_options['trail_story_setting'], false ); ?> />
       
        <?php }
        echo $html;
    }*/
}
if( is_admin() )
    $trail_story = new TrailStorySettings();