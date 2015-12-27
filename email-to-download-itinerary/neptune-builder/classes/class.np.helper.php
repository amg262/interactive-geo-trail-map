<?php
/**
 * Helper Class
 *
 * @Author: Ashok Kumar Nath (Bappi D Great)
 * @package: Builder
 */

if ( ! defined( 'ABSPATH' ) ) die( NP_HACK_MSG );

if( ! class_exists( 'NP_HELPER' ) ){
    /**
     * Class NP_HELPER
     */
    class NP_HELPER{
        
        /**
         * Get same unique number in each instance (cached)
         *
         * @param $prefix STRING Unique ID prefix
         * @since 1.0.1
         */
        public static function get_unique_number_cached( $prefix = 'np_builder_unique_' ) {
            $unique = wp_cache_get( 'np_builder_unique' );
            if( ! $unique ){
                $unique = self::get_unique_number( $prefix );
                wp_cache_set( 'np_builder_unique', $unique );
            }
            return apply_filters(
                                'np_builder/np_helper/unique_number_cache',
                                $unique,
                                $prefix
                            );
        }
        
        /**
         * Get different unique number in each instance
         *
         * @param $prefix STRING Unique ID prefix
         * @since 1.0.1
         */
        public static function get_unique_number( $prefix = 'np_builder_unique_' ){
            return apply_filters(
                                'np_builder/np_helper/unique_number',
                                $prefix . uniqid( true ),
                                $prefix
                            );
        }
        
        /**
         * Get current page URL
         * @since 1.0.1
         *
         * @param bool          $queryPram if you want to get url with query string, false otherwise
         * @return string       the url of the current page
         */
        public static function curPageURL( $queryPram = true ) {
            $url = 'http';
            
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
                $url .= "s";
            $url .= "://";
            if ($_SERVER["SERVER_PORT"] != "80")
                $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            else
                $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                
            if( ! $queryPram ){
                $url = explode( '?', $url );
                $url = $url[0];
            }
            
            return apply_filters(
                                'np_builder/np_helper/current_page_url',
                                $url,
                                $queryPram
                            );
        }
        
        /**
         * Customized date and time based on wordpress settings
         * @since 1.0.1
         *
         * @param bool|string               $str false if you don't pass any timestamp, otherwise the timestamp value
         * @param bool          $date       true if want to return date based in wordpress settings
         * @param bool          $time       true if want to return time based in wordpress settings
         * @param bool          $zone       true if want to return gmt value based in wordpress settings
         * @return string
         */
        public static function get_date_time_value( $str = false, $date = true, $time = false, $zone = false ){
            $res = '';
            if ( $str == false ) {
                $str = current_time( 'timestamp' );
            }
            if ( $date ) {
                $res .= date_i18n( get_option( 'date_format' ), $str );
            }
            if ( $time ) {
                $res .= ' ' . date_i18n( get_option( 'time_format' ), $str );
            }
            if ( $zone ) {
                $res .= ' UTC ' . ( ( get_option( 'gmt_offset' ) > 0 ) ? '+ ' : '' ) . get_option( 'gmt_offset' );
            }
            return apply_filters(
                                'np_builder/np_helper/get_date_time_value',
                                $res,
                                $str,
                                $date,
                                $time,
                                $zone
                            );
        }
        
        /**
         * Hexa to rgb converter
         * @since 1.0.1
         *
         * @param $hex
         * @return string
         */
        public static function hex2rgb( $hex ) {
            $hex = str_replace( "#", "", $hex );
        
            if( strlen( $hex ) == 3 ) {
                $r = hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) );
                $g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) );
                $b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) );
            } else {
                $r = hexdec (substr ( $hex, 0, 2 ) );
                $g = hexdec( substr( $hex, 2, 2 ) );
                $b = hexdec( substr( $hex, 4, 2 ) );
            }
            $rgb = array( $r, $g, $b );
            return apply_filters(
                                'np_builder/np_helper/hexa_2_rgb',
                                implode( ",", $rgb ),
                                $hex,
                                $rgb
                                );
        }
        
        /**
         * Array of all font awesome icons
         * @since 1.0.1
         *
         * @param $path string      Path to Font Awesome css file
         */
        public static function fontAwesome( $path = '' ){
            
            if( $path == '' ){
                $path = NP_BUILDER_DIR . 'assets/font-awesome-4.4.0/css/font-awesome.min.css';
            }

            $css = file_get_contents( $path );
            preg_match_all( '/\.(fa-(?:\w+(?:-)?)+):before/', $css, $matches );
            $icons = array();
            foreach( $matches[1] as $match ) {
                $readable = ucfirst( str_replace( '-', ' ', substr( $match, 3 ) ) );
                $icons[$match] = $readable;
            }
            
            return apply_filters(
                                'np_builder/np_helper/font_awesome_icons',
                                $icons,
                                $path
                            );
        
        }
        
        /**
         * Save font awesome array into database
         * @since 1.0.1
         *
         * @param $path string      Path to Font Awesome css file
         */
        public static function fontAwesomeUpdate( $path = '' ) {
            if( $path == '' ){
                $path = NP_BUILDER_DIR . 'assets/font-awesome-4.4.0/css/font-awesome.min.css';
            }
            update_option( 'np_update_font_awesome', self::fontAwesome( $path ) );
        }
        
        /**
         * Get saved array of font awesome icons
         * @since 1.0.1
         *
         * @param   $path       string      Path to Font Awesome css file
         * @param   $update     bool        True to run update operation
         */
        public static function getFontAwesome( $path = '', $update = false ) {
            if( $update ){
                self::fontAwesomeUpdate();
            }
            $icons = get_option( 'np_update_font_awesome' );
            if( ! $icons ) {
                self::fontAwesomeUpdate();
                $icons = get_option( 'np_update_font_awesome' );
            }
            return apply_filters(
                                'np_builder/np_helper/get_font_awesome_icons',
                                $icons,
                                $path
                            );
        }
        
    }
}