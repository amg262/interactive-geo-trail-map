/*
* Function that parses input fields
*/
jQuery(document).ready(function($) {

    $('#trail-story-post').submit(function($) {

        /*if ( document.trail_story_post.quick_post_title.value == null ||
                 document.trail_story_post.quick_post_title.value == "" ) {
            alert('All fields are required!');
            return false;

        } else if ( document.trail_story_post.quick_post_content.value == null ||
                         document.trail_story_post.quick_post_title.value == "" ) {
            alert('All fields are required!');
            return false;
        } else if ( document.trail_story_post.geo_mashup_location.value == null ||
                         document.trail_story_post.geo_mashup_location.value == "" )
            alert('Map location is required for submission!');
            return false;*/

    });
    
});

/**
* 
*/
jQuery(function($){


    var file_frame;

    // "mca_tray_button" is the ID of my button that opens the Media window
    $(".icon-content").each( function(){

        var $postType    = $( "div", this ).attr('class'),
            postTypes    = [];

        postTypes.push($postType);

        $('.trail-story-add-icon-button').on('click', function( event ){

            event.preventDefault();

            for (i = 0; i < postTypes.length; ++i) {

                var lookForID = 'trail-story-add-icon-button-' + postTypes[i];

                if( lookForID == $( this ).attr('id') ){

                    $iconWrapper  = $('.icon-wrapper .icon-content'),
                    $postWrapper  = $iconWrapper.find("." + postTypes[i]),
                    $addImgLink   = $($postWrapper).find('.trail-story-add-icon-button'),
                    $delImgLink   = $($postWrapper).find( '.trail-story-remove-icon-button'),
                    $imgContainer = $($postWrapper).find( '.trail-story-icon-image-container'),
                    $imgIdInput   = $($postWrapper).find( '.trail-story-icon-url' );
                        // console.log($imgContainer);

                    
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: $( this ).data( 'uploader_title' ),
                        button: {
                            text: $( this ).data( 'uploader_button_text' ),
                        },
                        multiple: false  
                    });

                    file_frame.on( 'select', function() {

                        attachment = file_frame.state().get('selection').first().toJSON();

                        // but you could get the URL instead by doing something like this:
                        $imgIdInput.val(attachment.url);

                        // Send the attachment URL to our custom image input field.
                        $imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

                        // Hide the add image link
                        $addImgLink.addClass( 'hidden' );

                        // Unhide the remove image link
                        $delImgLink.removeClass( 'hidden' );

                    });

                    file_frame.open();

                }
            }

        });

        // DELETE IMAGE LINK
        $('.trail-story-remove-icon-button').on( 'click', function( event ){

            for (i = 0; i < postTypes.length; ++i) {

                var lookForID = 'trail-story-remove-icon-button-' + postTypes[i];

                if( lookForID == $( this ).attr('id') ){

                    $iconWrapper  = $('.icon-wrapper .icon-content'),
                    $postWrapper  = $iconWrapper.find("." + postTypes[i]),
                    $addImgLink   = $($postWrapper).find('.trail-story-add-icon-button'),
                    $delImgLink   = $($postWrapper).find( '.trail-story-remove-icon-button'),
                    $imgContainer = $($postWrapper).find( '.trail-story-icon-image-container'),
                    $imgIdInput   = $($postWrapper).find( '.trail-story-icon-url' );

                    event.preventDefault();

                    // Clear out the preview image
                    $imgContainer.html( '' );

                    // Un-hide the add image link
                    $addImgLink.removeClass( 'hidden' );

                    // Hide the delete image link
                    $delImgLink.addClass( 'hidden' );

                    // Delete the image id from the hidden input
                    $imgIdInput.val( '' );

                    
                }
            }
        });

    });

}); //.ready