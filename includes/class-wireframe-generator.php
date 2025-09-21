<?php
/**
 * Wireframe Generator Class
 * Creates detailed wireframe structures and visual layouts
 */

if (!defined('ABSPATH')) {
    exit;
}

class AWB_Wireframe_Generator {
    
    private $section_templates;
    private $layout_patterns;
    
    public function __construct() {
        $this->section_templates = $this->get_section_templates();
        $this->layout_patterns = $this->get_layout_patterns();
    }
    
    public function generate_wireframe($form_data, $concept_data) {
        $website_type = $form_data['websiteType'] ?? 'Business Website';
        $industry = $form_data['industry'] ?? '';
        $features = $form_data['features'] ?? array();
        
        // Generate homepage wireframe
        $homepage_wireframe = $this->generate_homepage_wireframe($website_type, $industry, $features);
        
        // Generate additional page wireframes
        $additional_pages = $this->generate_additional_pages($website_type, $features);
        
        // Create responsive variations
        $responsive_wireframes = $this->generate_responsive_wireframes($homepage_wireframe);
        
        return array(
            'homepage' => $homepage_wireframe,
            'additional_pages' => $additional_pages,
            'responsive' => $responsive_wireframes,
            'layout_rationale' => $this->explain_layout_choices($website_type, $industry),
            'interaction_patterns' => $this->define_interaction_patterns($features),
            'component_library' => $this->generate_component_library($features)
        );
    }
    
    private function generate_homepage_wireframe($website_type, $industry, $features) {
        $sections = array();
        
        // Header section (always present)
        $sections[] = array(
            'name' => 'Header',
            'type' => 'navigation',
            'height' => 80,
            'elements' => array(
                'logo' => array('position' => 'left', 'width' => '20%'),
                'navigation' => array('position' => 'center', 'width' => '60%'),
                'cta_button' => array('position' => 'right', 'width' => '20%')
            ),
            'color' => '#1e293b',
            'priority' => 'critical',
            'mobile_behavior' => 'hamburger_menu'
        );
        
        // Hero section (customized by type)
        $hero_config = $this->get_hero_configuration($website_type, $industry);
        $sections[] = array_merge($hero_config, array(
            'name' => 'Hero Section',
            'type' => 'hero',
            'height' => 600,
            'color' => '#3b82f6',
            'priority' => 'critical'
        ));
        
        // Value proposition section
        if (in_array($website_type, ['Business Website', 'SaaS Platform', 'Professional Services'])) {
            $sections[] = array(
                'name' => 'Value Propositions',
                'type' => 'feature_grid',
                'height' => 400,
                'elements' => array(
                    'title' => array('text' => 'Why Choose Us'),
                    'grid' => array('columns' => 3, 'items' => 3)
                ),
                'color' => '#f8fafc',
                'priority' => 'high'
            );
        }
        
        // Services/Products section
        $services_config = $this->get_services_configuration($website_type, $features);
        if ($services_config) {
            $sections[] = array_merge($services_config, array(
                'color' => '#ffffff',
                'priority' => 'high'
            ));
        }
        
        // Social proof section
        if (in_array('Testimonials', $features) || $website_type !== 'Portfolio Site') {
            $sections[] = array(
                'name' => 'Social Proof',
                'type' => 'testimonials',
                'height' => 350,
                'elements' => array(
                    'testimonials' => array('count' => 3, 'layout' => 'carousel'),
                    'logos' => array('count' => 6, 'layout' => 'grid')
                ),
                'color' => '#f1f5f9',
                'priority' => 'medium'
            );
        }
        
        // CTA section
        $cta_config = $this->get_cta_configuration($website_type, $form_data['businessGoal'] ?? '');
        $sections[] = array_merge($cta_config, array(
            'color' => '#6366f1',
            'priority' => 'high'
        ));
        
        // Footer
        $sections[] = array(
            'name' => 'Footer',
            'type' => 'footer',
            'height' => 300,
            'elements' => array(
                'company_info' => array('width' => '25%'),
                'quick_links' => array('width' => '25%'),
                'contact_info' => array('width' => '25%'),
                'social_media' => array('width' => '25%')
            ),
            'color' => '#0f172a',
            'priority' => 'medium'
        );
        
        return array(
            'sections' => $sections,
            'total_height' => array_sum(array_column($sections, 'height')),
            'layout_type' => 'single_column',
            'max_width' => 1200,
            'responsive_breakpoints' => array(
                'mobile' => 768,
                'tablet' => 1024,
                'desktop' => 1200
            )
        );
    }
    
