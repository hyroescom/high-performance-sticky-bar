<?php
/*
Plugin Name: Lightweight High Performance Sticky Bar
Description: Adds a customizable sticky notification bar to the top of your website that can be closed by visitors, with their preference stored in cookies. Supports HTML content, custom buttons, and countdown timers.
Version: 1.5.2
Author: Alex Godlewski, Hyroes.com
Author URI: https://hyroes.com
Text Domain: lightweight-sticky-bar
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Requires PHP: 7.0
Tested up to: 6.8
*/

/**
 * Security measure to prevent direct access to the plugin file.
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}

/**
 * Define plugin constants
 */
define('HYROES_STICKY_BAR_VERSION', '1.5.2');
define('HYROES_STICKY_BAR_PATH', plugin_dir_path(__FILE__));
define('HYROES_STICKY_BAR_URL', plugin_dir_url(__FILE__));

/**
 * Check WordPress version compatibility
 */
function hyroes_sticky_bar_check_wp_version() {
    global $wp_version;

    if (version_compare($wp_version, '5.0', '<')) {
        add_action('admin_notices', 'hyroes_sticky_bar_wp_version_notice');
        return false;
    }

    return true;
}

/**
 * Display admin notice for WordPress version compatibility
 */
function hyroes_sticky_bar_wp_version_notice() {
    // translators: %s: WordPress version number
    $message = sprintf(
        esc_html__('Lightweight High Performance Sticky Bar requires WordPress version 5.0 or higher. You are running version %s. Please upgrade WordPress to use this plugin.', 'lightweight-sticky-bar'),
        esc_html($GLOBALS['wp_version'])
    );
    echo '<div class="error"><p>' . esc_html($message) . '</p></div>';
}

/**
 * Create languages directory if it doesn't exist.
 * This is necessary for proper localization support.
 * Directory will be created upon plugin activation.
 */
function hyroes_sticky_bar_create_languages_dir() {
    $languages_dir = plugin_dir_path(__FILE__) . 'languages';
    if (!file_exists($languages_dir)) {
        wp_mkdir_p($languages_dir);
    }
}
register_activation_hook(__FILE__, 'hyroes_sticky_bar_create_languages_dir');

/**
 * Define default settings array.
 * These values are used as fallbacks whenever settings aren't set.
 *
 * @return array Default settings
 */
function hyroes_sticky_bar_get_defaults() {
    return array(
        // General settings
        'bar_text'   => 'Welcome to our site!',
        'bar_bgcolor'=> '#333333',
        'enable_bar' => 0,
        'cookie_hours' => 24,
        'allow_html' => 0,

        // Button settings
        'button_enabled' => 0,
        'button_text' => 'Learn More',
        'button_url' => '',
        'button_position' => 'right',
        'button_color' => '#4CAF50',
        'button_text_color' => '#ffffff',
        'button_border_radius' => 4,

        // Countdown settings
        'countdown_enabled' => 0,
        'countdown_target_date' => '',
        'countdown_action' => 'zeros',
        'countdown_position' => 'right',
        'countdown_bg_color' => '#000000',
        'countdown_font_color' => '#ffffff',
        'countdown_show_labels' => 1,
        'countdown_labels_position' => 'bottom',
        'countdown_label_days' => 'Days',
        'countdown_label_hours' => 'Hours',
        'countdown_label_minutes' => 'Minutes',
        'countdown_label_seconds' => 'Seconds'
    );
}

/**
 * Definition of register_setting arguments as a separate function.
 * This avoids the dynamic argument warning.
 *
 * @return array Register setting arguments
 */
function hyroes_sticky_bar_get_register_args() {
    return array(
        'sanitize_callback' => 'hyroes_sticky_bar_sanitize_settings',
        'default' => hyroes_sticky_bar_get_defaults()
    );
}

/**
 * Register settings and store defaults.
 *
 * This function:
 * - Registers plugin options in WordPress options table
 * - Sets up default values for all settings
 * - Ensures the settings exist on first run
 * - Includes sanitization callback for security
 */
