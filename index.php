<?php
/**
* Plugin Name: Chat GPT Content Writer
* Plugin URI: https://github.com/huseyinstif/Chat-GPT-Wordpress-Plugin
* Description: Chat GPT Content Writer
* Version: 1.0
* Author: Hüseyin Tıntaş
* Author URI: https://github.com/huseyinstif/Chat-GPT-Wordpress-Plugin
* Please do not use without citing the source. It is an open source project and cannot be sold for a fee.
* Lütfen kaynak ve isim belirtmeden kullanmayınız. Open source projedir, ücretli satılamaz.
**/

//CREATE MENU
function chatgpt_content_writer_plugin() {
    add_menu_page(
        __( 'Chat GPT Panel', 'my-textdomain' ),
        __( 'Chat GPT Panel', 'my-textdomain' ),
        'manage_options',
        'chatgpt-content-writer-dashboard',
        'chatgpt_content_writer_dashboard',
        'dashicons-admin-users',
        999
    );
    add_submenu_page(
        'chatgpt-content-writer-dashboard', //parent menu name
        'Settings',
        'Settings',
        'manage_options', //
        'chatgpt-content-writer-settings', //url
        'chatgpt_content_writer_settings' //function
    );

}

//CALL CREATE MENU FUNCTION
add_action( 'admin_menu', 'chatgpt_content_writer_plugin' );


//FUNCTIONS
function chatgpt_content_writer_dashboard() {
    include "dashboard.php";
}
function chatgpt_content_writer_settings() {
    include "settings.php";
}


//CREATE TABLES
function create_table_chatgpt_content_writer() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	
    $table_name = $wpdb->prefix . 'chatgpt_content_writer';

    $sql = "CREATE TABLE " . $table_name . " (
	id int(11) NOT NULL AUTO_INCREMENT,
	api_token tinytext NOT NULL,
	temperature tinytext NOT NULL,
	max_tokens tinytext NOT NULL,
	language tinytext NOT NULL,
	PRIMARY KEY  (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


register_activation_hook(__FILE__, 'create_table_chatgpt_content_writer');