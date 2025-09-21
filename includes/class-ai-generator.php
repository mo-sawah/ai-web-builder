<?php
/**
 * AI Generator Class
 * Handles AI API calls and concept generation
 */

if (!defined('ABSPATH')) {
    exit;
}

class AWB_AI_Generator {
    
    private $openai_api_key;
    private $openrouter_api_key;
    private $default_model;
    
    public function __construct() {
        $this->openai_api_key = get_option('awb_openai_api_key', '');
        $this->openrouter_api_key = get_option('awb_openrouter_api_key', '');
        $this->default_model = get_option('awb_default_model', 'openai');
    }
    
    public function generate_concept($form_data) {
        try {
            // Validate required fields
            if (empty($form_data['businessType']) || empty($form_data['industry'])) {
                return array('success' => false, 'error' => 'Business name and industry are required');
            }
            
            // Check cache first
            if (get_option('awb_enable_caching') === '1') {
                $cache_key = 'awb_concept_' . md5(serialize($form_data));
                $cached_result = get_transient($cache_key);
                if ($cached_result) {
                    return array('success' => true, 'data' => $cached_result, 'cached' => true);
                }
            }
            
            // Generate enhanced prompt
            $prompt = $this->build_enhanced_prompt($form_data);
            
            // Try primary model first, fallback to secondary
            $concept_data = $this->call_ai_api($prompt);
            
            if (!$concept_data) {
                return array('success' => false, 'error' => 'Failed to generate concept with available AI models');
            }
            
            // Generate wireframe data
            $wireframe_generator = new AWB_Wireframe_Generator();
            $wireframe_data = $wireframe_generator->generate_wireframe($form_data, $concept_data);
            
            // Combine all data
            $final_concept = array_merge($concept_data, array(
                'wireframe' => $wireframe_data,
                'technical_specs' => $this->generate_technical_specs($form_data),
                'seo_analysis' => $this->generate_seo_analysis($form_data),
                'performance_metrics' => $this->generate_performance_metrics($form_data),
                'cost_breakdown' => $this->generate_cost_breakdown($form_data)
            ));
            
            // Cache the result
            if (get_option('awb_enable_caching') === '1') {
                $cache_duration = intval(get_option('awb_cache_duration', '3600'));
                set_transient($cache_key, $final_concept, $cache_duration);
            }
            
            // Save to database
            $this->save_concept_to_db($form_data, $final_concept);
            
            return array('success' => true, 'data' => $final_concept);
            
        } catch (Exception $e) {
            error_log('AWB Error: ' . $e->getMessage());
            return array('success' => false, 'error' => 'An unexpected error occurred');
        }
    }
    
    private function build_enhanced_prompt($form_data) {
        $business_context = "You are an expert web development consultant creating a comprehensive website concept.";
        
        $client_details = "CLIENT REQUIREMENTS:\n";
        $client_details .= "- Business: {$form_data['businessType']}\n";
        $client_details .= "- Industry: {$form_data['industry']}\n";
        $client_details .= "- Company Size: " . ($form_data['companySize'] ?? 'Not specified') . "\n";
        $client_details .= "- Target Audience: " . ($form_data['targetAudience'] ?? 'General') . "\n";
        $client_details .= "- Primary Goal: " . ($form_data['businessGoal'] ?? 'Brand awareness') . "\n";
        $client_details .= "- Budget: " . ($form_data['budget'] ?? 'Not specified') . "\n";
        $client_details .= "- Website Type: " . ($form_data['websiteType'] ?? 'Business Website') . "\n";
        $client_details .= "- Design Style: " . ($form_data['designStyle'] ?? 'Modern & Clean') . "\n";
        $client_details .= "- Timeline: " . ($form_data['timeline'] ?? 'Flexible') . "\n";
        
        if (!empty($form_data['features']) && is_array($form_data['features'])) {
            $client_details .= "- Required Features: " . implode(', ', $form_data['features']) . "\n";
        }
        
        if (!empty($form_data['businessDescription'])) {
            $client_details .= "- Business Description: {$form_data['businessDescription']}\n";
        }
        
        $json_structure = $this->get_json_structure();
        
        return $business_context . "\n\n" . $client_details . "\n\n" . $json_structure;
    }
    