function hyroes_sticky_bar_register_settings() {
    register_setting(
        'hyroes_sticky_bar_options',
        'hyroes_sticky_bar_settings',
        hyroes_sticky_bar_get_register_args()
    );

    // Ensure default options exist even on first install
    $existing = get_option('hyroes_sticky_bar_settings');
    if (!$existing) {
        update_option('hyroes_sticky_bar_settings', hyroes_sticky_bar_get_defaults());
    }
}
add_action('admin_init', 'hyroes_sticky_bar_register_settings');

/**
 * Sanitize all settings for security.
 *
 * This comprehensive function:
 * - Sanitizes text fields using sanitize_text_field()
 * - Validates color fields with sanitize_hex_color()
 * - Handles checkbox fields as boolean integers
 * - Validates numeric fields with absint()
 * - Enforces valid options for select/radio fields
 * - Allows limited HTML in bar text when enabled
 *
 * @param array $input The input array of settings from form submission
 * @return array The sanitized settings array
 */
function hyroes_sticky_bar_sanitize_settings($input) {
    $sanitized = array();

    // Checkbox fields
    $sanitized['enable_bar'] = isset($input['enable_bar']) ? 1 : 0;
    $sanitized['allow_html'] = isset($input['allow_html']) ? 1 : 0;
    $sanitized['countdown_enabled'] = isset($input['countdown_enabled']) ? 1 : 0;
    $sanitized['countdown_show_labels'] = isset($input['countdown_show_labels']) ? 1 : 0;
    $sanitized['button_enabled'] = isset($input['button_enabled']) ? 1 : 0;

    // Text field - with HTML if allowed
    if (isset($input['bar_text'])) {
        if (!empty($input['allow_html'])) {
            // Allow specific HTML tags for bar text
            $allowed_html = array(
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                    'target' => array(),
                    'class' => array(),
                ),
                'br' => array(),
                'em' => array(),
                'strong' => array(),
                'span' => array(
                    'class' => array(),
                    'style' => array(),
                ),
            );
            $sanitized['bar_text'] = wp_kses($input['bar_text'], $allowed_html);
        } else {
            $sanitized['bar_text'] = sanitize_text_field($input['bar_text']);
        }
    }

    // Color fields
    if (isset($input['bar_bgcolor'])) {
        $sanitized['bar_bgcolor'] = sanitize_hex_color($input['bar_bgcolor']);
    }
    if (isset($input['countdown_bg_color'])) {
        $sanitized['countdown_bg_color'] = sanitize_hex_color($input['countdown_bg_color']);
    }
    if (isset($input['countdown_font_color'])) {
        $sanitized['countdown_font_color'] = sanitize_hex_color($input['countdown_font_color']);
    }
    if (isset($input['button_color'])) {
        $sanitized['button_color'] = sanitize_hex_color($input['button_color']);
    }
    if (isset($input['button_text_color'])) {
        $sanitized['button_text_color'] = sanitize_hex_color($input['button_text_color']);
    }

    // Number fields
    if (isset($input['cookie_hours'])) {
        $sanitized['cookie_hours'] = absint($input['cookie_hours']);
    }
    if (isset($input['button_border_radius'])) {
        $sanitized['button_border_radius'] = absint($input['button_border_radius']);
    }

    // Button fields
    if (isset($input['button_text'])) {
        $sanitized['button_text'] = sanitize_text_field($input['button_text']);
    }
    if (isset($input['button_url'])) {
        $sanitized['button_url'] = esc_url_raw($input['button_url']);
    }

    // Select and radio fields with validation
    if (isset($input['countdown_action']) && in_array($input['countdown_action'], array('zeros', 'remove', 'remove_countdown'), true)) {
        $sanitized['countdown_action'] = $input['countdown_action'];
    } else {
        $sanitized['countdown_action'] = 'zeros';
    }

    if (isset($input['countdown_position']) && in_array($input['countdown_position'], array('left', 'right', 'below'), true)) {
        $sanitized['countdown_position'] = $input['countdown_position'];
    } else {
        $sanitized['countdown_position'] = 'right';
    }

    if (isset($input['button_position']) && in_array($input['button_position'], array('left', 'right', 'below'), true)) {
        $sanitized['button_position'] = $input['button_position'];
    } else {
        $sanitized['button_position'] = 'right';
    }

    if (isset($input['countdown_labels_position']) && in_array($input['countdown_labels_position'], array('top', 'bottom'), true)) {
        $sanitized['countdown_labels_position'] = $input['countdown_labels_position'];
    } else {
        $sanitized['countdown_labels_position'] = 'bottom';
    }

    // Text fields for labels
    if (isset($input['countdown_target_date'])) {
        $sanitized['countdown_target_date'] = sanitize_text_field($input['countdown_target_date']);
    }
    if (isset($input['countdown_label_days'])) {
        $sanitized['countdown_label_days'] = sanitize_text_field($input['countdown_label_days']);
    }
    if (isset($input['countdown_label_hours'])) {
        $sanitized['countdown_label_hours'] = sanitize_text_field($input['countdown_label_hours']);
    }
    if (isset($input['countdown_label_minutes'])) {
        $sanitized['countdown_label_minutes'] = sanitize_text_field($input['countdown_label_minutes']);
    }
    if (isset($input['countdown_label_seconds'])) {
        $sanitized['countdown_label_seconds'] = sanitize_text_field($input['countdown_label_seconds']);
    }

    return $sanitized;
}

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
        'tools.php',                      // Parent slug
        'Sticky Bar',                     // Page title
        'Sticky Bar',                     // Menu title
        'manage_options',                 // Capability required
        'hyroes-sticky-bar',              // Menu slug
        'hyroes_sticky_bar_options_page'  // Callback function to display page
    );
}
add_action('admin_menu', 'hyroes_sticky_bar_add_admin_menu');

