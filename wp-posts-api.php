<?php
/*
Plugin Name: WP Post API
Plugin URI:  http://www.greatwhiteark.com
Description: Simple JSON export of post excerpts.
Version:     1.0.0
Author:      GWA
Author URI:  http://www.greatwhiteark.com
License:     MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: gwa
Domain Path: /languages
*/

defined('ABSPATH') or die('No script kiddies please!');

add_filter('query_vars', 'gwasw_query_vars');
function gwasw_query_vars($query_vars) {
    $query_vars[] = 'gwasw_api';
    $query_vars[] = 'idpost';
    $query_vars[] = 'idsince';
    return $query_vars;
}

add_action('parse_request', 'gwasw_parse_request');
function gwasw_parse_request(&$wp) {
    if (array_key_exists('gwasw_api', $wp->query_vars)) {
        include 'api.php';
        exit;
    }
    return;
}
