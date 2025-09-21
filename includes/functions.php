<?php
/**
 * Helper Functions for AI Web Builder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize form data recursively
 */
function awb_sanitize_form_data($data) {
    if (is_array($data)) {
        return array_map('awb_sanitize_form_data', $data);
    }
    return sanitize_text_field($data);
}

/**
 * Validate API keys
 */
function awb_validate_api_keys() {
    $openai_key = get_option('awb_openai_api_key');
    $openrouter_key = get_option('awb_openrouter_api_key');
    
    return !empty($openai_key) || !empty($openrouter_key);
}

/**
 * Log AI Web Builder events
 */
function awb_log($message, $type = 'info') {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("AI Web Builder [{$type}]: " . $message);
    }
}

/**
 * Get concept statistics
 */
function awb_get_concept_stats() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'awb_generated_concepts';
    
    $total_concepts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $concepts_today = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = %s",
        current_time('Y-m-d')
    ));
    $concepts_this_month = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE MONTH(created_at) = %d AND YEAR(created_at) = %d",
        current_time('n'),
        current_time('Y')
    ));
    
    return array(
        'total' => $total_concepts,
        'today' => $concepts_today,
        'this_month' => $concepts_this_month
    );
}

/**
 * Clean up old cached data
 */
function awb_cleanup_cache() {
    // Clean up old transients
    global $wpdb;
    
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_awb_%' AND option_value < UNIX_TIMESTAMP()");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_awb_%' AND option_name NOT IN (SELECT CONCAT('_transient_', SUBSTRING(option_name, 19)) FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_awb_%')");
}

/**
 * Schedule cleanup
 */
function awb_schedule_cleanup() {
    if (!wp_next_scheduled('awb_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'awb_daily_cleanup');
    }
}

/**
 * Cleanup hook
 */
add_action('awb_daily_cleanup', 'awb_cleanup_cache');

/**
 * Format file size
 */
function awb_format_bytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

/**
 * Check if demo directory is writable
 */
function awb_check_demo_directory() {
    $upload_dir = wp_upload_dir();
    $demo_dir = $upload_dir['basedir'] . '/ai-web-builder-demos/';
    
    if (!file_exists($demo_dir)) {
        return wp_mkdir_p($demo_dir);
    }
    
    return is_writable($demo_dir);
}

/**
 * Get plugin status information
 */
function awb_get_plugin_status() {
    $status = array(
        'api_keys_configured' => awb_validate_api_keys(),
        'demo_directory_writable' => awb_check_demo_directory(),
        'caching_enabled' => get_option('awb_enable_caching') === '1',
        'cache_duration' => get_option('awb_cache_duration', '3600'),
        'default_model' => get_option('awb_default_model', 'openai')
    );
    
    return $status;
}

/**
 * Generate unique demo ID
 */
function awb_generate_demo_id() {
    return 'demo_' . time() . '_' . wp_generate_password(8, false);
}

/**
 * Clean up old demo files
 */
function awb_cleanup_old_demos($days = 30) {
    $upload_dir = wp_upload_dir();
    $demo_dir = $upload_dir['basedir'] . '/ai-web-builder-demos/';
    
    if (!is_dir($demo_dir)) {
        return;
    }
    
    $cutoff_time = time() - ($days * 24 * 60 * 60);
    $directories = glob($demo_dir . 'demo_*', GLOB_ONLYDIR);
    
    foreach ($directories as $dir) {
        if (filemtime($dir) < $cutoff_time) {
            awb_delete_directory($dir);
        }
    }
}

/**
 * Recursively delete directory
 */
function awb_delete_directory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($path) ? awb_delete_directory($path) : unlink($path);
    }
    
    return rmdir($dir);
}

/**
 * Get industry-specific suggestions
 */
