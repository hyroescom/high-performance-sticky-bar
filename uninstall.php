<?php
/**
 * Uninstall Lightweight High Performance Sticky Bar
 *
 * This file runs when the plugin is uninstalled to clean up the database.
 * It removes all plugin options to ensure a clean uninstallation.
 *
 * @package Lightweight-High-Performance-Sticky-Bar
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('hyroes_sticky_bar_settings');

// Clear any cached data
wp_cache_flush();
