<?php
/**
 * ShortCodes Class
 *
 * @Author: Ashok Kumar Nath (Bappi D Great)
 * @package: Builder
 */

if ( ! defined( 'ABSPATH' ) ) die( NP_HACK_MSG );

if( ! class_exists( 'NP_SHORTCODES' ) ){
    /**
     * Class NP_SHORTCODES
     *
     * @since 1.0.1
     */
    class NP_SHORTCODES{
        
        /**
         * ShortCodes list
         * @var ARRAY array of shortcodes
         * @since 1.0.1
         */
        public $_shortcodes = array();
        
        /**
         * ShortCodes Builder param
         * @since 1.0.1
         */
        public $_builder = array();
        
        /**
         * Unique ID
         * @since 1.0.1
         */
        private $_uniqueID;
        
        /**
         * Class Constructor
         *
         * @since 1.0.1
         */
        public function __construct( $shortcodes, $builder ) {
            $this->_shortcodes = $shortcodes;
            $this->_builder = $builder;
            $this->_uniqueID = NP_HELPER::get_unique_number( 'np_sc_builder_' );
            if( $this->_builder['builder'] ) {
                add_action( 'media_buttons', array( &$this, 'np_shortcode_builder_button' ) );
                add_action( 'admin_enqueue_scripts', array( &$this, 'register_builder_content' ) );
            }
        }
        
        /**
         * Register shortcodes
         *
         * @since 1.0.1
         */
        public function register() {
            foreach( $this->_shortcodes as $key => $shortcode ){
                if( class_exists( $shortcode ) ){
                    $codec = new $shortcode;
                    add_shortcode( $key, array( $codec, 'process_shortcode' ) );
                }
            }
        }
        
        /**
         * Add ShortCode builder button in editor
         *
         * @since 1.0.1
         */
        public function np_shortcode_builder_button() {
            ?>
            <div id="<?php echo $this->_uniqueID ?>" class="np_sc_builder_content">
                <div class="np-selector">
                    <h3>Select a ShortCode</h3>
                    <hr>
                    <select class="np-select">
                        <option value="">Select a shortcode</option>
                        <?php foreach( $this->_builder['codes'] as $key => $val ) { ?>
                        <option value="<?php echo $key ?>"><?php echo $val ?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <div class="np_sc_cb">
                        <?php foreach( $this->_builder['atts'] as $key => $val ) { ?>
                        <table cellpadding="5" cellspacing="5" id="<?php echo $key ?>" class="np_sc_builder_tbl">
                            <?php
                                foreach( $this->_builder['atts'][$key] as $elem ) {
                                    echo NP_FORM_HELPER::element( 'tr', $elem['name'], $elem['type'], $elem['term'], $elem['default'], $elem['default'], $elem['default'], isset( $elem['options'] ) ? $elem['options'] : array() );
                                }
                            ?>
                        </table>
                        <?php } ?>
                    </div>
                    <input type="button" class="button button-primary insert_editor" value="Insert Into Editor">
                </div>
            </div>
            <a data-src="<?php echo $this->_uniqueID ?>" href="javascript:;" class="button np-shortcode-builder-button" title="<?php echo $this->_builder['label'] ?>"><span class="<?php echo $this->_builder['icon'] ?>"></span> <?php echo $this->_builder['label'] ?></a>
            <?php
        }
        
        /**
         * Enqueue builder js
         *
         * @since 1.0.1
         */
        public function register_builder_content() {
            wp_register_script( 'np-tinymce', NP_BUILDER_URI . 'assets/js/np.tinymce.js' );
            wp_enqueue_script( 'np-tinymce' );
        }
        
    }
}

