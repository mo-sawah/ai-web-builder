<?php
/**
 * Admin Settings Page
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>AI Web Builder Settings</h1>
    
    <?php settings_errors(); ?>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('awb_settings');
        do_settings_sections('awb_settings');
        ?>
        
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="awb_openai_api_key">OpenAI API Key</label>
                    </th>
                    <td>
                        <input type="password" id="awb_openai_api_key" name="awb_openai_api_key" 
                               value="<?php echo esc_attr(get_option('awb_openai_api_key')); ?>" 
                               class="regular-text" />
                        <p class="description">
                            Your OpenAI API key for GPT models. Get it from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>.
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="awb_openrouter_api_key">OpenRouter API Key</label>
                    </th>
                    <td>
                        <input type="password" id="awb_openrouter_api_key" name="awb_openrouter_api_key" 
                               value="<?php echo esc_attr(get_option('awb_openrouter_api_key')); ?>" 
                               class="regular-text" />
                        <p class="description">
                            Your OpenRouter API key for accessing multiple LLM models. Get it from <a href="https://openrouter.ai/keys" target="_blank">OpenRouter</a>.
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="awb_default_model">Default AI Model</label>
                    </th>
                    <td>
                        <select id="awb_default_model" name="awb_default_model">
                            <option value="openai" <?php selected(get_option('awb_default_model'), 'openai'); ?>>
                                OpenAI (Primary)
                            </option>
                            <option value="openrouter" <?php selected(get_option('awb_default_model'), 'openrouter'); ?>>
                                OpenRouter (Primary)
                            </option>
                        </select>
                        <p class="description">
                            Choose which AI service to try first. The plugin will fallback to the other if the primary fails.
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="awb_enable_caching">Enable Caching</label>
                    </th>
                    <td>
                        <input type="checkbox" id="awb_enable_caching" name="awb_enable_caching" value="1" 
                               <?php checked(get_option('awb_enable_caching'), '1'); ?> />
                        <label for="awb_enable_caching">Cache AI responses to improve performance and reduce API costs</label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="awb_cache_duration">Cache Duration (seconds)</label>
                    </th>
                    <td>
                        <input type="number" id="awb_cache_duration" name="awb_cache_duration" 
                               value="<?php echo esc_attr(get_option('awb_cache_duration', '3600')); ?>" 
                               min="300" max="86400" class="small-text" />
                        <p class="description">
                            How long to cache AI responses (300 = 5 minutes, 3600 = 1 hour, 86400 = 24 hours).
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="awb-admin-info">
        <h2>Usage Instructions</h2>
        <div class="awb-info-card">
            <h3>Shortcode Usage</h3>
            <p>Add the AI Web Builder form to any page or post using this shortcode:</p>
            <code>[ai_web_builder]</code>
            
            <h4>Shortcode Parameters:</h4>
            <ul>
                <li><code>style</code> - Form styling (default: "default")</li>
                <li><code>show_demo_button</code> - Show/hide demo generation button (default: "true")</li>
            </ul>
            
            <h4>Example with parameters:</h4>
            <code>[ai_web_builder style="minimal" show_demo_button="false"]</code>
        </div>
        
        <div class="awb-info-card">
            <h3>API Key Setup</h3>
            <ol>
                <li><strong>OpenAI:</strong> Visit <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a> and create an API key</li>
                <li><strong>OpenRouter:</strong> Visit <a href="https://openrouter.ai/keys" target="_blank">OpenRouter</a> and create an API key</li>
                <li>Enter both keys above for maximum reliability (fallback system)</li>
                <li>Enable caching to reduce API costs and improve performance</li>
            </ol>
        </div>
        
        <div class="awb-info-card">
            <h3>Features Overview</h3>
            <ul>
                <li><strong>AI Concept Generation:</strong> Creates detailed website concepts with business analysis</li>
                <li><strong>Visual Wireframes:</strong> Generates interactive wireframe previews</li>
                <li><strong>Cost Estimation:</strong> Provides realistic project cost breakdowns</li>
                <li><strong>Live Demo Generation:</strong> Creates functional HTML demos</li>
                <li><strong>Dual AI Support:</strong> OpenAI + OpenRouter for maximum reliability</li>
                <li><strong>Smart Caching:</strong> Reduces API costs and improves performance</li>
            </ul>
        </div>
    </div>
</div>

<style>
.awb-admin-info {
    margin-top: 40px;
}

.awb-info-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.awb-info-card h3 {
    margin-top: 0;
    color: #23282d;
}

.awb-info-card h4 {
    margin-top: 20px;
    margin-bottom: 10px;
    color: #1d2327;
}

.awb-info-card code {
    background: #f6f7f7;
    border: 1px solid #ddd;
    padding: 4px 8px;
    border-radius: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
}

.awb-info-card ul,
.awb-info-card ol {
    margin-left: 20px;
}

.awb-info-card li {
    margin-bottom: 8px;
}
</style>