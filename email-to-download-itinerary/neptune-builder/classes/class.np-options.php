<?php

if ( ! defined( 'ABSPATH' ) ) die( NP_HACK_MSG );


/**
 * Options class for TM plugins
 */

if( ! class_exists( 'NP_OPTIONS' ) ){

	/**
	 * Options class
	 *
	 * @since 1.0
	 */
	class NP_OPTIONS{

		/**
		 * Plugin Key Name
		 * 
		 * @since 1.0
		 * @access private
		 * @var STRING
		 */
		private $_option_key;

		/**
		 * Static variable instance
		 * 
		 * @since 1.0
		 * @access private
		 */
		private static $_instance;

		/**
		 * Option array
		 * 
		 * @since 1.0
		 * @access private
		 * @var ARRAY
		 */
		private $_data = array();

		/**
		 * Default Options
		 * 
		 * @since 1.0
		 * @access private
		 * @var ARRAY
		 */
		private $_defaults = array();


		/**
		 * Class Constructor
		 * 
		 * @since 1.0
		 * @access private
		 *
		 * @param STRING $option_key Option Key
		 * @param ARRAY $defaults Array of default values
		 */
		public function __construct ( $option_key, $defauls = array() ) {
			$this->_option_key = $option_key;
			$this->_defaults = $defauls;

			$this->_populate();
		}

		/**
		 * Get instance of object
		 * 
		 * @since 1.0
		 * @access public
		 *
		 * @return OBJECT instance of self object
		 */
		public static function get_instance () {
			if ( ! isset ( self::$_instance ) ) self::$_instance = new NP_Options;
			return self::$_instance;
		}

		/**
		 * Get saved option values
		 * 
		 * @since 1.0
		 * @access public
		 *
		 * @return ARRAY Options array
		 */
		public function get_options () {
			return $this->_data;
		}

		/**
		 * Get a option value
		 * 
		 * @since 1.0
		 * @access public
		 *
		 * @param STRING $name Option Name
		 * @param MIXED $default Optional default return value
		 *
		 * @return MIXED Option Value or Default
		 */
		public function get_option ( $name ) {
			return isset( $this->_data[$name] ) ? $this->_data[$name] : $this->_defaults[$name];
		}

		/**
		 * Set a option value
		 * 
		 * @since 1.0
		 * @access public
		 *
		 * @param STRING $name Option Name
		 * @param MIXED $default Optional default return value
		 */
		public function set_option ( $name, $value ) {
			$this->_data[$name] = $value;
		}

		/**
		 * Set all option values
		 * 
		 * @since 1.0
		 * @access public
		 *
		 * @param ARRAY $values Array of option values to be stored
		 */
		public function set_options ( $values ) {
			if ( ! $values ) return false;
			foreach ( $values as $name => $value ) {
				$this->set_option( $name, $value );
			}
			$this->update();
		}

		/**
		 * Update all option values
		 * 
		 * @since 1.0
		 * @access public
		 */
		public function update () {
			return update_option( $this->_option_key, $this->_data );
		}

		/**
		 * Get all option values
		 * 
		 * @since 1.0
		 * @access private
		 */
		private function _populate () {

			$data = wp_cache_get( $this->_option_key );
			if( $data == '' ){
				$this->_data = get_option( $this->_option_key, $this->_defaults );
				wp_cache_set( $this->_option_key, $this->_data );
			}else{
				$this->_data = $data;
			}

		}

	}

}