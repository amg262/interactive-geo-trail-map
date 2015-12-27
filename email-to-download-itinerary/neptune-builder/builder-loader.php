<?php
/**
 * This is builder loader class
 *
 * @Author: Ashok Kumar Nath (Bappi D Great)
 * @package: Builder
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) die( NP_HACK_MSG );

define( 'NP_BUILDER_VERSION', '1.0.1' );
define( 'NP_BUILDER_DIR', plugin_dir_path( __FILE__ ) );
define( 'NP_BUILDER_URI', trailingslashit( plugins_url( '', __FILE__ ) ) );

if( ! function_exists( 'np_load_classes' ) ){
    /**
     * Autoload class callback
     *
     * @since 1.0.1
     * @param Class name
     */
    function np_load_classes( $class ){
        
        // List of class files
        $classes = array(
                    'NP_CPT' => NP_BUILDER_DIR . 'classes/class.custom-post-type.php',
                    'NP_OPTIONS' => NP_BUILDER_DIR . 'classes/class.np-options.php',
                    'NP_SHORTCODES' => NP_BUILDER_DIR . 'classes/class.shortcodes.php',
                    'NP_HELPER' => NP_BUILDER_DIR . 'classes/class.np.helper.php',
                    'NP_FORM_HELPER' => NP_BUILDER_DIR . 'classes/class.np.form.helper.php'
                );
        
        // Check if class file exists
        if( isset( $classes[$class] ) ){
            require_once $classes[$class];
        }
        
    }
    
    spl_autoload_register( 'np_load_classes' );
}

if( ! function_exists( 'np_enqueue_scripts' ) ){
    /**
     * Enqueue the required css and js files for the framework
     *
     * @since 1.0.1
     */
    add_action( 'admin_enqueue_scripts', 'np_admin_enqueue_scripts' );
    add_action( 'wp_enqueue_scripts', 'np_front_enqueue_scripts' );
    
    function np_admin_enqueue_scripts() {
        
        wp_enqueue_style(
                        'np-font-awesome',
                        NP_BUILDER_URI . 'assets/font-awesome-4.4.0/css/font-awesome.min.css',
                        '',
                        NP_BUILDER_VERSION
                    );
        wp_enqueue_style(
                        'np-colorbox-css',
                        NP_BUILDER_URI . 'assets/colorbox/colorbox.css',
                        '',
                        NP_BUILDER_VERSION
                    );
        wp_enqueue_style(
                        'nep-strap-css',
                        NP_BUILDER_URI . 'assets/css/nepstrap.css',
                        '',
                        NP_BUILDER_VERSION
                    );
        wp_enqueue_script(
                            'np-colorbox-script',
                            NP_BUILDER_URI . 'assets/colorbox/jquery.colorbox-min.js',
                            array(
                                'jquery'
                            ),
                            NP_BUILDER_VERSION,
                            true
                        );
        
        if( defined( 'NP_DEV_MODE' ) && NP_DEV_MODE == TRUE ){
            wp_enqueue_style(
                            'np-builder-style',
                            NP_BUILDER_URI . 'assets/css/np-builder.css',
                            '',
                            NP_BUILDER_VERSION
                        );
            wp_enqueue_script(
                            'np-builder-script',
                            NP_BUILDER_URI . 'assets/js/np-builder.js',
                            array(
                                'jquery'
                                // add mode dependency here
                            ),
                            NP_BUILDER_VERSION,
                            true
                        );
        }else{
            wp_enqueue_style(
                            'np-builder-style',
                            NP_BUILDER_URI . 'assets/css/np-builder.min.css',
                            '',
                            NP_BUILDER_VERSION
                        );
            wp_enqueue_script(
                            'np-builder-script',
                            NP_BUILDER_URI . 'assets/js/np-builder.min.js',
                            array(
                                'jquery'
                                // add mode dependency here
                            ),
                            NP_BUILDER_VERSION,
                            true
                        );
        }
    }
    
    function np_front_enqueue_scripts() {
        
        wp_enqueue_style(
                        'np-font-awesome',
                        NP_BUILDER_URI . 'assets/font-awesome-4.4.0/css/font-awesome.min.css',
                        '',
                        NP_BUILDER_VERSION
                    );
        wp_enqueue_style(
                        'nep-strap-css',
                        NP_BUILDER_URI . 'assets/css/nepstrap.css',
                        '',
                        NP_BUILDER_VERSION
                    );
        
        if( defined( 'NP_DEV_MODE' ) && NP_DEV_MODE == TRUE ){
            wp_enqueue_style(
                            'np-builder-style',
                            NP_BUILDER_URI . 'assets/css/np-builder.css',
                            '',
                            NP_BUILDER_VERSION
                        );
            wp_enqueue_script(
                            'np-builder-script',
                            NP_BUILDER_URI . 'assets/css/np-builder.js',
                            array(
                                'jquery'
                                // add mode dependency here
                            ),
                            NP_BUILDER_VERSION,
                            true
                        );
        }else{
            wp_enqueue_style(
                            'np-builder-style',
                            NP_BUILDER_URI . 'assets/css/np-builder.min.css',
                            '',
                            NP_BUILDER_VERSION
                        );
            wp_enqueue_script(
                            'np-builder-script',
                            NP_BUILDER_URI . 'assets/js/np-builder.min.js',
                            array(
                                'jquery'
                                // add mode dependency here
                            ),
                            NP_BUILDER_VERSION,
                            true
                        );
        }
    }
}
