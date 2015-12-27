<?php
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/**
* Script styles to run jQuery on pages
*/
//add_action( 'wp_enqueue_scripts', 'trail_story_setup_scripts' );

function trail_story_setup_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
}

/**
* Enqueue scripts
*/
//add_action('wp_footer','trail_story_scripts',5);

function trail_story_scripts() { ?>

<?php//$var = get_option('trail_story_option'); ?>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			

    	}); 
	</script>

<?php }

/**
* Enqueue styles
*/
//add_action('init','trail_story_styles',10);

function trail_story_styles() { ?>
<style type="text/css">

<?php if (is_admin()) { ?>

	

<?php } else { ?>
	
	.wp-menu-open.menu-top.menu-icon-trail-condition .wp-submenu.wp-submenu-wrap {
	    border-bottom: 3px inset #4DB270 !important;
	}

	#adminmenu > li > .menu-top.menu-icon-trail-condition {
	    border-bottom: 2px dotted #727171;
	}
	
<?php } ?>
</style>
<?php }