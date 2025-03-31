<?php
/*
Plugin Name: Hyroes Sticky Bar
Description: Adds a customizable sticky bar that can be closed, storing user preference in cookies.
Version: 1.1
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
            '1.1',
            true
        );
        wp_localize_script('hyroes-sticky-bar-js', 'HyroesStickyBarData', array(
            'barText' => $settings['bar_text'],
            'bgColor' => $settings['bar_bgcolor'],
            'cookieHours' => intval($settings['cookie_hours'])
        ));
        
        // Add CSS to ensure proper styling
        wp_add_inline_style('wp-block-library', '
            #hyroes-sticky-bar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                z-index: 999999 !important;
                box-sizing: border-box !important;
                margin: 0 !important;
                padding: 10px !important;
                transition: all 0.3s ease !important;
            }
            body.admin-bar #hyroes-sticky-bar {
                top: 32px !important;
            }
            @media screen and (max-width: 782px) {
                body.admin-bar #hyroes-sticky-bar {
                    top: 46px !important;
                }
            }
            /* Push body content down when bar is visible */
            body.has-hyroes-sticky-bar {
                padding-top: 40px !important;
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'hyroes_sticky_bar_enqueue_scripts');

// Create the sticky bar directly in the body
function hyroes_sticky_bar_body_open() {
    $settings = get_option('hyroes_sticky_bar_settings');
    if (!empty($settings['enable_bar'])) {
        echo '<div id="hyroes-sticky-bar" style="display:none;"></div>';
        echo '<script>document.body.classList.add("has-hyroes-sticky-bar");</script>';
    }
}
add_action('wp_body_open', 'hyroes_sticky_bar_body_open', 5);

// Fallback for themes that don't support wp_body_open
function hyroes_sticky_bar_fallback() {
    $settings = get_option('hyroes_sticky_bar_settings');
    if (!empty($settings['enable_bar'])) {
        if (!did_action('wp_body_open')) {
            echo '<div id="hyroes-sticky-bar" style="display:none;"></div>';
            echo '<script>document.body.classList.add("has-hyroes-sticky-bar");</script>';
        }
    }
}
add_action('wp_footer', 'hyroes_sticky_bar_fallback', 0);