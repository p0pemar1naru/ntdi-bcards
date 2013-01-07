<?php

/**
 * Description of BirthdayCard
 *
 * @author Nick
 */
class BirthdayCard {

    private $_tableBcards;
    private $_noncePreviewAction;
    protected $postPermalink = '';
    protected $attachmentImages;
    protected $cardID = null;
    protected $bookingNumber = null;
    protected $senderName = null;
    protected $senderEmail = null;
    protected $senderDob = null;
    protected $recipientName = null;
    protected $recipientEmail = null;
    protected $imageID = null;
    protected $messageText = null;
    protected $postID = null;
    protected $cardHash = null;
    protected $extraEmails = array();

    public function __construct($data = array())
    {
        global $wpdb;
        $this->_tableBcards = $wpdb->prefix . 'ntdi_bcards';
        $this->setPostID();
        $this->_noncePreviewAction = 'preview-card-on_' . $this->postID; // unique to this nonce
        $this->setPostPermalink();
        $this->setAttachmentImages();

        $sanitizedData = $this->validateFormValues($data);
//echo "<pre> sanitizedData: "; print_r($sanitizedData); echo "</pre>"; exit();
        if (!empty($sanitizedData))
            $this->storeFormValues($sanitizedData);
//            else
//                die('Hmmmm... Show validation errors !!!');
    }

