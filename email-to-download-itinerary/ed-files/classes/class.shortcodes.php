<?php
/**
 * ED ShortCodes
 *
 * @package Email to Download
 */
if( ! class_exists( 'ed_download_file' ) ) {
    class ed_download_file{
        public function process_shortcode( $atts ) {
            $atts = shortcode_atts( array(
		'title' => 'yes',
                'content' => 'yes',
		'id' => '',
                'style' => 'normal'
            ), $atts, 'ed_download_file' );
            
            extract( $atts );
            
            if( $id == '' ) return __( 'Please provie an ID of your file.', 'email-to-download' );
            
	    $post = get_post( $id );
	    $ed_file = get_post_meta( $id, '_ed_file_', true );
            //$ed_file_id = get_post_meta( $id, '_ed_file_id_', true );
	    
            ob_start();
            @include ED_FILES_DIR . "/templates/{$style}.php";
            $output = ob_get_contents();
            ob_end_clean();
            
            return $output;
        }
    }
}