<?php
/**
 * Plugin Name: NewsAPI Articles
 * Description: Fetches news articles from NewsAPI based on a topic and displays them via a custom post type and shortcode [newsapi_articles].
 * Version: 1.0.0
 * Author: Alex Stoian
 * License: GPL-2.0-or-later
 * Requires PHP: 7.2
 * Requires at least: 5.5
 * Tested up to: 6.7.2
 */

// Security check: abort if this file is called directly.
if (!defined('WPINC')) { die; }

// Include required class files (assuming they are in subfolders as per structure).
require_once plugin_dir_path(__FILE__) . 'admin/class-newsapi-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/class-newsapi-frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-newsapi-api.php';

class NewsAPI_Plugin {
    private $admin;
    private $frontend;

    public function __construct() {
        // Instantiate the admin and frontend classes
        $this->admin = new NewsAPI_Admin();
        $this->frontend = new NewsAPI_Frontend();

        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'on_activate'));
        register_deactivation_hook(__FILE__, array($this, 'on_deactivate'));

        // Hook into init to register post type and shortcode
        add_action('init', array($this->frontend, 'register_post_type'));
        add_action('init', array($this->frontend, 'register_shortcode'));

        // The admin class constructor hooks will add the settings page
        // (No need to call anything else here for admin, as it's handled in NewsAPI_Admin)
    }

    public function on_activate() {
        // Flush rewrite rules to register custom post type permalinks
        $this->frontend->register_post_type();
        flush_rewrite_rules();
    }

    public function on_deactivate() {
        // Cleanup: flush rewrite rules to remove custom post type rules
        flush_rewrite_rules();
    }
}

// Initialize the plugin
$GLOBALS['newsapi_plugin'] = new NewsAPI_Plugin();
