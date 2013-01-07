<?php

/**
 * Submits an HTTP POST to a reCAPTCHA server
 * 
 * @param string $host 
 * @param string $path 
 * @param array $data 
 * @param int port 
 * @return array response 
 */
function recaptchaHttpPost($host, $path, $data, $port = 80)
{
    $add_headers = array( "Host: $host" );

    $curl = curl_init('http://' . $host . ':' . $port . $path);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_USERAGENT, 'reCAPTCHA/PHP');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $add_headers);
    if (isset($_ENV['http_proxy']) && !empty($_ENV['http_proxy'])) {
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($curl, CURLOPT_PROXY, $_ENV['http_proxy']);  // CURLOPT_PROXYUSERPWD as username:password needed?
    }
    $response = curl_exec($curl);
    
    if ($response === false)
        die('Error connecting to ' . $host . '.');
    $response = explode("\r\n\r\n", $response, 2);

    return $response;
}

/**
 * url_get_contents function by Andy Langton: http://andylangton.co.uk/
 * 
 * @param type $url
 * @param type $useragent
 * @param type $headers
 * @param type $follow_redirects
 * @param type $debug
 * @return type 
 */
function url_get_contents($url, $useragent = 'cURL', $headers = false, $follow_redirects = false, $debug = false)
{

# initialise the CURL library
    $ch = curl_init();
# specify the URL to be retrieved
    curl_setopt($ch, CURLOPT_URL, $url);
# we want to get the contents of the URL and store it in a variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
# specify the useragent: this is a required courtesy to site owners
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
# ignore SSL errors
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
# return headers as requested
    if ($headers == true) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }
# only return headers
    if ($headers == 'headers only') {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
# follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects == true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }
# if debugging, return an array with CURL's debug info and the URL contents
    if ($debug == true) {
        $result['contents'] = curl_exec($ch);
        $result['info'] = curl_getinfo($ch);
    }
# otherwise just return the contents as a variable
    else
        $result = curl_exec($ch);
# free resources
    curl_close($ch);
# send back the data
    return $result;
}

/**
 * Adds JS to the header on a [bcards] shortcode enabled page/post
 * 
 * @global obj $wp_query 
 */
function ntdi_bcards_scripts()
{
    global $wp_query;
    if (isset($wp_query->queried_object->post_content))
        $pos = strpos($wp_query->queried_object->post_content, "[bcards]");
    if ((isset($pos)) && !(false === $pos)) {
        $js = '/js/ecards.js';
        if (file_exists(NTDI_BCARD_PATH . $js))
            wp_enqueue_script('wp_ecards_js', NTDI_BCARD_URL . $js, array('jquery'));

        wp_enqueue_script('thickbox');
//        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');
    }
}

/**
 * Adds style styles to the header on a [bcards] shortcode enabled page/post
 * 
 * @global obj $wp_query object 
 */
function ntdi_bcards_styles()
{
    global $wp_query;
    if (isset($wp_query->queried_object->post_content))
        $pos = strpos($wp_query->queried_object->post_content, "[bcards]");
    if ((isset($pos)) && !(false === $pos)) {
        $css = '/css/ecards.css';
        if (file_exists(NTDI_BCARD_PATH . $css)) {
            wp_register_style('wp_ecards', NTDI_BCARD_URL . $css);
            wp_enqueue_style('wp_ecards', NTDI_BCARD_URL . $css);
        }
    }

    wp_enqueue_style('thickbox');
    wp_enqueue_style('jquery.ui.theme', NTDI_BCARD_URL . '/css/blitzer/jquery-ui-1.9.2.custom.min.css'); // datepicker css
}

/**
 * Adds new query vars to the routes
 * 
 * @param array $qvars
 * @return array 
 */
function ntdi_register_query_vars($qvars)
{
    $qvars[] = 'action';
    $qvars[] = 'cardHash';

    return $qvars;
}

/**
 * Force routing to get the new query vars 
 */
function ntdi_rewrite_flush()
{
    ntdi_create_custom_post_type_bcard();
    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}

/**
 * Register custom post type bcard
 */
function ntdi_create_custom_post_type_bcard()
{
    $labels = array(
        'name' => 'Manage BCards',
        'singular_name' => 'BCard',
        'menu_name' => 'Manage BCards',
        'add_new' => 'New BCard',
        'add_new_item' => 'Add New BCard',
        'edit' => 'Edit',
        'edit_item' => 'Edit BCard',
        'new_item' => 'New BCard',
        'view' => 'View BCard',
        'view_item' => 'View BCard',
        'search_items' => 'Search BCards',
        'not_found' => 'No BCards found',
        'not_found_in_trash' => 'No BCard found in Trash',
        'parent' => 'Parent BCard');

    $args = array(
        'label' => 'BCards',
        'description' => 'BCards',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'page',
        'hierarchical' => true,
        'rewrite' => array('slug' => 'bcard'),
        'query_var' => true,
        'has_archive' => false,
        'menu_position' => 0,
        'supports' => array('title', 'editor', 'page-attributes', 'thumbnail'),
        'labels' => $labels
    );

    register_post_type('bcard', $args);
}

/**
 * Adds tables to db on plugin activation 
 */
function ntdi_bcards_plugin_activation()
{
    include_once NTDI_BCARD_PATH . '/tables.php';
    $ntdi_bcards_tables = new NTDI_Bcards_Tables();
}