    /**
     * Validates submitted form data
     * 
     * @param assoc $data Form data in POST
     * @return asoc|boolean TRUE if no validation errors else asoc array of filtered data
     */
    private function validateFormValues($data)
    {
        // 1. Validate input - front-end only
        // 2. Filter the input
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'ntdi_image':
                    case 'post_id':
                    case 'ntdi_booking':
                    case 'card_ID':
//                        $value = (int) $value;
                        $value = absint($value);
                        break;
                    case 'ntdi_dob':
                    case 'ntdi_sname':
                    case 'ntdi_rname':
                    case 'card_hash':
                        $value = sanitize_text_field($value);
                        break;
                    case 'ntdi_message':
                        $value = wp_filter_nohtml_kses($value);
                        break;
                    case 'ntdi_semail':
                    case 'ntdi_remail':
                        $value = sanitize_email($value);
                        break;
                    case 'ntdi_extra_email':
                        // $value is an indexed array of extra emails
                        foreach ($value as $extraEmail) {
                            $extraEmail = isset($extraEmail) ? sanitize_email($extraEmail) : '';
                        }
                        break;
                    default:
                        $value = wp_filter_nohtml_kses($value);
                        break;
                }
            }
            return $data;
        } else {
            return false;
        }
    }

    /**
     * ######### STATIC FUNCTION ????? ######### OR return new Object singleton like
     * 
     * Sets the object's properties using the create form post values in the supplied array
     *
     * @param assoc The form post values
     */
    private function storeFormValues($sanitizedData)
    {
        /**
         * @todo do I need the card id, post id, card hash?
         * @todo empty instead of isset ?
         */
        if (isset($sanitizedData['ntdi_image']))
            $this->imageID = $sanitizedData['ntdi_image'];
        if (isset($sanitizedData['ntdi_booking']))
            $this->bookingNumber = $sanitizedData['ntdi_booking'];
        if (isset($sanitizedData['ntdi_dob']))
            $this->senderDob = $sanitizedData['ntdi_dob'];
        if (isset($sanitizedData['ntdi_sname']))
            $this->senderName = $sanitizedData['ntdi_sname'];
        if (isset($sanitizedData['ntdi_semail']))
            $this->senderEmail = $sanitizedData['ntdi_semail'];
        if (isset($sanitizedData['ntdi_rname']))
            $this->recipientName = $sanitizedData['ntdi_rname'];
        if (isset($sanitizedData['ntdi_remail']))
            $this->recipientEmail = $sanitizedData['ntdi_remail'];
        if (isset($sanitizedData['ntdi_message']))
            $this->messageText = $sanitizedData['ntdi_message'];
        if (isset($sanitizedData['post_id']))
            $this->postID = $sanitizedData['post_id'];
        if (isset($sanitizedData['card_ID']))
            $this->cardID = $sanitizedData['card_ID'];
        if (isset($sanitizedData['card_hash']))
            $this->cardHash = $sanitizedData['card_hash'];
        if (isset($sanitizedData['ntdi_extra_email']) && !empty($sanitizedData['ntdi_extra_email']))
            $this->extraEmails = (array) $sanitizedData['ntdi_extra_email'];
//echo "<pre> sanitized Extra Emails: "; print_r($this->extraEmails); echo "</pre>"; exit();
    }

    /**
     * Loads the requested card based on the hash
     * 
     * @global obj $wpdb
     * @param string $cardHash the card hash
     * @return boolean True if the card is retrieved, false otherwise
     */
    public function getByHash($cardHash)
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$this->_tableBcards} WHERE card_hash = %s;", array($cardHash));
        if ($card = $wpdb->get_row($sql)) {
            $this->cardID = (int) $card->card_ID;
            $this->senderName = $card->sender_name;
            $this->senderEmail = $card->sender_email;
            $this->recipientName = $card->recipient_name;
            $this->recipientEmail = $card->recipient_email;
            $this->imageID = (int) $card->image_id;
            $this->messageText = $card->message_text;
            $this->postID = (int) $card->post_id;
            $this->cardHash = $card->card_hash;

            return true;
        } else
            return false;
    }

    public function insert()
    {
        global $wpdb;
        $data = array(
            'booking_number' => $this->bookingNumber,
            'sender_name' => $this->senderName,
            'sender_email' => $this->senderEmail,
            'sender_dob' => $this->senderDob,
            'recipient_name' => $this->recipientName,
            'recipient_email' => $this->recipientEmail,
            'image_id' => $this->imageID,
            'message_text' => $this->messageText,
            'post_id' => $this->postID,
            'card_hash' => $this->cardHash
        );
        $num_rows = $wpdb->insert($this->_tableBcards, (array) $data, array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s'));
        if ($num_rows)
            $wpdb->flush();

        return $num_rows;
    }

    /**
     * Emails a Card
     * 
     * @return bool Whether the email contents were sent successfully.
     */
    public function sendCard()
    {
        $headers = array(
            'FROM: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
            "BCC: {$this->recipientEmail}"
        );
        if (!empty($this->extraEmails)) {
            foreach ($this->extraEmails as $email) {
                if($email)
                    $headers[] = "BCC: {$email}";
            }
        }
//        $to = $this->recipientEmail;
        $to = $this->senderEmail;
//        $message = "Hi " . $this->recipientName . ",\r\n\r\n";
        $message = "Hello,\r\n\r\n";
        $message .= "You have been sent a birthday invitation card from " . $this->senderName . ".\r\n";
        $message .= "To view it, please visit " . $this->postPermalink . "?action=load&cardHash=" . $this->cardHash . " \r\n";

        $subject = 'Birthday invitation card from ' . $this->senderName;
//echo "<pre> HEADERS: "; print_r($headers); echo "<br>SUBJECT: $subject<br>TO: {$to}<br>MESSAGE:{$message}</pre>";exit();
        $response = wp_mail($to, $subject, $message, $headers);

        return $response;
    }

    /**
     * Generate a unique ID using sha512 encryption. 
     * 
     * @see http://php.net/manual/en/function.hash.php
     * @param int|string $id If $id is NULL, the generated string will be random. If not, the same hash will always be presented, provided the $salt is the same.
     * @param string $salt A salt. In this case the WP NONCE_SALT will do the job.
     * @param int $length The length of the generated string. If NULL, the default 128 characters is used.
     * @return string The hash with the corresponding length. 
     */
    function generateUniqueHash($id = NULL, $salt = NULL, $length = NULL)
    {
        $id = ($id == NULL) ? uniqid(hash("sha512", rand()), TRUE) : $id;
        $code = hash("sha512", $id . $salt);
        return $length == NULL ? $code : substr($code, 0, $length);
    }

    public function getAttachmentImages()
    {
        return $this->attachmentImages;
    }

    /**
     * Retrieves post image attachements.<br />
     * Sets $attachmentImages property as follows:
     * <ul>
     *  <li>array of image attachment Objects</li>
     *  <li>OR false - if no image attachments</li>
     * </ul>
     */
    protected function setAttachmentImages()
    {
        $args = array(
            'post_parent' => (int) $this->postID,
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'orderby' => 'menu_order',
            'order' => 'ASC');
        $attachments = get_children($args);
        if (empty($attachments)) // false OR empty array - is empty()
            $this->attachmentImages = false;
        else
            $this->attachmentImages = $attachments;
    }

    protected function setPostID()
    {
        global $post;
        $this->postID = (int) $post->ID;
    }

    public function getPostID()
    {
        return $this->postID;
    }

    protected function setPostPermalink()
    {
        $this->postPermalink = get_permalink((int) $this->postID);
    }

    public function getPostPermalink()
    {
        return $this->postPermalink;
    }

    /**
     * Prints 'checked' if current image is in POST
     * 
     * @param int $id Attchment ID attribute 
     * @return string checked attribute OR empty string
     */
    public function isSelected($attachmentID)
    {
        if (isset($_POST['ntdi_image']) && $attachmentID == $_POST['ntdi_image'])
            return 'checked="checked"';
        else
            return '';
    }

    public function getImageID()
    {
        return $this->imageID;
    }

    public function getSenderName()
    {
        return $this->senderName;
    }

    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    public function getRecipientName()
    {
        return $this->recipientName;
    }

    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    public function getMessageText()
    {
        return $this->messageText;
    }

    public function getNoncePreviewAction()
    {
        return $this->_noncePreviewAction;
    }

    public function getBookingNumber()
    {
        return $this->bookingNumber;
    }

    public function getSenderDob()
    {
        return $this->senderDob;
    }

    public function getExtraEmails()
    {
        return $this->extraEmails;
    }

}

