<?php
/*
Plugin Name: Hyroes Sticky Bar
Description: Adds a customizable sticky bar that can be closed, storing user preference in cookies.
Version: 1.4
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

// Add the sticky bar HTML to the site
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