    private function get_hero_configuration($website_type, $industry) {
        $configs = array(
            'E-commerce Store' => array(
                'layout' => 'product_showcase',
                'elements' => array(
                    'headline' => array('position' => 'left', 'width' => '50%'),
                    'product_image' => array('position' => 'right', 'width' => '50%'),
                    'search_bar' => array('position' => 'center', 'width' => '80%'),
                    'category_nav' => array('position' => 'bottom', 'width' => '100%')
                )
            ),
            'SaaS Platform' => array(
                'layout' => 'demo_focused',
                'elements' => array(
                    'headline' => array('position' => 'center', 'width' => '80%'),
                    'subheadline' => array('position' => 'center', 'width' => '60%'),
                    'demo_video' => array('position' => 'center', 'width' => '70%'),
                    'trial_button' => array('position' => 'center', 'width' => '30%')
                )
            ),
            'Portfolio Site' => array(
                'layout' => 'visual_showcase',
                'elements' => array(
                    'name_title' => array('position' => 'left', 'width' => '40%'),
                    'featured_work' => array('position' => 'right', 'width' => '60%'),
                    'skills_tags' => array('position' => 'bottom', 'width' => '100%')
                )
            ),
            'Restaurant & Food' => array(
                'layout' => 'appetite_appeal',
                'elements' => array(
                    'restaurant_name' => array('position' => 'center', 'width' => '60%'),
                    'hero_image' => array('position' => 'background', 'width' => '100%'),
                    'reservation_button' => array('position' => 'center', 'width' => '40%'),
                    'hours_location' => array('position' => 'bottom', 'width' => '100%')
                )
            )
        );
        
        return $configs[$website_type] ?? array(
            'layout' => 'standard_business',
            'elements' => array(
                'headline' => array('position' => 'left', 'width' => '50%'),
                'hero_image' => array('position' => 'right', 'width' => '50%'),
                'cta_buttons' => array('position' => 'left', 'width' => '50%')
            )
        );
    }
    
    private function get_services_configuration($website_type, $features) {
        if ($website_type === 'E-commerce Store') {
            return array(
                'name' => 'Featured Products',
                'type' => 'product_grid',
                'height' => 500,
                'elements' => array(
                    'product_cards' => array('columns' => 4, 'rows' => 2),
                    'filter_sidebar' => array('width' => '20%'),
                    'sort_options' => array('position' => 'top')
                )
            );
        }
        
        if ($website_type === 'Portfolio Site') {
            return array(
                'name' => 'Portfolio Gallery',
                'type' => 'portfolio_grid',
                'height' => 600,
                'elements' => array(
                    'project_cards' => array('columns' => 3, 'masonry' => true),
                    'category_filter' => array('position' => 'top'),
                    'view_all_button' => array('position' => 'bottom')
                )
            );
        }
        
        return array(
            'name' => 'Our Services',
            'type' => 'services_grid',
            'height' => 450,
            'elements' => array(
                'service_cards' => array('columns' => 3, 'rows' => 1),
                'service_icons' => array('style' => 'outlined'),
                'learn_more_links' => array('style' => 'text_link')
            )
        );
    }
    
    private function get_cta_configuration($website_type, $business_goal) {
        $configs = array(
            'Generate Leads' => array(
                'name' => 'Lead Capture',
                'type' => 'lead_form',
                'height' => 400,
                'elements' => array(
                    'headline' => array('text' => 'Get Your Free Consultation'),
                    'form' => array('fields' => array('name', 'email', 'phone', 'message')),
                    'benefits_list' => array('items' => 3)
                )
            ),
            'Sell Products Online' => array(
                'name' => 'Shop Now',
                'type' => 'product_cta',
                'height' => 300,
                'elements' => array(
                    'headline' => array('text' => 'Start Shopping Today'),
                    'featured_categories' => array('count' => 4),
                    'promo_banner' => array('text' => 'Free shipping on orders over $50')
                )
            ),
            'Accept Bookings/Appointments' => array(
                'name' => 'Book Appointment',
                'type' => 'booking_widget',
                'height' => 350,
                'elements' => array(
                    'calendar_widget' => array('style' => 'inline'),
                    'service_selector' => array('type' => 'dropdown'),
                    'contact_info' => array('phone', 'address')
                )
            )
        );
        
        return $configs[$business_goal] ?? array(
            'name' => 'Get Started',
            'type' => 'general_cta',
            'height' => 250,
            'elements' => array(
                'headline' => array('text' => 'Ready to Get Started?'),
                'cta_button' => array('text' => 'Contact Us Today'),
                'contact_options' => array('phone', 'email', 'form')
            )
        );
    }
    
