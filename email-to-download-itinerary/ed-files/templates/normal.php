<div class="etd_dw_form">
    <?php if( $title != 'no' ) { ?>
    <h4><?php echo $post->post_title ?></h4>
    <?php } ?>
    <?php if( $content != 'no' ) { ?>
    <div class="etd_dw_con">
        <?php echo $post->post_content; ?>
    </div>
    <?php } ?>
    <form action="#" method="post">
        <!--input type="hidden" id="ed_attachment_id" value="<?php echo $ed_file_id ?>"-->
        <input type="hidden" class="ed_attachment_url" value="<?php echo $ed_file ?>">
        <input type="hidden" class="ed_download_id" value="<?php echo $id ?>">
        <p><?php _e( 'Please give an email adress where we should send the download link.', 'email-to-download' ); ?></p>
        <div cellpadding="5" cellspacing="5">
            <dl>
                <!--td><?php _e( 'Email' ) ?></td-->
                <dd>
                    <input type="text" class="ed_email" placeholder="<?php _e( 'Your email here', 'email-to-download' ) ?>">
                    <span class="ed_error">&nbsp;</span>
                </dd>
            </dl>
            <dl>
                <dd><input type="checkbox" class="ed_subscribed" />
                    &nbsp;Yes, send email announcements on Ice Age Trail Alliance news, volunteers events and more.</dd>
            </dl>
            <dl>
                <!--td>&nbsp;</td-->
                <dd><input type="button" class="etd_submit" value="<?php _e( 'Get Download Link', 'email-to-download' ) ?>"></dd>
            </dl>
        </div>
    </form>
</div>