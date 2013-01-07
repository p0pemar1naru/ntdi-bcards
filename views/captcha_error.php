<!--<p><?php //echo '<pre>POST[] from create action: '; print_r($_POST); echo '</pre>';  ?></p>-->

<p>Sorry... incorrect CAPTCHA.<br />Please hit the edit button to try again.<br /><br />Message::<?php echo $error; ?></p>
<div id="nt_wpecard_viewcard">
    <div id="nt_wpecard_confirm">
        <form action="<?php echo esc_url($formAction); ?>" method="post">
             <?php wp_nonce_field($bcard->getNoncePreviewAction(), NTDI_NONCE_NAME); ?>
            <input type="hidden" name="nt_wpecars_post" value="<?php echo esc_attr($bcard->getPostID()); ?>" />
            <input type="hidden" name="ntdi_image" value="<?php echo esc_attr($bcard->getImageID()); ?>" />
            
            <input type="hidden" name="ntdi_booking" value="<?php echo esc_attr($bcard->getBookingNumber()); ?>" />
            <input type="hidden" name="ntdi_dob" value="<?php echo esc_attr($bcard->getSenderDob()); ?>" />
            
            <input type="hidden" name="ntdi_message" value="<?php echo esc_attr($bcard->getMessageText()); ?>" />
            <input type="hidden" name="ntdi_sname" value="<?php echo esc_attr($bcard->getSenderName()); ?>" />
            <input type="hidden" name="ntdi_semail" value="<?php echo esc_attr($bcard->getSenderEmail()); ?>" />
            <input type="hidden" name="ntdi_rname" value="<?php echo esc_attr($bcard->getRecipientName()); ?>" />
            <input type="hidden" name="ntdi_remail" value="<?php echo esc_attr($bcard->getRecipientEmail()); ?>" />
            <input type="hidden" name="card_hash" value="<?php echo $bcard->generateUniqueHash(NULL, NONCE_SALT, 10); ?>" />

            <input type="submit" name="ntdi_create" value="Edit Card" />
        </form>
    </div>
</div>
<div style="clear:both;"></div>