    private function generate_additional_pages($website_type, $features) {
        $pages = array();
        
        // About page (universal)
        $pages['about'] = array(
            'name' => 'About Us',
            'sections' => array(
                array('name' => 'Company Story', 'height' => 400),
                array('name' => 'Team Members', 'height' => 500),
                array('name' => 'Mission & Values', 'height' => 300),
                array('name' => 'Contact CTA', 'height' => 200)
            ),
            'priority' => 'high'
        );
        
        // Services page
        if ($website_type !== 'E-commerce Store') {
            $pages['services'] = array(
                'name' => 'Services',
                'sections' => array(
                    array('name' => 'Services Overview', 'height' => 300),
                    array('name' => 'Service Details', 'height' => 600),
                    array('name' => 'Process Timeline', 'height' => 400),
                    array('name' => 'Pricing Table', 'height' => 500)
                ),
                'priority' => 'high'
            );
        }
        
        // Product pages for e-commerce
        if ($website_type === 'E-commerce Store' || in_array('E-commerce Store', $features)) {
            $pages['product_detail'] = array(
                'name' => 'Product Detail',
                'sections' => array(
                    array('name' => 'Product Gallery', 'height' => 500),
                    array('name' => 'Product Info', 'height' => 400),
                    array('name' => 'Reviews & Ratings', 'height' => 350),
                    array('name' => 'Related Products', 'height' => 300)
                ),
                'priority' => 'critical'
            );
        }
        
        // Contact page
        $pages['contact'] = array(
            'name' => 'Contact',
            'sections' => array(
                array('name' => 'Contact Form', 'height' => 400),
                array('name' => 'Location Map', 'height' => 300),
                array('name' => 'Contact Information', 'height' => 200)
            ),
            'priority' => 'medium'
        );
        
        // Blog page
        if (in_array('Blog System', $features)) {
            $pages['blog'] = array(
                'name' => 'Blog',
                'sections' => array(
                    array('name' => 'Featured Posts', 'height' => 300),
                    array('name' => 'Post Grid', 'height' => 600),
                    array('name' => 'Categories Sidebar', 'height' => 400)
                ),
                'priority' => 'medium'
            );
        }
        
        return $pages;
    }
    
    private function generate_responsive_wireframes($homepage_wireframe) {
        $mobile_sections = array();
        $tablet_sections = array();
        
        foreach ($homepage_wireframe['sections'] as $section) {
            // Mobile adaptations
            $mobile_section = $section;
            $mobile_section['height'] = $this->calculate_mobile_height($section);
            $mobile_section['layout'] = 'stacked';
            
            // Tablet adaptations  
            $tablet_section = $section;
            $tablet_section['height'] = $section['height'] * 0.8;
            
            $mobile_sections[] = $mobile_section;
            $tablet_sections[] = $tablet_section;
        }
        
        return array(
            'mobile' => array(
                'sections' => $mobile_sections,
                'max_width' => 375,
                'layout_type' => 'single_column'
            ),
            'tablet' => array(
                'sections' => $tablet_sections,
                'max_width' => 768,
                'layout_type' => 'hybrid'
            )
        );
    }
    
    private function calculate_mobile_height($section) {
        $base_height = $section['height'];
        
        // Adjust based on section type
        switch ($section['type']) {
            case 'hero':
                return $base_height * 0.7; // Reduce hero height on mobile
            case 'feature_grid':
                return $base_height * 1.5; // Stack features vertically
            case 'testimonials':
                return $base_height * 0.8; // Single column testimonials
            default:
                return $base_height;
        }
    }
    