function awb_get_industry_suggestions($industry) {
    $suggestions = array(
        'Technology & Software' => array(
            'colors' => array('#3b82f6', '#8b5cf6', '#06b6d4'),
            'features' => array('API Integration', 'Advanced Analytics', 'Custom Database'),
            'keywords' => array('software development', 'tech solutions', 'digital transformation')
        ),
        'Healthcare & Medical' => array(
            'colors' => array('#10b981', '#3b82f6', '#f59e0b'),
            'features' => array('Online Booking', 'Patient Portal', 'HIPAA Compliance'),
            'keywords' => array('medical services', 'healthcare provider', 'patient care')
        ),
        'E-commerce & Retail' => array(
            'colors' => array('#f59e0b', '#ef4444', '#8b5cf6'),
            'features' => array('E-commerce Store', 'Payment Gateway', 'Inventory Management'),
            'keywords' => array('online store', 'retail products', 'shopping')
        ),
        'Restaurant & Food' => array(
            'colors' => array('#f59e0b', '#ef4444', '#84cc16'),
            'features' => array('Online Booking', 'Menu Display', 'Order System'),
            'keywords' => array('restaurant', 'food delivery', 'dining')
        )
    );
    
    return $suggestions[$industry] ?? array(
        'colors' => array('#3b82f6', '#8b5cf6', '#f59e0b'),
        'features' => array('Contact Forms', 'Mobile Responsive', 'SEO Optimization'),
        'keywords' => array('business services', 'professional solutions')
    );
}

/**
 * Validate concept data
 */
function awb_validate_concept_data($data) {
    $required_fields = array('concept', 'colorScheme', 'features');
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            return false;
        }
    }
    
    // Validate concept structure
    if (!isset($data['concept']['title']) || !isset($data['concept']['description'])) {
        return false;
    }
    
    return true;
}

/**
 * Estimate API cost
 */
function awb_estimate_api_cost($tokens, $model = 'gpt-4') {
    $costs_per_1k_tokens = array(
        'gpt-4' => 0.03,
        'gpt-3.5-turbo' => 0.002,
        'claude-3.5-sonnet' => 0.003
    );
    
    $cost_per_token = $costs_per_1k_tokens[$model] ?? 0.03;
    return ($tokens / 1000) * $cost_per_token;
}

/**
 * Rate limit check
 */
function awb_check_rate_limit($user_ip = null) {
    if (!$user_ip) {
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    $rate_limit_key = 'awb_rate_limit_' . md5($user_ip);
    $current_count = get_transient($rate_limit_key) ?: 0;
    
    // Allow 10 requests per hour per IP
    if ($current_count >= 10) {
        return false;
    }
    
    set_transient($rate_limit_key, $current_count + 1, HOUR_IN_SECONDS);
    return true;
}

/**
 * Generate concept hash for caching
 */
function awb_generate_concept_hash($form_data) {
    // Remove timestamp-sensitive data for consistent hashing
    $cache_data = $form_data;
    unset($cache_data['timestamp']);
    
    return md5(serialize($cache_data));
}

/**
 * Format timeline estimate
 */
function awb_format_timeline($features_count, $complexity = 'medium') {
    $base_weeks = array(
        'simple' => 2,
        'medium' => 4,
        'complex' => 8
    );
    
    $weeks = $base_weeks[$complexity] + ($features_count * 0.5);
    $weeks = max(2, ceil($weeks)); // Minimum 2 weeks
    
    if ($weeks <= 4) {
        return $weeks . ' weeks';
    } else {
        $months = ceil($weeks / 4);
        return $months . ' month' . ($months > 1 ? 's' : '');
    }
}

/**
 * Get responsive breakpoints
 */
function awb_get_responsive_breakpoints() {
    return array(
        'mobile' => 768,
        'tablet' => 1024,
        'desktop' => 1200,
        'large' => 1440
    );
}

/**
 * Admin notice for missing API keys
 */
function awb_admin_notice_api_keys() {
    if (!awb_validate_api_keys()) {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>AI Web Builder:</strong> Please configure your API keys in the <a href="' . admin_url('admin.php?page=ai-web-builder-settings') . '">plugin settings</a> to enable AI concept generation.</p>
        </div>';
    }
}
add_action('admin_notices', 'awb_admin_notice_api_keys');

/**
 * Schedule demo cleanup on activation
 */
function awb_schedule_demo_cleanup() {
    if (!wp_next_scheduled('awb_demo_cleanup')) {
        wp_schedule_event(time(), 'daily', 'awb_demo_cleanup');
    }
}
add_action('awb_demo_cleanup', function() {
    awb_cleanup_old_demos(7); // Clean up demos older than 7 days
});

/**
 * Unschedule events on deactivation
 */
function awb_unschedule_events() {
    wp_clear_scheduled_hook('awb_daily_cleanup');
    wp_clear_scheduled_hook('awb_demo_cleanup');
}
register_deactivation_hook(__FILE__, 'awb_unschedule_events');