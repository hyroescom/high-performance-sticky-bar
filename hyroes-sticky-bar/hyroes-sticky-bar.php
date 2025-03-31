<?php
/*
Plugin Name: Hyroes Sticky Bar
Description: Adds a customizable sticky bar that can be closed, storing user preference in cookies.
Version: 1.0
Author: Alex Godlewski, Hyroes.com
*/

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Register settings
function hyroes_sticky_bar_register_settings() {
    register_setting('hyroes_sticky_bar_options', 'hyroes_sticky_bar_settings');
}
add_action('admin_init', 'hyroes_sticky_bar_register_settings');

// Add admin menu under Tools
function hyroes_sticky_bar_add_admin_menu() {
    add_submenu_page(
        'tools.php',
        'Sticky Bar',
        'Sticky Bar',
        'manage_options',
        'hyroes-sticky-bar',
        'hyroes_sticky_bar_options_page'
    );
}
add_action('admin_menu', 'hyroes_sticky_bar_add_admin_menu');

function hyroes_sticky_bar_options_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Default settings
    $defaults = array(
        'bar_text' => 'Welcome to our site!',
        'bar_bgcolor' => '#333333'
    );
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    // Save settings if form is submitted
    if (isset($_POST['submit'])) {
        $settings['bar_text'] = sanitize_text_field($_POST['bar_text']);
        $settings['bar_bgcolor'] = sanitize_hex_color($_POST['bar_bgcolor']);
        update_option('hyroes_sticky_bar_settings', $settings);
        echo '<div class="updated"><p>Sticky Bar settings saved.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Hyroes Sticky Bar</h1>
        <form method="post" action="">
            <?php settings_fields('hyroes_sticky_bar_options'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="bar_text">Sticky Bar Text</label></th>
                    <td><input type="text" name="bar_text" value="<?php echo esc_attr($settings['bar_text']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bar_bgcolor">Background Color</label></th>
                    <td><input type="text" name="bar_bgcolor" value="<?php echo esc_attr($settings['bar_bgcolor']); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue front-end assets
function hyroes_sticky_bar_enqueue_scripts() {
    $defaults = array(
        'bar_text' => 'Welcome to our site!',
        'bar_bgcolor' => '#333333'
    );
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    wp_enqueue_script('hyroes-sticky-bar-js', plugin_dir_url(__FILE__) . 'sticky-bar.js', array('jquery'), '1.0', true);
    wp_localize_script('hyroes-sticky-bar-js', 'HyroesStickyBarData', array(
        'barText' => $settings['bar_text'],
        'bgColor' => $settings['bar_bgcolor']
    ));
}
add_action('wp_enqueue_scripts', 'hyroes_sticky_bar_enqueue_scripts');

// Output the sticky bar container
function hyroes_sticky_bar_display() {
    echo '<div id="hyroes-sticky-bar" style="display:none;"></div>';
}
add_action('wp_footer', 'hyroes_sticky_bar_display');