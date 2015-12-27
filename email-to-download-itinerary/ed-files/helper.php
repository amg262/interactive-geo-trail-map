<?php

function ed_get_sidebar() {
    ob_start();
    ?>
    <div class="postbox-container" id="postbox-container-1">
        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Want MailChimp Integration?', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <p><?php _e( 'Get our awesome plugin to integrate your email to download plugin with mailchimp. People who provides an emaila ddress to you, save that email into a mailchimp list instantly.', 'email-to-download' ) ?></p>
                <p><a href="https://duogeek.com/products/plugins/mailchimp-integration-email-to-download/" target="_blank" class="button button-primary"><?php _e( 'Download Mailchimp Integration', 'email-to-download' ) ?></a></p>
            </div>
        </div>
        
        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Support / Report a bug', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <p><?php _e( 'Please feel free to let us know if you got any bug to report. For any type of support query, be our free member to get access on our support forum. Free members get unlimited support for all of our products.', 'email-to-download' ) ?></p>
                <p><a href="https://duogeek.com/support/forum/duogeek-products/" target="_blank" class="button button-primary"><?php _e( 'Get Support', 'email-to-download' ) ?></a></p>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Buy us a coffee', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <p><?php _e( 'If you like the plugin, please buy us a coffee to inspire us to develop further.', 'email-to-download' ) ?></p>
                <p>
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="CC5EW4KZLVQNY"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>

                </p>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Rate us', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <p><?php _e( 'Please give us a 5 star review, if you like our products and support.', 'email-to-download' ) ?></p>
                <p class="star-icons">
                    <a href="https://wordpress.org/support/view/plugin-reviews/email-to-download" target="_blank">
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                    </a>
                </p>
            </div>
        </div>
        
        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Subscribe to our NewsLetter', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <p><?php _e( "Please join our newsletter program to get updates, offers, promotion and blog post. We don't send any spam emails and your email address is totally secured.", 'email-to-download' ) ?></p>
                <p>
                    <center><div id="mc_embed_signup"><form id="mc-embedded-subscribe-form" class="validate" action="//duogeek.us9.list-manage.com/subscribe/post?u=8c05a468e4f384bd52b5ec8c9&amp;id=a52e938757" method="post" name="mc-embedded-subscribe-form" novalidate="" target="_blank"><div id="mc_embed_signup_scroll"><input id="mce-EMAIL" class="email" style="width: 100%; text-align: center;" name="EMAIL" required="" type="email" value="" placeholder="Enter Your Email Address" /><br><br><!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups--><div style="position: absolute; left: -5000px;"><input tabindex="-1" name="b_8c05a468e4f384bd52b5ec8c9_a52e938757" type="text" value="" /></div><div><input id="mc-embedded-subscribe" class="button avia-button  avia-icon_select-yes-left-icon avia-color-theme-color-subtle avia-size-large" name="subscribe" type="submit" value="Subscribe" /></div></div></form></div></center>
                </p>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Join us on facebook', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FDuo-Geek%2F329994980535258&amp;width=250&amp;height=258&amp;colorscheme=dark&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false&amp;appId=723137171103956" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:250px; height:258px;" allowTransparency="true"></iframe>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><span><?php _e( 'Follow us on twitter', 'email-to-download' ) ?></span></h3>
            <div class="inside centerAlign">
                <a href="https://twitter.com/duogeekdev" target="_blank" class="button button-secondary">Follow @duogeekdev <span class="dashicons dashicons-twitter" style="position: relative; top: 3px"></span></a>
            </div>
        </div>
    </div>
    
    <style>
        .centerAlign{text-align: center}
        .centerAlign a{text-decoration: none}
    </style>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
}