    private function explain_layout_choices($website_type, $industry) {
        $explanations = array(
            'header_design' => 'Clean navigation with prominent CTA button for easy user action',
            'hero_section' => 'Large hero area to immediately communicate value proposition',
            'content_hierarchy' => 'Logical flow from problem to solution to social proof to action',
            'white_space' => 'Generous spacing to improve readability and focus attention',
            'mobile_first' => 'Responsive design ensuring optimal experience on all devices'
        );
        
        // Add industry-specific explanations
        if ($industry === 'E-commerce & Retail') {
            $explanations['product_focus'] = 'Product imagery and easy navigation to shopping features';
        }
        
        if ($industry === 'Healthcare & Medical') {
            $explanations['trust_elements'] = 'Prominent trust signals and professional appearance';
        }
        
        return $explanations;
    }
    
    private function define_interaction_patterns($features) {
        $patterns = array(
            'navigation' => array(
                'desktop' => 'Horizontal menu with hover states',
                'mobile' => 'Hamburger menu with slide-out navigation'
            ),
            'buttons' => array(
                'primary' => 'Solid background with hover animation',
                'secondary' => 'Outlined style with fill animation'
            ),
            'forms' => array(
                'validation' => 'Real-time validation with clear error messages',
                'submission' => 'Loading states and success confirmations'
            )
        );
        
        // Add feature-specific patterns
        if (in_array('E-commerce Store', $features)) {
            $patterns['shopping'] = array(
                'product_cards' => 'Hover overlay with quick action buttons',
                'cart' => 'Slide-out cart with quantity controls',
                'checkout' => 'Multi-step process with progress indicator'
            );
        }
        
        if (in_array('Live Chat', $features)) {
            $patterns['chat'] = array(
                'trigger' => 'Floating button in bottom right corner',
                'window' => 'Slide-up chat interface with minimize option'
            );
        }
        
        return $patterns;
    }
    
    private function generate_component_library($features) {
        $components = array(
            'buttons' => array(
                'primary_button' => array('style' => 'solid', 'sizes' => array('sm', 'md', 'lg')),
                'secondary_button' => array('style' => 'outline', 'sizes' => array('sm', 'md', 'lg')),
                'text_link' => array('style' => 'underline', 'states' => array('default', 'hover'))
            ),
            'forms' => array(
                'input_field' => array('types' => array('text', 'email', 'tel', 'textarea')),
                'select_dropdown' => array('styles' => array('default', 'searchable')),
                'checkbox' => array('styles' => array('default', 'switch')),
                'radio_button' => array('styles' => array('default', 'card'))
            ),
            'cards' => array(
                'content_card' => array('variants' => array('text', 'image', 'mixed')),
                'product_card' => array('elements' => array('image', 'title', 'price', 'rating')),
                'testimonial_card' => array('elements' => array('quote', 'author', 'avatar', 'rating'))
            ),
            'navigation' => array(
                'main_menu' => array('orientations' => array('horizontal', 'vertical')),
                'breadcrumbs' => array('separator' => 'chevron'),
                'pagination' => array('styles' => array('numbered', 'prev_next'))
            )
        );
        
        // Add feature-specific components
        if (in_array('Online Booking', $features)) {
            $components['booking'] = array(
                'calendar_widget' => array('views' => array('month', 'week', 'day')),
                'time_picker' => array('intervals' => array('15min', '30min', '60min')),
                'booking_form' => array('steps' => array('service', 'time', 'details', 'confirmation'))
            );
        }
        
        return $components;
    }
    
    private function get_section_templates() {
        return array(
            'hero' => array(
                'standard' => 'Split layout with text left, image right',
                'centered' => 'Centered text with background image',
                'minimal' => 'Large headline with simple CTA'
            ),
            'features' => array(
                'grid' => '3-column grid with icons and descriptions',
                'alternating' => 'Alternating image/text sections',
                'tabbed' => 'Tabbed interface with feature details'
            ),
            'testimonials' => array(
                'carousel' => 'Sliding testimonial cards',
                'grid' => 'Static grid of testimonial cards',
                'featured' => 'Single large testimonial with smaller ones'
            )
        );
    }
    
    private function get_layout_patterns() {
        return array(
            'Business Website' => array('hero', 'features', 'services', 'testimonials', 'contact'),
            'E-commerce Store' => array('hero', 'categories', 'featured_products', 'testimonials', 'newsletter'),
            'Portfolio Site' => array('hero', 'portfolio_grid', 'about', 'services', 'contact'),
            'SaaS Platform' => array('hero', 'features', 'pricing', 'testimonials', 'trial_signup')
        );
    }
}