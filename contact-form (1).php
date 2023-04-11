<?php
/*
Plugin Name: Contact Form
Description: A simple contact form plugin for WordPress.
Version: 1.0
Author: by yasmine
License: GPL2
*/

// Register the plugin activation hook
register_activation_hook(__FILE__, 'contact_form_create_table');

// Create the wp_contact_form table
function contact_form_create_table()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'contact_form';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    subject varchar(100) NOT NULL,
    first_name varchar(50) NOT NULL,
    last_name varchar(50) NOT NULL,
    email varchar(100) NOT NULL,
    message longtext NOT NULL,
    submission_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

// Register the plugin deactivation hook
register_deactivation_hook(__FILE__, 'contact_form_delete_table');

// Delete the wp_contact_form table
function contact_form_delete_table()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'contact_form';

  $sql = "DROP TABLE IF EXISTS $table_name;";

  $wpdb->query($sql);
}
// Register the shortcode
add_shortcode('contact_form', 'contact_form_shortcode');

function contact_form_process()
{
  $contact_form_subject = $_POST['contact_form_subject'];
  $contact_form_first_name = $_POST['contact_form_first_name'];
  $contact_form_last_name = $_POST['contact_form_last_name'];
  $contact_form_email = $_POST['contact_form_email'];
  $contact_form_message = $_POST['contact_form_message'];
  $date = 'NOW()';
  global $wpdb;
  $table_name = $wpdb->prefix . 'contact_form';
  $sql = "INSERT INTO $table_name (`subject`, `first_name`, `last_name`, `email`, `message`, `submission_date`) VALUES ('$contact_form_subject','$contact_form_first_name','$contact_form_last_name','$contact_form_email','$contact_form_message', $date)";
  $wpdb->query($sql);
}

// Define the shortcode function
function contact_form_shortcode()
{
  // Check if the form was submitted
  if (isset($_POST['contact_form_submit'])) {
    // Process the form submission
    contact_form_process();
  }

  // Define the form HTML
  $form = '
    <form method="post" action="">
      <label for="contact_form_subject">Subject</label>
      <input type="text" id="contact_form_subject" name="contact_form_subject" required>

      <label for="contact_form_first_name">First Name</label>
      <input type="text" id="contact_form_first_name" name="contact_form_first_name" required>

      <label for="contact_form_last_name">Last Name</label>
      <input type="text" id="contact_form_last_name" name="contact_form_last_name" required>

      <label for="contact_form_email">Email</label>
      <input type="email" id="contact_form_email" name="contact_form_email" required>

      <label for="contact_form_message">Message</label>
      <textarea id="contact_form_message" name="contact_form_message" required></textarea>

      <input type="submit" name="contact_form_submit" value="Send">
    </form>
  ';

  // Return the form HTML
  return $form;
}
// Add the plugin as a menu item
add_action('admin_menu', 'cf_add_menu_item');

function cf_add_menu_item()
{
  add_menu_page(
    'Contact Form Responses',
    'Contact Form',
    'manage_options',
    'cf-responses',
    'cf_display_responses',
    'dashicons-email',
    20
  );
}


// Display the responses submitted by users
function cf_display_responses()
{
  // check user capabilities
  if (!current_user_can('manage_options')) {
    return;
  }
  // include the WP_List_Table class
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

  // Define the WP_List_Table subclass
  class CF_Responses_List_Table extends WP_List_Table
  {
    function prepare_items()
    {
      // define column headers
      $columns = array(
        'id' => 'ID',
        'subject' => 'Subject',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'message' => 'Message',
        'submission_date' => 'Submission Date'
      );
      $this->_column_headers = array($columns, array(), array());

      // get the data from the wp_contact_form table
      global $wpdb;
      $table_name = $wpdb->prefix . 'contact_form';
      $data = $wpdb->get_results("SELECT * FROM $table_name");

      // set the data for the table
      $this->items = $data;
    }

    function column_default($item, $column_name)
    {
      return $item->$column_name;
    }

    function get_columns()
    {
      return array(
        'id' => 'ID',
        'subject' => 'Subject',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'message' => 'Message',
        'submission_date' => 'Submission Date'
      );
    }
  }

  // create an instance of the CF_Responses_List_Table class
  $responses_list_table = new CF_Responses_List_Table();

  // prepare the data for the table
  $responses_list_table->prepare_items();

  // display the table
  echo '<div class="wrap">';
  echo '<h2>Contact Form Responses</h2>';
  $responses_list_table->display();
  echo '</div>';
}


?>