    private function get_json_structure() {
        return 'RESPOND WITH ONLY VALID JSON IN THIS EXACT STRUCTURE:
{
  "concept": {
    "title": "[Use actual business name from input]",
    "tagline": "[Industry-specific, compelling tagline max 60 characters]",
    "description": "[Detailed 2-3 sentence description matching goals and industry]",
    "estimatedCost": "[Realistic cost range based on budget and features]",
    "timeline": "[Appropriate timeline in weeks/months]",
    "pages": "[Recommended number of pages]",
    "sections": [
      "[5-8 specific sections relevant to the business type]"
    ],
    "value_propositions": [
      "[3-5 key value propositions for this business]"
    ],
    "target_actions": [
      "[3-4 specific calls-to-action users should take]"
    ]
  },
  "colorScheme": {
    "primary": "[Hex color matching industry/style]",
    "secondary": "[Complementary hex color]",
    "accent": "[Accent hex color]",
    "background": "[Background hex color]",
    "text": "[Text hex color]",
    "success": "[Success state color]",
    "warning": "[Warning state color]"
  },
  "typography": {
    "headings": "[Professional font family for headings]",
    "body": "[Readable font family for body text]",
    "sizes": {
      "h1": "[Size in px]",
      "h2": "[Size in px]",
      "body": "[Size in px]"
    }
  },
  "features": {
    "essential": [
      "[3-5 essential features for this business type]"
    ],
    "recommended": [
      "[3-5 recommended features based on goals]"
    ],
    "advanced": [
      "[2-4 advanced features for growth]"
    ]
  },
  "content_strategy": {
    "homepage_sections": [
      {
        "name": "[Section name]",
        "purpose": "[What this section achieves]",
        "content_type": "[Text/Image/Video/Form etc.]"
      }
    ],
    "additional_pages": [
      {
        "name": "[Page name]",
        "purpose": "[Why this page is needed]",
        "priority": "[High/Medium/Low]"
      }
    ]
  },
  "seoScore": "[85-98 based on complexity]",
  "performanceScore": "[80-95 based on features]",
  "accessibilityScore": "[90-99 based on standards]",
  "mobileScore": "[90-99 mobile optimization score]"
}';
    }
    
    private function call_ai_api($prompt) {
        // Try OpenRouter first if available
        if (!empty($this->openrouter_api_key)) {
            $result = $this->call_openrouter_api($prompt);
            if ($result) return $result;
        }
        
        // Fallback to OpenAI
        if (!empty($this->openai_api_key)) {
            $result = $this->call_openai_api($prompt);
            if ($result) return $result;
        }
        
        return false;
    }
    
