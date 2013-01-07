<!--<p><?php //echo '<pre>POST[] from create action: '; print_r($_POST); echo '</pre>'; ?></p>-->
<!--<p><?php //echo '<pre>Bcard Object: '; print_r($bcard); echo '</pre>'; ?></p>-->

<div id="ntdi-bcard-preview">
    <div id="bcards-preview-container">
        <div id="bcards-preview-image">
            <img src="<?php echo esc_url(wp_get_attachment_url($bcard->getImageID())); ?>" />
        </div>
        <div id="bcards-preview-message">
            <p>To: <?php echo esc_html($bcard->getRecipientName()); ?></p>
            <p>From: <?php echo esc_html($bcard->getSenderName()); ?></p>
            <p><?php echo nl2br(esc_textarea(stripslashes($bcard->getMessageText()))); ?></p>
        </div>
    </div>
    <div style="clear:left;"></div>
    <div id="bcards-preview-confirm">
        <form action="<?php echo esc_url($formAction); ?>" method="post">
            <?php wp_nonce_field($bcard->getNoncePreviewAction(), NTDI_NONCE_NAME); ?>
            <input type="hidden" name="nt_wpecars_post" value="<?php echo esc_attr($bcard->getPostID()); ?>" />
            <input type="hidden" name="ntdi_image" value="<?php echo esc_attr($bcard->getImageID()); ?>" />

            <input type="hidden" name="ntdi_booking" value="<?php echo esc_attr($bcard->getBookingNumber()); ?>" />
            <input type="hidden" name="ntdi_dob" value="<?php echo esc_attr($bcard->getSenderDob()); ?>" />

            <input type="hidden" name="ntdi_message" value="<?php echo esc_attr(stripslashes($bcard->getMessageText())); ?>" />
            
            <input type="hidden" name="ntdi_sname" value="<?php echo esc_attr($bcard->getSenderName()); ?>" />
            <input type="hidden" name="ntdi_semail" value="<?php echo esc_attr($bcard->getSenderEmail()); ?>" />
            <input type="hidden" name="ntdi_rname" value="<?php echo esc_attr($bcard->getRecipientName()); ?>" />
            <input type="hidden" name="ntdi_remail" value="<?php echo esc_attr($bcard->getRecipientEmail()); ?>" />
            <?php
            $moreEmails = $bcard->getExtraEmails();
            if (!empty($moreEmails)) :
                foreach ($moreEmails as $email) :
                    ?>
                    <input type="hidden" name="ntdi_extra_email[]" value="<?php echo esc_attr($email); ?>" />
                    <?php
                endforeach;
            endif;
            ?>
            <input type="hidden" name="card_hash" value="<?php echo $bcard->generateUniqueHash(NULL, NONCE_SALT, 10); ?>" />
            <div id="submit">
                <input type="submit" name="ntdi_send" value="Send Card" />
                <input type="submit" name="ntdi_create" value="Edit Card" />
            </div>
        </form>
    </div>
</div>
<div style="clear:both;"></div>