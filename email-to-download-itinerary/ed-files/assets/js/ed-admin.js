/**
 * ED Admin JS
 *
 * @package Email to Download
 */

;jQuery(function($){
    
    var ED_Plugin = {
            
        init: function() {
            this.fireUploader();
        },
        
        fireUploader: function() {
            $(document).on( 'click', '#ed_uploader_btn', function(e) {
                e.preventDefault();
                
                var file_frame, image_data;
                
                if ( undefined !== file_frame ) {
                    file_frame.open();
                    return;
                }
                
                file_frame = wp.media.frames.file_frame = wp.media({
                    frame:    'post',
                    state:    'insert',
                    multiple: false
                });
                
                file_frame.on( 'insert', function() {

                    json = file_frame.state().get( 'selection' ).first().toJSON();
                    
                    if ( 0 > $.trim( json.url.length ) ) {
                        return;
                    }
                    
                    //console.log( json.url );
                    
                    $('#ed_item').val(json.url);
                    //$('#ed_id').val(json.id);
             
                });
             
                // Now display the actual file_frame
                file_frame.open();
                
                //tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
                
                return false;
            });
        }
            
    };
        
    ED_Plugin.init();
    
});