<?php
/**
 * Plugin Name: AI Web Builder
 * Plugin URI: https://sawahsolutions.com
 * Description: Generate comprehensive website concepts with AI-powered wireframes, cost estimates, and live demos
 * Version: 1.0.2
 * Author: Mohamed Sawah
 * Author URI: https://sawahsolutions.com
 * License: GPL v2 or later
 * Text Domain: ai-web-builder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AI_WEB_BUILDER_VERSION', '1.0.2');
define('AI_WEB_BUILDER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_WEB_BUILDER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_WEB_BUILDER_ASSETS_URL', AI_WEB_BUILDER_PLUGIN_URL . 'assets/');

class AI_Web_Builder {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize components
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_shortcode('ai_web_builder', array($this, 'render_shortcode'));
        
        // Admin menu
        if (is_admin()) {
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_init', array($this, 'admin_init'));
        }
        
        // AJAX handlers
        add_action('wp_ajax_awb_generate_concept', array($this, 'ajax_generate_concept'));
        add_action('wp_ajax_nopriv_awb_generate_concept', array($this, 'ajax_generate_concept'));
        add_action('wp_ajax_awb_generate_demo', array($this, 'ajax_generate_demo'));
        add_action('wp_ajax_nopriv_awb_generate_demo', array($this, 'ajax_generate_demo'));
    }
    
    private function load_dependencies() {
        require_once AI_WEB_BUILDER_PLUGIN_DIR . 'includes/class-ai-generator.php';
        require_once AI_WEB_BUILDER_PLUGIN_DIR . 'includes/class-wireframe-generator.php';
        require_once AI_WEB_BUILDER_PLUGIN_DIR . 'includes/class-demo-generator.php';
        require_once AI_WEB_BUILDER_PLUGIN_DIR . 'includes/functions.php';
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('ai-web-builder-style', AI_WEB_BUILDER_ASSETS_URL . 'css/frontend.css', array(), AI_WEB_BUILDER_VERSION);
        wp_enqueue_script('ai-web-builder-script', AI_WEB_BUILDER_ASSETS_URL . 'js/frontend.js', array('jquery'), AI_WEB_BUILDER_VERSION, true);
        
        wp_localize_script('ai-web-builder-script', 'awb_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('awb_nonce'),
            'assets_url' => AI_WEB_BUILDER_ASSETS_URL
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'ai-web-builder') !== false) {
            wp_enqueue_style('ai-web-builder-admin', AI_WEB_BUILDER_ASSETS_URL . 'css/admin.css', array(), AI_WEB_BUILDER_VERSION);
            wp_enqueue_script('ai-web-builder-admin', AI_WEB_BUILDER_ASSETS_URL . 'js/admin.js', array('jquery'), AI_WEB_BUILDER_VERSION, true);
        }
    }
    
    public function admin_menu() {
        add_menu_page(
            'AI Web Builder',
            'AI Web Builder',
            'manage_options',
            'ai-web-builder',
            array($this, 'admin_page'),
            'dashicons-admin-site-alt3',
            30
        );
        
        add_submenu_page(
            'ai-web-builder',
            'Settings',
            'Settings',
            'manage_options',
            'ai-web-builder-settings',
            array($this, 'settings_page')
        );
    }
    
    public function admin_init() {
        register_setting('awb_settings', 'awb_openai_api_key');
        register_setting('awb_settings', 'awb_openrouter_api_key');
        register_setting('awb_settings', 'awb_default_model');
        register_setting('awb_settings', 'awb_enable_caching');
        register_setting('awb_settings', 'awb_cache_duration');
    }
    
    public function admin_page() {
        include AI_WEB_BUILDER_PLUGIN_DIR . 'admin/admin-page.php';
    }
    
    public function settings_page() {
        include AI_WEB_BUILDER_PLUGIN_DIR . 'admin/settings-page.php';
    }
    
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'default',
            'show_demo_button' => 'true'
        ), $atts);
        
        ob_start();
        include AI_WEB_BUILDER_PLUGIN_DIR . 'templates/frontend-form.php';
        return ob_get_clean();
    }
    
    public function ajax_generate_concept() {
        check_ajax_referer('awb_nonce', 'nonce');
        
        $form_data = sanitize_text_field($_POST['form_data']);
        $parsed_data = json_decode(stripslashes($form_data), true);
        
        if (!$parsed_data) {
            wp_die(json_encode(array('success' => false, 'error' => 'Invalid form data')));
        }
        
        $generator = new AWB_AI_Generator();
        $result = $generator->generate_concept($parsed_data);
        
        wp_die(json_encode($result));
    }
    
    public function ajax_generate_demo() {
        check_ajax_referer('awb_nonce', 'nonce');
        
        $concept_data = sanitize_text_field($_POST['concept_data']);
        $parsed_data = json_decode(stripslashes($concept_data), true);
        
        if (!$parsed_data) {
            wp_die(json_encode(array('success' => false, 'error' => 'Invalid concept data')));
        }
        
        $demo_generator = new AWB_Demo_Generator();
        $result = $demo_generator->generate_demo($parsed_data);
        
        wp_die(json_encode($result));
    }
    
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        if (!get_option('awb_enable_caching')) {
            update_option('awb_enable_caching', '1');
        }
        if (!get_option('awb_cache_duration')) {
            update_option('awb_cache_duration', '3600');
        }
        if (!get_option('awb_default_model')) {
            update_option('awb_default_model', 'openai');
        }
    }
    
    public function deactivate() {
        // Clean up transients
        delete_transient('awb_cached_concepts');
    }
    
    private function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'awb_generated_concepts';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_data longtext NOT NULL,
            concept_data longtext NOT NULL,
            demo_url varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialize the plugin
new AI_Web_Builder();