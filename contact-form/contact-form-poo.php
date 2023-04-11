<?php
/*
 * Plugin Name:       Contact form.
 * Description:       Create and customize wordpress contact forms.
 * Version:           1.0.0.
 * Author:            Ilias anouar.
 */
if (!defined('ABSPATH')) {
    echo 'what are you trying to do';
    exit;
}
// Create the table in the database when the plugin is activated
// register_activation_hook(__FILE__, 'contact_form_create_table');

// function contact_form_create_table()
// {
//     global $wpdb;

//     $table_name = $wpdb->prefix . 'contact_form';
//     $charset_collate = $wpdb->get_charset_collate();

//     $sql = "CREATE TABLE $table_name (
//       id mediumint(9) NOT NULL AUTO_INCREMENT,
//       sujet varchar(255) NOT NULL,
//       nom varchar(255) NOT NULL,
//       prenom varchar(255) NOT NULL,
//       email varchar(255) NOT NULL,
//       message text NOT NULL,
//       date_envoi datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
//       UNIQUE KEY id (id)
//   ) $charset_collate;";

//     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//     dbDelta($sql);
// }
// Delete the table from the database when the plugin is deactivated
register_deactivation_hook(__FILE__, 'contact_form_delete_table');

function contact_form_delete_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'contact_form';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

class contact_form
{
    public function __construct()
    {
        register_activation_hook(__FILE__, 'contact_form_create_table');
        add_action('admin_menu', array($this, 'contact_form_add_menu'));


    }
    public function contact_form_create_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'contact_form';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      sujet varchar(255) NOT NULL,
      nom varchar(255) NOT NULL,
      prenom varchar(255) NOT NULL,
      email varchar(255) NOT NULL,
      message text NOT NULL,
      date_envoi datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      UNIQUE KEY id (id)
  ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function contact_form_add_menu()
    {
        add_menu_page(
            'WPOrg',
            'Contact Form',
            'manage_options',
            plugin_dir_path(__FILE__) . 'admin/view.php',
            null,
            'dashicons-admin-tools',
            20
        );
    }
}

new contact_form;


?>