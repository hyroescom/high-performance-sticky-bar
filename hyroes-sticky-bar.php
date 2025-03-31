<?php
/*
Plugin Name: Hyroes Sticky Bar
Description: Adds a customizable sticky bar that can be closed, storing user preference in cookies.
Version: 1.0
Author: Alex Godlewski, Hyroes.com
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register settings + store defaults once
function hyroes_sticky_bar_register_settings() {
    register_setting('hyroes_sticky_bar_options', 'hyroes_sticky_bar_settings');
    // Ensure default options
    $existing = get_option('hyroes_sticky_bar_settings');
    if (!$existing) {
        add_option('hyroes_sticky_bar_settings', array(
            'bar_text'   => 'Welcome to our site!',
            'bar_bgcolor'=> '#333333',
            'enable_bar' => 0,
            'cookie_hours' => 24
        ));
    }
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

// Enqueue admin scripts
function hyroes_sticky_bar_admin_scripts($hook) {
    if ($hook === 'tools_page_hyroes-sticky-bar') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'hyroes_sticky_bar_admin_scripts');

function hyroes_sticky_bar_options_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Default settings
    $defaults = array(
        'bar_text' => 'Welcome to our site!',
        'bar_bgcolor' => '#333333',
        'enable_bar' => 0,
        'cookie_hours' => 24
    );
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    // Save settings if form is submitted
    if (isset($_POST['submit'])) {
        $settings['bar_text']   = sanitize_text_field($_POST['bar_text']);
        $settings['bar_bgcolor']= sanitize_hex_color($_POST['bar_bgcolor']);
        $settings['enable_bar'] = isset($_POST['enable_bar']) ? 1 : 0;
        $settings['cookie_hours'] = intval($_POST['cookie_hours']);
        update_option('hyroes_sticky_bar_settings', $settings);
        echo '<div class="updated"><p>Sticky Bar settings saved.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Hyroes Sticky Bar</h1>
        <form method="post" action="">
            <?php settings_fields('hyroes_sticky_bar_options'); ?>
            <script>
                jQuery(document).ready(function($) {
                    $('.color-picker').wpColorPicker({
                        defaultColor: '<?php echo esc_attr($settings['bar_bgcolor']); ?>'
                    });
                });
            </script>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="bar_text">Sticky Bar Text</label></th>
                    <td><input type="text" name="bar_text" value="<?php echo esc_attr($settings['bar_text']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bar_bgcolor">Background Color</label></th>
                    <td><input type="text" name="bar_bgcolor" value="<?php echo esc_attr($settings['bar_bgcolor']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cookie_hours">Hide Duration (Hours)</label></th>
                    <td><input type="number" min="1" max="8760" name="cookie_hours" value="<?php echo esc_attr($settings['cookie_hours']); ?>" class="small-text" />
                    <span class="description">Number of hours the bar stays hidden after a visitor closes it</span></td>
                </tr>
                <tr>
                    <th scope="row">Enable Sticky Bar</th>
                    <td>
                        <input type="checkbox" name="enable_bar" value="1" <?php checked($settings['enable_bar'], 1); ?> />
                        <span class="description">Check to activate sticky bar on the site</span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue front-end assets only if bar is enabled
function hyroes_sticky_bar_enqueue_scripts() {
    $defaults = array(
        'bar_text' => 'Welcome to our site!',
        'bar_bgcolor' => '#333333',
        'enable_bar' => 0,
        'cookie_hours' => 24
    );
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    // Only load script if bar is enabled
    if (!empty($settings['enable_bar'])) {
        wp_enqueue_script(
            'hyroes-sticky-bar-js',
            plugin_dir_url(__FILE__) . 'sticky-bar.js',
            array('jquery'),
            '1.0',
            true
        );
        wp_localize_script('hyroes-sticky-bar-js', 'HyroesStickyBarData', array(
            'barText' => $settings['bar_text'],
            'bgColor' => $settings['bar_bgcolor'],
            'cookieHours' => intval($settings['cookie_hours'])
        ));
    }
}
add_action('wp_enqueue_scripts', 'hyroes_sticky_bar_enqueue_scripts');

// Use JavaScript to insert the sticky bar as the first element in the header
function hyroes_sticky_bar_insertion_script() {
    $settings = get_option('hyroes_sticky_bar_settings');
    if (!empty($settings['enable_bar'])) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Create the sticky bar element
                var $stickyBar = $('<div id="hyroes-sticky-bar" style="display:none;"></div>');
                
                // Insert it as the first element inside the header
                $('#header').prepend($stickyBar);
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'hyroes_sticky_bar_insertion_script', 5);