    private function call_openrouter_api($prompt) {
        $data = array(
            'model' => 'anthropic/claude-3.5-sonnet',
            'messages' => array(
                array('role' => 'system', 'content' => 'You are a professional web development consultant. Always respond with valid JSON only.'),
                array('role' => 'user', 'content' => $prompt)
            ),
            'max_tokens' => 3000,
            'temperature' => 0.3
        );
        
        $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->openrouter_api_key,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => home_url(),
                'X-Title' => 'AI Web Builder Plugin'
            ),
            'body' => json_encode($data),
            'timeout' => 60
        ));
        
        return $this->process_api_response($response);
    }
    
    private function call_openai_api($prompt) {
        $data = array(
            'model' => 'gpt-4',
            'messages' => array(
                array('role' => 'system', 'content' => 'You are a professional web development consultant. Always respond with valid JSON only.'),
                array('role' => 'user', 'content' => $prompt)
            ),
            'max_tokens' => 3000,
            'temperature' => 0.3
        );
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->openai_api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data),
            'timeout' => 60
        ));
        
        return $this->process_api_response($response);
    }
    
    private function process_api_response($response) {
        if (is_wp_error($response)) {
            error_log('AWB API Error: ' . $response->get_error_message());
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!$data || !isset($data['choices'][0]['message']['content'])) {
            error_log('AWB Invalid API Response: ' . $body);
            return false;
        }
        
        $ai_response = trim($data['choices'][0]['message']['content']);
        
        // Clean up response
        $ai_response = preg_replace('/```json\s*/', '', $ai_response);
        $ai_response = preg_replace('/```\s*$/', '', $ai_response);
        $ai_response = trim($ai_response);
        
        $concept_data = json_decode($ai_response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('AWB JSON Parse Error: ' . json_last_error_msg());
            return false;
        }
        
        return $concept_data;
    }
    
    private function generate_technical_specs($form_data) {
        $features = $form_data['features'] ?? array();
        
        $hosting_requirements = array(
            'min_storage' => '5GB',
            'bandwidth' => '100GB/month',
            'ssl_required' => true,
            'php_version' => '8.0+',
            'mysql_version' => '5.7+'
        );
        
        // Adjust based on features
        if (in_array('E-commerce Store', $features)) {
            $hosting_requirements['min_storage'] = '20GB';
            $hosting_requirements['bandwidth'] = '500GB/month';
        }
        
        if (in_array('Advanced Analytics', $features) || in_array('Custom Database', $features)) {
            $hosting_requirements['min_storage'] = '50GB';
        }
        
        return array(
            'hosting' => $hosting_requirements,
            'recommended_cms' => 'WordPress',
            'development_time' => $this->calculate_development_time($features),
            'maintenance_level' => $this->calculate_maintenance_level($features)
        );
    }
    
    private function generate_seo_analysis($form_data) {
        $industry = $form_data['industry'] ?? '';
        $business_goal = $form_data['businessGoal'] ?? '';
        
        return array(
            'keyword_opportunities' => $this->get_keyword_suggestions($industry),
            'local_seo_potential' => in_array($business_goal, ['Generate Leads', 'Accept Bookings/Appointments']) ? 'High' : 'Medium',
            'content_recommendations' => array(
                'blog_topics' => $this->get_blog_topics($industry),
                'page_optimization' => array(
                    'title_tags' => 'Optimize for primary keywords',
                    'meta_descriptions' => 'Include call-to-action and value proposition',
                    'headings' => 'Use semantic H1-H6 structure',
                    'images' => 'Add descriptive alt text'
                )
            ),
            'technical_seo' => array(
                'page_speed' => 'Optimize images and enable caching',
                'mobile_friendly' => 'Implement responsive design',
                'schema_markup' => 'Add business and product schemas',
                'sitemap' => 'Generate and submit XML sitemap'
            )
        );
    }
    
    private function generate_performance_metrics($form_data) {
        $features = $form_data['features'] ?? array();
        $complexity_score = count($features) * 2;
        
        return array(
            'estimated_load_time' => $complexity_score > 20 ? '2-3 seconds' : '1-2 seconds',
            'mobile_performance' => 85 + (10 - min($complexity_score / 2, 10)),
            'optimization_priorities' => array(
                'Image compression and optimization',
                'CSS and JavaScript minification',
                'Browser caching implementation',
                'CDN setup for static assets'
            ),
            'monitoring_recommendations' => array(
                'Google PageSpeed Insights',
                'GTmetrix performance monitoring',
                'Core Web Vitals tracking',
                'Uptime monitoring'
            )
        );
    }
    
    private function generate_cost_breakdown($form_data) {
        $features = $form_data['features'] ?? array();
        $budget_range = $form_data['budget'] ?? '';
        $website_type = $form_data['websiteType'] ?? 'Business Website';
        
        $base_costs = array(
            'design' => 1500,
            'development' => 2500,
            'content' => 800,
            'testing' => 500,
            'deployment' => 300
        );
        
        // Adjust based on website type
        $multipliers = array(
            'E-commerce Store' => 2.0,
            'SaaS Platform' => 3.0,
            'Membership/Community' => 2.5,
            'Business Website' => 1.0,
            'Portfolio Site' => 0.7,
            'Landing Page' => 0.4
        );
        
        $multiplier = $multipliers[$website_type] ?? 1.0;
        
        // Feature-based additions
        $feature_costs = array(
            'E-commerce Store' => 2000,
            'Online Booking' => 800,
            'Payment Gateway' => 500,
            'CRM Integration' => 1200,
            'AI Chatbot' => 1500,
            'Custom Database' => 2500,
            'API Integration' => 1000,
            'Multi-language' => 1500
        );
        
        $additional_cost = 0;
        foreach ($features as $feature) {
            if (isset($feature_costs[$feature])) {
                $additional_cost += $feature_costs[$feature];
            }
        }
        
        foreach ($base_costs as $key => $cost) {
            $base_costs[$key] = round($cost * $multiplier);
        }
        
        $base_costs['features'] = $additional_cost;
        $total = array_sum($base_costs);
        
        return array(
            'breakdown' => $base_costs,
            'total_estimated' => $total,
            'range' => '$' . number_format($total * 0.8) . ' - $' . number_format($total * 1.2),
            'payment_schedule' => array(
                'deposit' => '30% upfront',
                'milestone_1' => '30% at design approval',
                'milestone_2' => '30% at development completion',
                'final' => '10% at launch'
            )
        );
    }
    
    // Helper methods
    private function calculate_development_time($features) {
        $base_weeks = 4;
        $feature_weeks = count($features) * 0.5;
        return ceil($base_weeks + $feature_weeks) . ' weeks';
    }
    
    private function calculate_maintenance_level($features) {
        $complex_features = array('E-commerce Store', 'CRM Integration', 'Custom Database', 'API Integration');
        $has_complex = array_intersect($features, $complex_features);
        
        if (count($has_complex) > 2) return 'High';
        if (count($has_complex) > 0) return 'Medium';
        return 'Low';
    }
    
    private function get_keyword_suggestions($industry) {
        $suggestions = array(
            'Technology & Software' => array('software development', 'tech solutions', 'digital transformation'),
            'Healthcare & Medical' => array('medical services', 'healthcare provider', 'patient care'),
            'E-commerce & Retail' => array('online store', 'retail products', 'shopping'),
            'Education & Training' => array('online courses', 'training programs', 'education services'),
            'Finance & Banking' => array('financial services', 'banking solutions', 'investment advice')
        );
        
        return $suggestions[$industry] ?? array('business services', 'professional solutions', 'expert consulting');
    }
    
    private function get_blog_topics($industry) {
        $topics = array(
            'Technology & Software' => array('Industry trends', 'Software tutorials', 'Tech news'),
            'Healthcare & Medical' => array('Health tips', 'Medical advances', 'Wellness guides'),
            'E-commerce & Retail' => array('Product spotlights', 'Shopping guides', 'Industry trends'),
        );
        
        return $topics[$industry] ?? array('Industry insights', 'Best practices', 'Case studies');
    }
    
    private function save_concept_to_db($form_data, $concept_data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'awb_generated_concepts';
        
        $wpdb->insert(
            $table_name,
            array(
                'form_data' => json_encode($form_data),
                'concept_data' => json_encode($concept_data),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s')
        );
    }
}