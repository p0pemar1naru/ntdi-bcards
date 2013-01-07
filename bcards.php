<?php

/*
  Plugin Name: NTDI Birthday Cards
  Plugin URI: http://www.cjdigital.ca
  Description: This plugin allows visitors of a WP Page or Post to send eCards using a shortcode.
  Version: 1.3.8
  Author: Nick Tetcu
  Author URI: http://www.tetcu.com
 */

define('NTDI_BCARD_PATH', WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)));
define('NTDI_BCARD_URL', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)));
define('NTDI_BCARD_TPL_PATH', NTDI_BCARD_PATH . '/views');
define('NTDI_BCARD_MODEL_PATH', NTDI_BCARD_PATH . '/models');
define('NTDI_BCARD_LIB_PATH', NTDI_BCARD_PATH . '/lib');
define('NTDI_NONCE_NAME', plugin_basename(dirname(__FILE__)) . '-do'); // plugin name - action
define('NTDI_RECAPTCHA_PUBLIC_KEY', '');
define('NTDI_RECAPTCHA_PRIVATE_KEY', '');

require NTDI_BCARD_LIB_PATH . '/utils.php';
require NTDI_BCARD_MODEL_PATH . '/BirthdayCard.php';

register_activation_hook(__FILE__, 'ntdi_bcards_plugin_activation'); # Adds tables to db on plugin activation 
//add_action('init', 'ntdi_create_custom_post_type_bcard');
//register_activation_hook(__FILE__, 'ntdi_rewrite_flush');

add_filter('query_vars', 'ntdi_register_query_vars', 10, 1);

add_action('wp_print_scripts', 'ntdi_bcards_styles');

add_action('wp_print_scripts', 'ntdi_bcards_scripts');

add_shortcode('bcards', 'skate_do_shortcode_bcard');

/**
 * Simple Front Controller
 * 
 * @global obj $wp_query object
 */
function skate_do_shortcode_bcard()
{
    if (is_page()) {
//    if (is_singular('bcard')) {
        global $wp_query;

        $action = isset($wp_query->query_vars['action']) ? urldecode($wp_query->query_vars['action']) : '';
        switch ($action) {
//            case 'preview':
//                // show bcard preview
//                previewBcard();
//                break;
            case 'load':
                // loads a bcard
                $cardHash = isset($wp_query->query_vars['cardHash']) ? urldecode($wp_query->query_vars['cardHash']) : '';
                loadCard($cardHash);
                break;
            default:
                // show create bcard form
                createBcard();
                break;
        }
    }
}

function createBcard()
{
    require_once NTDI_BCARD_LIB_PATH . '/recaptchalib.php';

    # Handle reCAPTCHA
    $resp = null; # the response from reCAPTCHA
    $error = null; # the error code from reCAPTCHA, if any
    if (isset($_POST['recaptcha_response_field'])) {
        $resp = recaptcha_check_answer(NTDI_RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
        if ($resp->is_valid === true) {
            // redirect to preview action
            previewBcard();
            return;
        } else {
            # set the error code so that we can display it
            $error = $resp->error;
        }
    }

    if (isset($_POST['ntdi_create'])) {
        // card is being edited
        $bcard = new BirthdayCard($_POST);
        if (wp_verify_nonce($_POST[NTDI_NONCE_NAME], $bcard->getNoncePreviewAction())) {
            $formAction = $bcard->getPostPermalink();
//            $formAction = $bcard->getPostPermalink() . '?action=preview';

            include NTDI_BCARD_TPL_PATH . '/create_bcard.php';
        } else
            die('Your code smells.');
    } elseif (isset($_POST['ntdi_send'])) {
        // card is sent
        $bcard = new BirthdayCard($_POST);
        if (wp_verify_nonce($_POST[NTDI_NONCE_NAME], $bcard->getNoncePreviewAction())) {
            $rows = $bcard->insert();
            if ($rows) {
                $response = $bcard->sendCard();
                if (!$response)
                    die('Sorry... cannnot email the card.');
            } else
                die('Sorry... cannnot send the card.');

            include NTDI_BCARD_TPL_PATH . '/sent_bcard.php';
        } else
            die('Your code smells.');
    } else {
        // show create new card form
        $bcard = new BirthdayCard();
        $formAction = $bcard->getPostPermalink();
//        $formAction = $bcard->getPostPermalink() . '?action=preview';

        include NTDI_BCARD_TPL_PATH . '/create_bcard.php';
    }
}

function previewBcard()
{
    if (isset($_POST['ntdi_create'])) {
        $bcard = new BirthdayCard($_POST);
        if (wp_verify_nonce($_POST[NTDI_NONCE_NAME], $bcard->getNoncePreviewAction())) {
            $formAction = $bcard->getPostPermalink();

            include NTDI_BCARD_TPL_PATH . '/preview_bcard.php';
        } else
            die('Your code smells.');
    }
}

function loadCard($cardHash)
{
    $bcard = new BirthdayCard();
    $isCard = $bcard->getByHash($cardHash);

    include NTDI_BCARD_TPL_PATH . '/load_bcard.php';
}
