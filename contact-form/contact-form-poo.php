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
register_activation_hook(__FILE__, 'contact_form_create_table');

function contact_form_create_table()
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
// Delete the table from the database when the plugin is deactivated
register_deactivation_hook(__FILE__, 'contact_form_delete_table');

function contact_form_delete_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'contact_form';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

class Contact_form
{
    public function __construct()
    {
        // add menu icon
        add_action('admin_menu', array($this, 'contact_form_add_menu'));
        // load css, js ets
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));
        // add shortcode
        add_shortcode('contact-form', array($this, 'load_shortcode'));
        // add jquery
        add_action('wp_footer', array($this, 'load_script'));
        // register rest API
        add_action('rest_api_init', array($this, 'register_rest_api'));
    }

    public function contact_form_add_menu()
    {
        add_menu_page(
            'Contact-form',
            'Contact Form',
            'manage_options',
            plugin_dir_path(__FILE__) . 'admin/view.php',
            null,
            'dashicons-welcome-widgets-menus',
            20
        );
    }

    public function load_assets()
    {
        wp_enqueue_style(
            'contact-form',
            plugin_dir_url(__FILE__) . 'css/contact-form.css',
            array(),
            1,
            'all'
        );

        wp_enqueue_script(
            'contact-form',
            plugin_dir_url(__FILE__) . 'js/contact-form.js',
            array('jquery'),
            1,
            true
        );
    }

    public function load_shortcode()
    { ?>
        <div class="contact-form">
            <h3 class="mb-5">Send us a message</h3>
            <p class="danger mb-5">please fill the below form</p>
            <form id="contact-form__form">
                <input name="First_name" class="mb-5 w-100" type="text" placeholder="First name" required="">
                <input name="Last_name" class="mb-5 w-100" type="text" placeholder="Last name" required="">
                <input name="Subject" class="mb-5 w-100" type="text" placeholder="Subject" required="">
                <input name="Email" class="mb-5 w-100" type="email" placeholder="E-mail" required="">
                <textarea class="mb-5 w-100" name="Message" id="message" placeholder="Type your message" required=""></textarea>
                <button type="submit" class="btn btn-send w-100">Send message</button>
            </form>
        </div>
    <?php }
    public function load_script()
    { ?>
        <script>
            var nonce = '<?php echo wp_create_nonce('wp_rest'); ?>';
            (function ($) {
                $('#contact-form__form').submit(function (event) {
                    event.preventDefault();
                    var form = $(this).serialize();
                    $.ajax({
                        method: 'post',
                        url: '<?php echo get_rest_url(null, 'contact-form/v1/send-email'); ?>',
                        headers: { 'X-WP-Nonce': nonce },
                        data: form
                    })
                })
            })(jQuery)
        </script>
    <?php }
    public function register_rest_api()
    {
        register_rest_route(
            'contact-form/v1',
            'send-email',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'handle_contact_form')
            )
        );
    }

    public function handle_contact_form($data)
    {
        $headers = $data->get_headers();
        $params = $data->get_params();
        $nonce = $headers['x_wp_nonce'][0];
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_REST_Response('Message not sent', 422);
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_form';
        $contact_form_subject = $params['Subject'];
        $contact_form_first_name = $params['First_name'];
        $contact_form_last_name = $params['Last_name'];
        $contact_form_email = $params['Email'];
        $contact_form_message = $params['Message'];
        $date = 'NOW()';
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_form';
        $sql = "INSERT INTO $table_name (`sujet`, `nom`, `prenom`, `email`, `message`, `date_envoi`) VALUES ('$contact_form_subject','$contact_form_first_name','$contact_form_last_name','$contact_form_email','$contact_form_message', $date)";
        // $wpdb->query($sql);
        if ($wpdb->query($sql)) {
            return new WP_REST_Response('Thank you for your message', 200);
        }
    }

}

new Contact_form;


?>