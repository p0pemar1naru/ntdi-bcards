<!--<p><?php //echo '<pre>POST[] from preview action: '; print_r($_POST); echo '</pre>';            ?></p>-->
<!--<p><?php //echo '<pre>Bcard Object: '; print_r($bcard); echo '</pre>'; ?></p>-->

<form id="ntdi-create" action="<?php echo esc_url($formAction); ?>" method="post">
    <?php wp_nonce_field($bcard->getNoncePreviewAction(), NTDI_NONCE_NAME); ?>

    <p class="bcards-headlines attention">Please select a card:</p>
    <ul id="bcards-images-container">
        <?php
        $attachements = $bcard->getAttachmentImages();
        if (!empty($attachements)) :
            foreach ($attachements as $attachement) :
                ?>
                <li class="bcards-image-item">
                    <a href="<?php echo esc_url(wp_get_attachment_url($attachement->ID)); ?>" class='thickbox' rel="<?php echo esc_attr('ntdi_bcard_' . $bcard->getPostID()); ?>"><?php echo wp_get_attachment_image(esc_html($attachement->ID), 'thumbnail'); ?></a>
                    <span><input type="radio" name="ntdi_image" id="image-<?php echo esc_attr($attachement->ID); ?>" value="<?php echo esc_attr($attachement->ID); ?>" required <?php echo $bcard->isSelected($attachement->ID); ?> /></span>
                </li>
                <?php
            endforeach;
        else :
            ?>
            <li class="bcards-no-image">No images exists for this page.</li>
        <?php endif; ?>
    </ul>
    <div style="clear:left;"></div>
    <!--    <div id="bcards-info-container">-->
    <ul id="bcards-info">
        <li class="bcards-item short">
            <label for="ntdi-booking"><span class="ntdi_booking_label text">Booking #</span></label>
            <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_booking" value="<?php echo esc_attr($bcard->getBookingNumber()); ?>" type="text" required maxlength="6" /></span>
        </li>
        <li class="bcards-item short">
            <label for="ntdi-dob"><span class="ntdi_dob_label text">D.O.B.</span></label>
            <span class="bcards-item-wrap"><input id="ntdi_dob" class="bcards-item" name="ntdi_dob" value="<?php echo esc_attr($bcard->getSenderDob()); ?>" type="text" required maxlength="10" /></span>
        </li>
        <li class="bcards-item short">
            <label for="ntdi-sname"><span class="nt_wpecards_sname_label text">Sender Name</span></label>
            <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_sname" value="<?php echo esc_attr($bcard->getSenderName()); ?>" type="text" required maxlength="255" /></span>
        </li>
        <li class="bcards-item short">
            <label for="ntdi-semail"><span class="nt_wpecards_semail_label text">Sender Email</span></label>
            <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_semail" value="<?php echo esc_attr($bcard->getSenderEmail()); ?>" type="email" required maxlength="255" /></span>
        </li>				
        <li class="bcards-item short">
            <label for="ntdi-rname" ><span class="nt_wpecards_rname_label text">Recipient Name</span></label>
            <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_rname" value="<?php echo esc_attr($bcard->getRecipientName()); ?>" type="text" required maxlength="255" /></span>
        </li>
        <li class="bcards-item short">
            <label for="ntdi-remail"><span class="nt_wpecards_remail_label text">Recipient Email</span></label>
            <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_remail" value="<?php echo esc_attr($bcard->getRecipientEmail()); ?>" type="email" required maxlength="255" /></span>
        </li>

        <hr class="clr-lft" />
        <p class="bcards-headlines clr-lft attention">Add extra emails:</p>
        <?php
        $moreEmails = $bcard->getExtraEmails();
        if (!empty($moreEmails)) :
            foreach ($moreEmails as $email) :
                ?>
                <li class="bcards-item short">
                    <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                    <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="<?php echo $email ? esc_attr($email) : ''; ?>" type="email" maxlength="255" /></span>
                </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
            <li class="bcards-item short">
                <label for="ntdi-extra-email"><span class="ntdi-extra-email-label text">Add Email</span></label>
                <span class="bcards-item-wrap"><input class="bcards-item" name="ntdi_extra_email[]" value="" type="email" maxlength="255" /></span>
            </li>
        <?php endif; ?>
        <hr class="clr-lft" />

        <li id="message" class="bcards-item clr-lft">
            <span class="text">Your Message</span>
            <span class="bcards-item-wrap"><textarea id="bcards-message" name="ntdi_message" type="text" required maxlength="65535"><?php echo esc_textarea(stripslashes($bcard->getMessageText())); ?></textarea></span>
        </li>
        <!-- ReCAPTCHA -->
        <div style="clear:left;"></div><?php echo recaptcha_get_html(NTDI_RECAPTCHA_PUBLIC_KEY, $error); ?><div style="clear:both;"></div>

        <li id="submit" class="clr-lft">
            <input type="submit" id="nt_wpecards_submit" name="ntdi_create" value="Preview" />
        </li>
        <!--    </div>-->
    </ul>
</form>
<div style="clear:both;"></div>