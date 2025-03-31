<?php
/*
Plugin Name: Lightweight High Performance Sticky Bar
Description: Adds a customizable sticky notification bar to the top of your website that can be closed by visitors, with their preference stored in cookies.
Version: 1.4
Author: Hyroes.com
Author URI: https://hyroes.com
Text Domain: high-performance-sticky-bar-main
Domain Path: /languages
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Security measure to prevent direct access to the plugin file.
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}

/**
 * Sanitize plugin settings
 *
 * @param array $input The raw input from the settings form.
 * @return array Sanitized settings array.
 */
function hyroes_sticky_bar_sanitize_settings($input) {
    $sanitized_input = array();
    
    $sanitized_input['bar_text'] = isset($input['bar_text']) ? sanitize_text_field($input['bar_text']) : '';
    $sanitized_input['bar_bgcolor'] = isset($input['bar_bgcolor']) ? sanitize_hex_color($input['bar_bgcolor']) : '#333333';
    $sanitized_input['enable_bar'] = isset($input['enable_bar']) ? 1 : 0;
    $sanitized_input['cookie_hours'] = isset($input['cookie_hours']) ? 
        intval($input['cookie_hours']) : 24;
    
    return $sanitized_input;
}

// Register settings + store defaults once
function hyroes_sticky_bar_register_settings() {
    register_setting('hyroes_sticky_bar_options', 'hyroes_sticky_bar_settings', 'hyroes_sticky_bar_sanitize_settings');
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

/**
 * Adds the plugin settings page to the WordPress admin menu under Tools.
 *
 * Creates a submenu page that allows administrators to configure
 * the sticky bar options including text, color, and visibility settings.
 * 
 * @return void
 */
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

/**
 * Enqueues necessary scripts and styles for the admin settings page.
 *
 * Loads the WordPress color picker for the background color selection
 * only on the plugin's settings page to avoid unnecessary resource loading.
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function hyroes_sticky_bar_admin_scripts($hook) {
    if ($hook === 'tools_page_hyroes-sticky-bar') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'hyroes_sticky_bar_admin_scripts');

/**
 * Renders the plugin settings page HTML in the WordPress admin.
 *
 * Provides a form for administrators to configure:
 * - Custom text for the sticky bar
 * - Background color using WordPress color picker
 * - Cookie duration (how long the bar stays hidden after being closed)
 * - Toggle to enable/disable the sticky bar
 */
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

    // Verify nonce and save settings if form is submitted
    if (isset($_POST['submit']) && check_admin_referer('hyroes_sticky_bar_nonce')) {
        // Validate and sanitize bar text
        if (isset($_POST['bar_text'])) {
            $settings['bar_text'] = sanitize_text_field(wp_unslash($_POST['bar_text']));
        }

        // Validate and sanitize background color
        if (isset($_POST['bar_bgcolor'])) {
            $settings['bar_bgcolor'] = sanitize_hex_color(wp_unslash($_POST['bar_bgcolor']));
        }

        // Validate and sanitize cookie hours
        $settings['cookie_hours'] = isset($_POST['cookie_hours']) ? absint(wp_unslash($_POST['cookie_hours'])) : 24;

        $settings['enable_bar'] = isset($_POST['enable_bar']) ? 1 : 0;
        update_option('hyroes_sticky_bar_settings', array_map('wp_kses_post', $settings));
        echo '<div class="updated"><p>' . esc_html__('Sticky Bar settings saved.', 'high-performance-sticky-bar-main') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Lightweight High Performance Sticky Bar', 'high-performance-sticky-bar-main'); ?></h1>
        <form method="post" action="">
            <?php settings_fields('hyroes_sticky_bar_options'); ?>
            <?php wp_nonce_field('hyroes_sticky_bar_nonce'); ?>
            <script>
                jQuery(document).ready(function($) {
                    $('.color-picker').wpColorPicker({
                        defaultColor: '<?php echo esc_attr($settings['bar_bgcolor']); ?>'
                    });
                });
            </script>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="bar_text"><?php esc_html_e('Sticky Bar Text', 'high-performance-sticky-bar-main'); ?></label></th>
                    <td><input type="text" name="bar_text" value="<?php echo esc_attr($settings['bar_text']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bar_bgcolor"><?php esc_html_e('Background Color', 'high-performance-sticky-bar-main'); ?></label></th>
                    <td><input type="text" name="bar_bgcolor" value="<?php echo esc_attr($settings['bar_bgcolor']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cookie_hours"><?php esc_html_e('Hide Duration (Hours)', 'high-performance-sticky-bar-main'); ?></label></th>
                    <td><input type="number" min="1" max="8760" name="cookie_hours" value="<?php echo esc_attr($settings['cookie_hours']); ?>" class="small-text" />
                    <span class="description"><?php esc_html_e('Number of hours the bar stays hidden after a visitor closes it', 'high-performance-sticky-bar-main'); ?></span></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Sticky Bar', 'high-performance-sticky-bar-main'); ?></th>
                    <td>
                        <input type="checkbox" name="enable_bar" value="1" <?php checked($settings['enable_bar'], 1); ?> />
                        <span class="description"><?php esc_html_e('Check to activate sticky bar on the site', 'high-performance-sticky-bar-main'); ?></span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Enqueues front-end assets only when the sticky bar is enabled.
 *
 * This function:
 * 1. Loads the JavaScript file for bar functionality
 * 2. Passes necessary data to JavaScript via wp_localize_script
 * 3. Adds inline CSS to style the sticky bar using admin settings
 *
 * @return void
 */
function hyroes_sticky_bar_enqueue_scripts() {
    $settings = get_option('hyroes_sticky_bar_settings');
    
    // Only proceed if bar is enabled
    if (empty($settings['enable_bar'])) {
        return;
    }
    
    // Enqueue the JS file
    wp_enqueue_script(
        'hyroes-sticky-bar-js',
        plugin_dir_url(__FILE__) . 'sticky-bar.js',
        array('jquery'),
        '1.4',
        true
    );
    
    // Pass data to JS
    wp_localize_script(
        'hyroes-sticky-bar-js', 
        'HyroesStickyBarData', 
        array(
            'barText' => $settings['bar_text'],
            'cookieHours' => intval($settings['cookie_hours'])
        )
    );
    
    // Add CSS
    wp_add_inline_style(
        'wp-block-library',
        '
        #hyroes-sticky-bar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: ' . esc_attr($settings['bar_bgcolor']) . ';
            color: #ffffff;
            z-index: 999999;
            padding: 12px 30px 12px 10px;
            text-align: center;
            box-sizing: border-box;
            font-size: 14px;
            line-height: 1.4;
        }
        #hyroes-sticky-bar-close {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }
        body.admin-bar #hyroes-sticky-bar {
            top: 32px;
        }
        @media screen and (max-width: 782px) {
            body.admin-bar #hyroes-sticky-bar {
                top: 46px;
            }
        }
        body.has-hyroes-sticky-bar {
            padding-top: 40px;
        }
        '
    );
}
add_action('wp_enqueue_scripts', 'hyroes_sticky_bar_enqueue_scripts');

/**
 * Outputs the sticky bar HTML to the website footer.
 *
 * This function adds the sticky bar HTML only when the bar is enabled
 * in the admin settings. The HTML includes:
 * - The container div with proper ID for styling
 * - The message text from settings
 * - A close button that triggers the JavaScript hide functionality
 */
function hyroes_sticky_bar_add_html() {
    $settings = get_option('hyroes_sticky_bar_settings');
    
    // Only proceed if bar is enabled
    if (empty($settings['enable_bar'])) {
        return;
    }
    
    echo '<div id="hyroes-sticky-bar">';
    echo esc_html($settings['bar_text']);
    echo '<span id="hyroes-sticky-bar-close">×</span>';
    echo '</div>';
}
add_action('wp_footer', 'hyroes_sticky_bar_add_html', 20);