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

// Settings -------

class GwaSwSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'WP Posts API Settings',
            'WP Posts API',
            'manage_options',
            'gwasw-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('gwasw_options');
        ?>
        <div class="wrap">
            <h1>WP Posts API Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields('gwasw_options_group');
                do_settings_sections('gwasw-settings-admin');
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'gwasw_options_group', // Option group
            'gwasw_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            array($this, 'print_section_info'), // Callback
            'gwasw-settings-admin' // Page
        );

        add_settings_field(
            'author', // ID
            'Author', // Title
            array($this, 'author_callback'), // Callback
            'gwasw-settings-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'author_image', // ID
            'Author image', // Title
            array($this, 'author_image_callback'), // Callback
            'gwasw-settings-admin', // Page
            'setting_section_id' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['author'])) {
            $new_input['author'] = sanitize_text_field($input['author']);
        }

        if (isset($input['author_image'])) {
            $new_input['author_image'] = sanitize_text_field($input['author_image']);
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    public function author_callback()
    {
        printf(
            '<input type="text" class="regular-text" id="author" name="gwasw_options[author]" value="%s" />',
            isset($this->options['author']) ? esc_attr($this->options['author']) : ''
        );
    }

    public function author_image_callback()
    {
        printf(
            '<input type="text" class="regular-text" id="author_image" name="gwasw_options[author_image]" value="%s" placeholder="https://" />',
            isset($this->options['author_image']) ? esc_attr($this->options['author_image']) : ''
        );
        echo '<div class="description">Copy in the URL of an image to be used as the author image. You can upload it to media and copy the URL there.</div>';
    }
}

if (is_admin()) {
    $my_settings_page = new GwaSwSettingsPage();
}