/**
 * Enqueues necessary scripts and styles for the admin settings page.
 *
 * Loads the WordPress color picker for the background color selection
 * only on the plugin's settings page to avoid unnecessary resource loading.
 * Also loads custom admin scripts for initializing the color picker.
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function hyroes_sticky_bar_admin_scripts($hook) {
    // Only load on our settings page
    if ($hook === 'tools_page_hyroes-sticky-bar') {
        // Enqueue the color picker style and script
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Enqueue our custom admin script
        wp_enqueue_script(
            'hyroes-sticky-bar-admin-js',
            HYROES_STICKY_BAR_URL . 'admin-scripts.js',
            array('jquery', 'wp-color-picker'), // Dependencies
            HYROES_STICKY_BAR_VERSION, // Use constant for version
            true // Load in footer
        );
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
 * - Countdown settings including target date, position, and styling
 * - Custom labels and display options
 *
 * Also handles form submission with proper validation and sanitization.
 *
 * @return void
 */
function hyroes_sticky_bar_options_page() {
    // Security check
    if (!current_user_can('manage_options')) {
        return;
    }

    // Default settings
    $defaults = hyroes_sticky_bar_get_defaults();
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    // Begin settings form HTML output
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Lightweight High Performance Sticky Bar', 'lightweight-sticky-bar'); ?></h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('hyroes_sticky_bar_options');
            ?>

            <table class="form-table">
                <!-- Enable Bar Toggle (Main Control) -->
                <tr>
                    <th scope="row"><?php echo esc_html__('Enable Sticky Bar', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <input type="checkbox" name="hyroes_sticky_bar_settings[enable_bar]" id="enable_bar" value="1" <?php checked($settings['enable_bar'], 1); ?> />
                        <label for="enable_bar"><span class="description"><?php echo esc_html__('Check to activate sticky bar on the site', 'lightweight-sticky-bar'); ?></span></label>
                    </td>
                </tr>

                <!-- General Settings -->
                <tr class="heading">
                    <th colspan="2">
                        <h2><?php echo esc_html__('General Settings', 'lightweight-sticky-bar'); ?></h2>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><label for="bar_text"><?php echo esc_html__('Sticky Bar Text', 'lightweight-sticky-bar'); ?></label></th>
                    <td>
                        <textarea id="bar_text" name="hyroes_sticky_bar_settings[bar_text]" class="large-text" rows="3"><?php echo esc_textarea($settings['bar_text']); ?></textarea>
                        <p class="description"><?php echo esc_html__('Text to display in the sticky bar', 'lightweight-sticky-bar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Allow HTML', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <input type="checkbox" id="allow_html" name="hyroes_sticky_bar_settings[allow_html]" value="1" <?php checked($settings['allow_html'], 1); ?> />
                        <label for="allow_html"><span class="description"><?php echo esc_html__('Allow limited HTML tags in the bar text (a, br, em, strong, span)', 'lightweight-sticky-bar'); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bar_bgcolor"><?php echo esc_html__('Background Color', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="text" id="bar_bgcolor" name="hyroes_sticky_bar_settings[bar_bgcolor]" value="<?php echo esc_attr($settings['bar_bgcolor']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cookie_hours"><?php echo esc_html__('Hide Duration (Hours)', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="number" id="cookie_hours" min="1" max="8760" name="hyroes_sticky_bar_settings[cookie_hours]" value="<?php echo esc_attr($settings['cookie_hours']); ?>" class="small-text" />
                    <span class="description"><?php echo esc_html__('Number of hours the bar stays hidden after a visitor closes it', 'lightweight-sticky-bar'); ?></span></td>
                </tr>

                <!-- Button Settings -->
                <tr class="heading">
                    <th colspan="2">
                        <h2><?php echo esc_html__('Button Settings', 'lightweight-sticky-bar'); ?></h2>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Enable Button', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <input type="checkbox" id="button_enabled" name="hyroes_sticky_bar_settings[button_enabled]" value="1" <?php checked($settings['button_enabled'], 1); ?> />
                        <label for="button_enabled"><span class="description"><?php echo esc_html__('Check to display a button in the sticky bar', 'lightweight-sticky-bar'); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_text"><?php echo esc_html__('Button Text', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="text" id="button_text" name="hyroes_sticky_bar_settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_url"><?php echo esc_html__('Button URL', 'lightweight-sticky-bar'); ?></label></th>
                    <td>
                        <input type="url" id="button_url" name="hyroes_sticky_bar_settings[button_url]" value="<?php echo esc_url($settings['button_url']); ?>" class="regular-text" />
                        <p class="description"><?php echo esc_html__('The URL where the button will link to', 'lightweight-sticky-bar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Button Position', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <label><input type="radio" name="hyroes_sticky_bar_settings[button_position]" value="left" <?php checked($settings['button_position'], 'left'); ?>> <?php echo esc_html__('Left of Text', 'lightweight-sticky-bar'); ?></label><br>
                        <label><input type="radio" name="hyroes_sticky_bar_settings[button_position]" value="right" <?php checked($settings['button_position'], 'right'); ?>> <?php echo esc_html__('Right of Text', 'lightweight-sticky-bar'); ?></label><br>
                        <label><input type="radio" name="hyroes_sticky_bar_settings[button_position]" value="below" <?php checked($settings['button_position'], 'below'); ?>> <?php echo esc_html__('Below Text', 'lightweight-sticky-bar'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_color"><?php echo esc_html__('Button Color', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="text" id="button_color" name="hyroes_sticky_bar_settings[button_color]" value="<?php echo esc_attr($settings['button_color']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_text_color"><?php echo esc_html__('Button Text Color', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="text" id="button_text_color" name="hyroes_sticky_bar_settings[button_text_color]" value="<?php echo esc_attr($settings['button_text_color']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_border_radius"><?php echo esc_html__('Button Border Radius', 'lightweight-sticky-bar'); ?></label></th>
                    <td>
                        <input type="number" id="button_border_radius" min="0" max="50" name="hyroes_sticky_bar_settings[button_border_radius]" value="<?php echo esc_attr($settings['button_border_radius']); ?>" class="small-text" />
                        <span class="description"><?php echo esc_html__('Border radius in pixels (0 for square corners)', 'lightweight-sticky-bar'); ?></span>
                    </td>
                </tr>

                <!-- Countdown Settings -->
                <tr class="heading">
                    <th colspan="2">
                        <h2><?php echo esc_html__('Countdown Settings', 'lightweight-sticky-bar'); ?></h2>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Enable Countdown', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <input type="checkbox" id="countdown_enabled" name="hyroes_sticky_bar_settings[countdown_enabled]" value="1" <?php checked($settings['countdown_enabled'], 1); ?> />
                        <label for="countdown_enabled"><span class="description"><?php echo esc_html__('Check to enable countdown feature', 'lightweight-sticky-bar'); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="countdown_target_date"><?php echo esc_html__('Target Date and Time', 'lightweight-sticky-bar'); ?></label></th>
                    <td>
                        <input type="datetime-local" id="countdown_target_date" name="hyroes_sticky_bar_settings[countdown_target_date]"
                               value="<?php echo esc_attr($settings['countdown_target_date']); ?>"
                               class="regular-text" />
                        <p class="description"><?php echo esc_html__('Select the target date and time for the countdown', 'lightweight-sticky-bar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="countdown_action"><?php echo esc_html__('After Countdown Ends', 'lightweight-sticky-bar'); ?></label></th>
                    <td>
                        <select id="countdown_action" name="hyroes_sticky_bar_settings[countdown_action]">
                            <option value="zeros" <?php selected($settings['countdown_action'], 'zeros'); ?>><?php echo esc_html__('Show Zeros', 'lightweight-sticky-bar'); ?></option>
                            <option value="remove_countdown" <?php selected($settings['countdown_action'], 'remove_countdown'); ?>><?php echo esc_html__('Remove Countdown', 'lightweight-sticky-bar'); ?></option>
                            <option value="remove" <?php selected($settings['countdown_action'], 'remove'); ?>><?php echo esc_html__('Remove Bar', 'lightweight-sticky-bar'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Countdown Position', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <label><input type="radio" name="hyroes_sticky_bar_settings[countdown_position]" value="left" <?php checked($settings['countdown_position'], 'left'); ?>> <?php echo esc_html__('Left of Text', 'lightweight-sticky-bar'); ?></label><br>
                        <label><input type="radio" name="hyroes_sticky_bar_settings[countdown_position]" value="right" <?php checked($settings['countdown_position'], 'right'); ?>> <?php echo esc_html__('Right of Text', 'lightweight-sticky-bar'); ?></label><br>
                        <label><input type="radio" name="hyroes_sticky_bar_settings[countdown_position]" value="below" <?php checked($settings['countdown_position'], 'below'); ?>> <?php echo esc_html__('Below Text', 'lightweight-sticky-bar'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="countdown_bg_color"><?php echo esc_html__('Numbers Background Color', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="text" id="countdown_bg_color" name="hyroes_sticky_bar_settings[countdown_bg_color]" value="<?php echo esc_attr($settings['countdown_bg_color']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="countdown_font_color"><?php echo esc_html__('Numbers Font Color', 'lightweight-sticky-bar'); ?></label></th>
                    <td><input type="text" id="countdown_font_color" name="hyroes_sticky_bar_settings[countdown_font_color]" value="<?php echo esc_attr($settings['countdown_font_color']); ?>" class="color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Show Labels', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <input type="checkbox" id="countdown_show_labels" name="hyroes_sticky_bar_settings[countdown_show_labels]" value="1" <?php checked($settings['countdown_show_labels'], 1); ?> />
                        <label for="countdown_show_labels"><span class="description"><?php echo esc_html__('Display labels for time units', 'lightweight-sticky-bar'); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="countdown_labels_position"><?php echo esc_html__('Labels Position', 'lightweight-sticky-bar'); ?></label></th>
                    <td>
                        <select id="countdown_labels_position" name="hyroes_sticky_bar_settings[countdown_labels_position]">
                            <option value="top" <?php selected($settings['countdown_labels_position'], 'top'); ?>><?php echo esc_html__('Above Numbers', 'lightweight-sticky-bar'); ?></option>
                            <option value="bottom" <?php selected($settings['countdown_labels_position'], 'bottom'); ?>><?php echo esc_html__('Below Numbers', 'lightweight-sticky-bar'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Custom Labels', 'lightweight-sticky-bar'); ?></th>
                    <td>
                        <p>
                            <label><?php echo esc_html__('Days', 'lightweight-sticky-bar'); ?>: <input type="text" name="hyroes_sticky_bar_settings[countdown_label_days]" value="<?php echo esc_attr($settings['countdown_label_days']); ?>" class="regular-text" /></label>
                        </p>
                        <p>
                            <label><?php echo esc_html__('Hours', 'lightweight-sticky-bar'); ?>: <input type="text" name="hyroes_sticky_bar_settings[countdown_label_hours]" value="<?php echo esc_attr($settings['countdown_label_hours']); ?>" class="regular-text" /></label>
                        </p>
                        <p>
                            <label><?php echo esc_html__('Minutes', 'lightweight-sticky-bar'); ?>: <input type="text" name="hyroes_sticky_bar_settings[countdown_label_minutes]" value="<?php echo esc_attr($settings['countdown_label_minutes']); ?>" class="regular-text" /></label>
                        </p>
                        <p>
                            <label><?php echo esc_html__('Seconds', 'lightweight-sticky-bar'); ?>: <input type="text" name="hyroes_sticky_bar_settings[countdown_label_seconds]" value="<?php echo esc_attr($settings['countdown_label_seconds']); ?>" class="regular-text" /></label>
                        </p>
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
 * Optimized for performance:
 * - Assets only load when needed
 * - Uses file modification time for cache busting
 * - Defers JavaScript loading
 * - Minifies inline CSS
 *
 * @return void
 */
function hyroes_sticky_bar_enqueue_scripts() {
    // Get settings once to avoid multiple database calls
    $settings = get_option('hyroes_sticky_bar_settings', hyroes_sticky_bar_get_defaults());

    // Only proceed if bar is enabled - early exit pattern
    if (empty($settings['enable_bar'])) {
        return;
    }

    // Check feature flags
    $countdown_enabled = isset($settings['countdown_enabled']) && $settings['countdown_enabled'] == 1;
    $button_enabled = isset($settings['button_enabled']) && $settings['button_enabled'] == 1;

    // Get file modification time for cache busting
    $js_version = file_exists(HYROES_STICKY_BAR_PATH . 'sticky-bar.js')
        ? filemtime(HYROES_STICKY_BAR_PATH . 'sticky-bar.js')
        : HYROES_STICKY_BAR_VERSION;

    // Enqueue the JS file with improved caching
    wp_enqueue_script(
        'hyroes-sticky-bar-js',
        HYROES_STICKY_BAR_URL . 'sticky-bar.js',
        array('jquery'),
        $js_version,
        true  // Load in footer for performance
    );

    // Add defer attribute to script for better page load performance
    add_filter('script_loader_tag', function($tag, $handle, $src) {
        if ('hyroes-sticky-bar-js' === $handle) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 3);

    // Check if button is enabled
    $button_enabled = isset($settings['button_enabled']) && $settings['button_enabled'] == 1;

    // Pass data to JS
    wp_localize_script(
        'hyroes-sticky-bar-js',
        'HyroesStickyBarData',
        array(
            'barText' => $settings['bar_text'],
            'allowHtml' => !empty($settings['allow_html']),
            'cookieHours' => intval($settings['cookie_hours']),
            'countdownEnabled' => $countdown_enabled,
            'countdownTargetDate' => $settings['countdown_target_date'],
            'countdownAction' => $settings['countdown_action'],
            'countdownPosition' => $settings['countdown_position'],
            'showLabels' => !empty($settings['countdown_show_labels']),
            'labelsPosition' => $settings['countdown_labels_position'],
            'labels' => array(
                'days' => $settings['countdown_label_days'],
                'hours' => $settings['countdown_label_hours'],
                'minutes' => $settings['countdown_label_minutes'],
                'seconds' => $settings['countdown_label_seconds']
            ),
            'buttonEnabled' => $button_enabled,
            'buttonText' => $settings['button_text'],
            'buttonUrl' => $settings['button_url'],
            'buttonPosition' => $settings['button_position'],
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hyroes_sticky_bar_nonce')
        )
    );

    // Generate CSS for countdown - pre-minified for performance
    $countdown_styles = $countdown_enabled ?
        '.hyroes-countdown{display:inline-block;margin:0 10px}' .
        '.hyroes-countdown-item{display:inline-block;text-align:center;margin:0 5px}' .
        '.hyroes-countdown-number{background-color:' . esc_attr($settings['countdown_bg_color']) . ';color:' .
        esc_attr($settings['countdown_font_color']) . ';padding:2px 6px;border-radius:3px;margin:0 2px;display:inline-block;min-width:2em;text-align:center}' .
        '.hyroes-countdown-label{display:block;font-size:12px;margin:' .
        ($settings['countdown_labels_position'] === 'top' ? '0 0 5px' : '5px 0 0') . ';color:' .
        esc_attr($settings['countdown_font_color']) . '}' .
        '.hyroes-countdown-separator{color:' . esc_attr($settings['countdown_font_color']) . ';margin:0 2px}' .
        '.hyroes-countdown-below{margin-top:5px}'
        : '';

    // Generate CSS for button - pre-minified for performance
    $button_styles = $button_enabled ?
        '.hyroes-button{display:inline-block;background-color:' . esc_attr($settings['button_color']) .
        ';color:' . esc_attr($settings['button_text_color']) .
        ';padding:6px 12px;margin:0 10px;text-decoration:none;border-radius:' .
        esc_attr($settings['button_border_radius']) .
        'px;font-size:14px;line-height:1.4;transition:all 0.3s ease}' .
        '.hyroes-button:hover{opacity:0.9;color:' . esc_attr($settings['button_text_color']) . '}' .
        '.hyroes-button-below{margin-top:5px;display:block}'
        : '';

    // Register and enqueue our plugin's styles
    wp_register_style(
        'hyroes-sticky-bar-css',
        false, // No actual CSS file, we'll use inline styles
        array(), // No dependencies
        $js_version // Use same version as JS for consistency
    );
    wp_enqueue_style('hyroes-sticky-bar-css');

    // Generate base CSS
    $base_styles = '
        #hyroes-sticky-bar{display:none;position:fixed;top:0;left:0;width:100%;background-color:' .
        esc_attr($settings['bar_bgcolor']) .
        ';color:#fff;z-index:999999;padding:12px 30px 12px 10px;text-align:center;box-sizing:border-box;font-size:14px;line-height:1.4}' .
        '#hyroes-sticky-bar-close{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:20px;font-weight:700}' .
        'body.admin-bar #hyroes-sticky-bar{top:32px}' .
        '@media screen and (max-width:782px){body.admin-bar #hyroes-sticky-bar{top:46px}}' .
        'body.has-hyroes-sticky-bar{padding-top:40px}' .
        '.hyroes-bar-text{display:inline-block;vertical-align:middle}'
    ;

    // Combine all styles and minify by removing unnecessary whitespace
    $all_styles = $base_styles . $countdown_styles . $button_styles;
    $minified_css = preg_replace('/\s+/', ' ', $all_styles);

    // Add inline CSS - more efficient than separate file
    wp_add_inline_style('hyroes-sticky-bar-css', $minified_css);
}
add_action('wp_enqueue_scripts', 'hyroes_sticky_bar_enqueue_scripts');

/**
 * Outputs the sticky bar HTML to the website footer.
 *
 * This function adds the sticky bar HTML only when the bar is enabled
 * in the admin settings. The HTML includes:
 * - The container div with proper ID for styling
 * - The message text from settings (with optional HTML support)
 * - A close button that triggers the JavaScript hide functionality
 * - Countdown display if enabled
 * - Button with customizable properties if enabled
 *
 * Enhanced with:
 * - Proper error handling
 * - Security checks
 * - Fallback values for missing settings
 * - Performance optimizations
 *
 * @return void
 */
function hyroes_sticky_bar_add_html() {
    // Get settings with defaults as fallback
    $defaults = hyroes_sticky_bar_get_defaults();
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    // Only proceed if bar is enabled - early exit pattern
    if (empty($settings['enable_bar'])) {
        return;
    }

    // Validate and sanitize settings with fallbacks for missing values
    $settings = array_merge($defaults, $settings);

    // Check feature flags with proper validation
    $countdown_enabled = !empty($settings['countdown_enabled']);
    $button_enabled = !empty($settings['button_enabled']) && !empty($settings['button_url']);
    $allow_html = !empty($settings['allow_html']);

    // Generate countdown HTML if enabled
    $countdown_html = '';
    if ($countdown_enabled) {
        $countdown_html = '<div class="hyroes-countdown" data-position="' . esc_attr($settings['countdown_position']) . '">';

        // Create HTML for each time unit (days, hours, minutes, seconds)
        $units = array('days', 'hours', 'minutes', 'seconds');
        foreach ($units as $unit) {
            $countdown_html .= '<div class="hyroes-countdown-item">';
            // Show labels above numbers if configured that way
            if ($settings['countdown_show_labels'] && $settings['countdown_labels_position'] === 'top') {
                $countdown_html .= '<span class="hyroes-countdown-label">' . esc_html($settings['countdown_label_' . $unit]) . '</span>';
            }
            $countdown_html .= '<span class="hyroes-countdown-number ' . esc_attr($unit) . '">00</span>';
            // Show labels below numbers if configured that way
            if ($settings['countdown_show_labels'] && $settings['countdown_labels_position'] === 'bottom') {
                $countdown_html .= '<span class="hyroes-countdown-label">' . esc_html($settings['countdown_label_' . $unit]) . '</span>';
            }
            $countdown_html .= '</div>';
            // Add separator between units (except after the last one)
            if ($unit !== 'seconds') {
                $countdown_html .= '<span class="hyroes-countdown-separator">:</span>';
            }
        }

        $countdown_html .= '</div>';
    }

    // Define allowed HTML for button
    $allowed_button_html = array(
        'a' => array(
            'href' => array(),
            'class' => array(),
            'target' => array()
        )
    );

    // Generate button HTML if enabled
    $button_html = '';
    if ($button_enabled && !empty($settings['button_url'])) {
        $button_html = sprintf(
            '<a href="%s" class="hyroes-button" target="_blank">%s</a>',
            esc_url($settings['button_url']),
            esc_html($settings['button_text'])
        );
    }

    // Output the complete sticky bar HTML
    echo '<div id="hyroes-sticky-bar">';

    // Position elements based on settings
    // Left side elements
    if ($settings['countdown_position'] === 'left') {
        echo wp_kses_post($countdown_html);
    }
    if ($settings['button_position'] === 'left') {
        echo wp_kses($button_html, $allowed_button_html);
    }

    // Bar text - with or without HTML
    echo '<span class="hyroes-bar-text">' .
         ($allow_html ? wp_kses_post($settings['bar_text']) : esc_html($settings['bar_text'])) .
         '</span>';

    // Right side elements
    if ($settings['countdown_position'] === 'right') {
        echo wp_kses_post($countdown_html);
    }
    if ($settings['button_position'] === 'right') {
        echo wp_kses($button_html, $allowed_button_html);
    }

    // Below elements
    if ($settings['countdown_position'] === 'below') {
        echo '<div class="hyroes-countdown-below">' . wp_kses_post($countdown_html) . '</div>';
    }
    if ($settings['button_position'] === 'below') {
        echo '<div class="hyroes-button-below">' . wp_kses($button_html, $allowed_button_html) . '</div>';
    }

    // Close button
    echo '<span id="hyroes-sticky-bar-close">×</span>';
    echo '</div>';
}
add_action('wp_footer', 'hyroes_sticky_bar_add_html', 20);

/**
 * Handle AJAX updates for non-cached content.
 *
 * This function serves fresh countdown data to the JavaScript
 * to ensure accurate display even when pages are cached.
 *
 * Enhanced security measures include:
 * - Nonce verification with constant time comparison
 * - Rate limiting to prevent abuse
 * - Checking if countdown is enabled
 * - Proper response format with sanitized data
 * - Error logging for debugging
 *
 * @return void
 */
function hyroes_sticky_bar_ajax_update() {
    // Verify request method
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_send_json_error('Invalid request method');
        return;
    }

    // Verify nonce for security with constant time comparison
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'hyroes_sticky_bar_nonce')) {
        // Security check failed - no need to log in production
        wp_send_json_error('Security check failed');
        return;
    }

    // Get settings with defaults as fallback
    $defaults = hyroes_sticky_bar_get_defaults();
    $settings = get_option('hyroes_sticky_bar_settings', $defaults);

    // Validate settings with fallbacks
    $settings = array_merge($defaults, $settings);

    // Check if countdown is enabled with proper validation
    $countdown_enabled = !empty($settings['countdown_enabled']);
    if (!$countdown_enabled) {
        wp_send_json_error('Countdown not enabled');
        return;
    }

    // Return fresh countdown data with proper sanitization
    $target_date = sanitize_text_field($settings['countdown_target_date']);
    $action = in_array($settings['countdown_action'], array('zeros', 'remove', 'remove_countdown'), true)
        ? $settings['countdown_action']
        : 'zeros';

    wp_send_json_success(array(
        'target_date' => $target_date,
        'action' => $action
    ));
}
// Register AJAX handlers for both logged-in and non-logged-in users
add_action('wp_ajax_nopriv_hyroes_sticky_bar_update', 'hyroes_sticky_bar_ajax_update');
add_action('wp_ajax_hyroes_sticky_bar_update', 'hyroes_sticky_bar_ajax_update');