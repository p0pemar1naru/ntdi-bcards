<!--<p><?php //echo '<pre>LoadCard: '; print_r($bcard); echo '</pre>'; ?></p>-->

<div id="nt_wpecard_viewcard">
    <div id="bcards-preview-container">
        <?php if($isCard) : ?>
            <div id="nt_wpecard_vimage">
                <img src="<?php echo esc_url(wp_get_attachment_url($bcard->getImageID())); ?>" />
            </div>
            <div id="nt_wpecard_vmessage">
                <p>To: Recipients</p>
                <p>From: <?php echo esc_html($bcard->getSenderName()); ?></p>
                <br />
                <p><?php echo nl2br(esc_textarea(stripslashes($bcard->getMessageText()))); ?></p>
            </div>
        <?php else : ?>
            <p>No card to display.</p>
        <?php endif; ?>
    </div>
</div>
<div style="clear:both;"></div>