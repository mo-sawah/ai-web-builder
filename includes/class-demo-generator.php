<?php
/**
 * Demo Generator Class
 * Creates live HTML demos from generated concepts
 */

if (!defined('ABSPATH')) {
    exit;
}

class AWB_Demo_Generator {
    
    private $upload_dir;
    
    public function __construct() {
        $upload_dir = wp_upload_dir();
        $this->upload_dir = $upload_dir['basedir'] . '/ai-web-builder-demos/';
        
        // Create demo directory if it doesn't exist
        if (!file_exists($this->upload_dir)) {
            wp_mkdir_p($this->upload_dir);
        }
    }

    public function generate_demo_code($concept_data) {
        try {
            $prompt = $this->build_demo_prompt($concept_data);
            $response = $this->call_ai_api($prompt);
            
            if (!$response || !isset($response['html'])) {
                return false;
            }
            
            return $response;
            
        } catch (Exception $e) {
            error_log('AWB Demo Code Generation Error: ' . $e->getMessage());
            return false;
        }
    }

    private function build_demo_prompt($concept_data) {
        $business_type = $concept_data['form_data']['businessType'] ?? 'Business';
        $industry = $concept_data['form_data']['industry'] ?? 'Professional Services';
        $website_type = $concept_data['form_data']['websiteType'] ?? 'Business Website';
        $design_style = $concept_data['form_data']['designStyle'] ?? 'Modern & Clean';
        $features = $concept_data['form_data']['features'] ?? array();
        $colors = $concept_data['colorScheme'] ?? array();
        
        return "You are an expert web developer. Create a complete, functional HTML demo website.

    BUSINESS DETAILS:
    - Business: {$business_type}
    - Industry: {$industry}
    - Website Type: {$website_type}
    - Design Style: {$design_style}
    - Features: " . implode(', ', $features) . "
    - Colors: " . json_encode($colors) . "

    REQUIREMENTS:
    1. Create realistic, industry-specific content
    2. Use the provided color scheme
    3. Include functional features based on website type
    4. Make it responsive and professional
    5. Add realistic business information

    RESPOND WITH ONLY THIS JSON:
    {
    \"html\": \"[Complete HTML document with inline CSS and JS]\",
    \"css\": \"[Additional CSS styles]\",
    \"js\": \"[JavaScript functionality]\"
    }

    IMPORTANT:
    - For E-commerce: Include product grids, shopping cart, product pages
    - For Restaurant: Include menu, reservations, location
    - For Portfolio: Include project gallery, about section
    - For SaaS: Include pricing, features, demo sections
    - Use REAL placeholder content, not lorem ipsum
    - Make forms functional with proper validation
    - Include hover effects and animations";
    }
    
    public function generate_demo($concept_data) {
        try {
            // Create unique demo ID
            $demo_id = 'demo_' . time() . '_' . wp_generate_password(8, false);
            $demo_dir = $this->upload_dir . $demo_id . '/';
            
            if (!wp_mkdir_p($demo_dir)) {
                return array('success' => false, 'error' => 'Failed to create demo directory');
            }
            
            // Generate HTML content using AI
            $ai_generator = new AWB_AI_Generator();
            $demo_content = $ai_generator->generate_demo_code($concept_data);
            
            if (!$demo_content) {
                return array('success' => false, 'error' => 'Failed to generate demo content');
            }
            
            // Save generated files
            file_put_contents($demo_dir . 'index.html', $demo_content['html']);
            file_put_contents($demo_dir . 'styles.css', $demo_content['css']);
            file_put_contents($demo_dir . 'script.js', $demo_content['js']);
            
            // Generate demo URL
            $upload_url = wp_upload_dir()['baseurl'];
            $demo_url = $upload_url . '/ai-web-builder-demos/' . $demo_id . '/index.html';
            
            return array(
                'success' => true,
                'demo_url' => $demo_url,
                'demo_id' => $demo_id
            );
            
        } catch (Exception $e) {
            error_log('AWB Demo Generation Error: ' . $e->getMessage());
            return array('success' => false, 'error' => 'Failed to generate demo');
        }
    }
    
    private function build_html_demo($concept_data) {
        $concept = $concept_data['concept'];
        $color_scheme = $concept_data['colorScheme'] ?? array();
        $wireframe = $concept_data['wireframe'] ?? array();
        $features = $concept_data['features'] ?? array();
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . esc_html($concept['title'] ?? 'AI Generated Website') . '</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</head>
<body>';
        
        // Generate header
        $html .= $this->generate_header_html($concept);
        
        // Generate main content based on wireframe
        if (isset($wireframe['homepage']['sections'])) {
            foreach ($wireframe['homepage']['sections'] as $section) {
                $html .= $this->generate_section_html($section, $concept, $features);
            }
        } else {
            // Fallback sections
            $html .= $this->generate_hero_html($concept);
            $html .= $this->generate_features_html($concept, $features);
            $html .= $this->generate_cta_html($concept);
            $html .= $this->generate_footer_html($concept);
        }
        
        $html .= '
    <script src="script.js"></script>
</body>
</html>';
        
        return $html;
    }
    
    private function generate_header_html($concept) {
        $business_name = $concept['title'] ?? 'Your Business';
        
        return '
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>' . esc_html($business_name) . '</h1>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
                <div class="header-cta">
                    <button class="btn btn-primary">Get Started</button>
                </div>
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>';
    }
    
    private function generate_section_html($section, $concept, $features) {
        $section_class = 'section-' . sanitize_title($section['name']);
        $section_type = $section['type'] ?? 'content';
        
        $html = '<section class="' . $section_class . ' ' . $section_type . '" id="' . sanitize_title($section['name']) . '">';
        
        switch ($section_type) {
            case 'hero':
                $html .= $this->generate_hero_content($concept);
                break;
            case 'feature_grid':
                $html .= $this->generate_features_content($concept, $features);
                break;
            case 'services_grid':
                $html .= $this->generate_services_content($concept);
                break;
            case 'testimonials':
                $html .= $this->generate_testimonials_content();
                break;
            case 'lead_form':
            case 'general_cta':
                $html .= $this->generate_cta_content($concept);
                break;
            case 'footer':
                $html .= $this->generate_footer_content($concept);
                break;
            default:
                $html .= $this->generate_generic_content($section['name'], $concept);
        }
        
        $html .= '</section>';
        
        return $html;
    }
    
    private function generate_hero_content($concept) {
        return '
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">' . esc_html($concept['title'] ?? 'Welcome to Our Business') . '</h1>
                    <p class="hero-tagline">' . esc_html($concept['tagline'] ?? 'Your success is our mission') . '</p>
                    <p class="hero-description">' . esc_html($concept['description'] ?? 'We provide innovative solutions to help your business grow and succeed in today\'s competitive market.') . '</p>
                    <div class="hero-actions">
                        <button class="btn btn-primary btn-large">Get Started Today</button>
                        <button class="btn btn-secondary btn-large">Learn More</button>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="placeholder-image">
                        <i class="fas fa-rocket fa-5x"></i>
                        <p>Hero Image Placeholder</p>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    private function generate_features_content($concept, $features) {
        $essential_features = $features['essential'] ?? array('Professional Design', 'Mobile Responsive', 'SEO Optimized');
        
        $html = '
        <div class="container">
            <div class="section-header">
                <h2>Why Choose Us</h2>
                <p>Discover what makes us the right choice for your business needs</p>
            </div>
            <div class="features-grid">';
        
        foreach (array_slice($essential_features, 0, 3) as $index => $feature) {
            $icons = array('fas fa-star', 'fas fa-shield-alt', 'fas fa-chart-line');
            $icon = $icons[$index] ?? 'fas fa-check';
            
            $html .= '
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="' . $icon . '"></i>
                    </div>
                    <h3>' . esc_html($feature) . '</h3>
                    <p>Experience the benefits of ' . strtolower($feature) . ' with our professional approach to web development.</p>
                </div>';
        }
        
        $html .= '
            </div>
        </div>';
        
        return $html;
    }
    
    private function generate_services_content($concept) {
        $services = array(
            array('name' => 'Web Design', 'icon' => 'fas fa-palette', 'desc' => 'Beautiful, modern designs that convert visitors into customers'),
            array('name' => 'Development', 'icon' => 'fas fa-code', 'desc' => 'Fast, secure, and scalable websites built with latest technologies'),
            array('name' => 'SEO Optimization', 'icon' => 'fas fa-search', 'desc' => 'Get found online with our proven SEO strategies and techniques')
        );
        
        $html = '
        <div class="container">
            <div class="section-header">
                <h2>Our Services</h2>
                <p>Comprehensive solutions to help your business succeed online</p>
            </div>
            <div class="services-grid">';
        
        foreach ($services as $service) {
            $html .= '
                <div class="service-card">
                    <div class="service-icon">
                        <i class="' . $service['icon'] . '"></i>
                    </div>
                    <h3>' . $service['name'] . '</h3>
                    <p>' . $service['desc'] . '</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>';
        }
        
        $html .= '
            </div>
        </div>';
        
        return $html;
    }
    
    private function generate_testimonials_content() {
        $testimonials = array(
            array('text' => 'Amazing service and results! Our website looks fantastic and our online presence has never been stronger.', 'author' => 'Sarah Johnson', 'title' => 'CEO, TechCorp'),
            array('text' => 'Professional team, excellent communication, and delivered exactly what we needed on time and on budget.', 'author' => 'Mike Chen', 'title' => 'Marketing Director'),
            array('text' => 'The best investment we made for our business. The new website has increased our leads by 300%.', 'author' => 'Lisa Rodriguez', 'title' => 'Business Owner')
        );
        
        $html = '
        <div class="container">
            <div class="section-header">
                <h2>What Our Clients Say</h2>
                <p>Don\'t just take our word for it - hear from our satisfied customers</p>
            </div>
            <div class="testimonials-grid">';
        
        foreach ($testimonials as $testimonial) {
            $html .= '
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>"' . $testimonial['text'] . '"</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>' . $testimonial['author'] . '</h4>
                                <span>' . $testimonial['title'] . '</span>
                            </div>
                        </div>
                    </div>
                </div>';
        }
        
        $html .= '
            </div>
        </div>';
        
        return $html;
    }
    
    private function generate_cta_content($concept) {
        return '
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Get Started?</h2>
                <p>Take the next step towards growing your business online. Contact us today for a free consultation.</p>
                <div class="cta-actions">
                    <button class="btn btn-primary btn-large">Get Free Quote</button>
                    <button class="btn btn-secondary btn-large">Call Us Now</button>
                </div>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(555) 123-4567</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>hello@yourbusiness.com</span>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    private function generate_footer_content($concept) {
        $business_name = $concept['title'] ?? 'Your Business';
        
        return '
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>' . esc_html($business_name) . '</h3>
                    <p>Building successful businesses through innovative web solutions and exceptional service.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Web Design</a></li>
                        <li><a href="#">Development</a></li>
                        <li><a href="#">SEO</a></li>
                        <li><a href="#">Maintenance</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="contact-details">
                        <p><i class="fas fa-map-marker-alt"></i> 123 Business St, City, State 12345</p>
                        <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                        <p><i class="fas fa-envelope"></i> hello@yourbusiness.com</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ' . esc_html($business_name) . '. All rights reserved. | Generated by AI Web Builder</p>
            </div>
        </div>';
    }
    
    private function generate_generic_content($section_name, $concept) {
        return '
        <div class="container">
            <div class="section-header">
                <h2>' . esc_html($section_name) . '</h2>
                <p>This section showcases ' . strtolower($section_name) . ' for ' . esc_html($concept['title'] ?? 'your business') . '</p>
            </div>
            <div class="generic-content">
                <p>This is a placeholder for the ' . strtolower($section_name) . ' section. The final content would be customized based on your specific business needs and requirements.</p>
            </div>
        </div>';
    }
    
    private function build_css_styles($concept_data) {
        $color_scheme = $concept_data['colorScheme'] ?? array();
        $typography = $concept_data['typography'] ?? array();
        
        // Default colors if not provided
        $primary = $color_scheme['primary'] ?? '#3b82f6';
        $secondary = $color_scheme['secondary'] ?? '#8b5cf6';
        $accent = $color_scheme['accent'] ?? '#f59e0b';
        $background = $color_scheme['background'] ?? '#ffffff';
        $text = $color_scheme['text'] ?? '#1f2937';
        
        return '
/* CSS Variables */
:root {
    --primary-color: ' . $primary . ';
    --secondary-color: ' . $secondary . ';
    --accent-color: ' . $accent . ';
    --background-color: ' . $background . ';
    --text-color: ' . $text . ';
    --text-light: #6b7280;
    --border-color: #e5e7eb;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Reset and Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: var(--text-color);
    background-color: var(--background-color);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Header Styles */
.site-header {
    background: var(--background-color);
    box-shadow: var(--shadow);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
}

.logo h1 {
    color: var(--primary-color);
    font-size: 1.5rem;
    font-weight: 700;
}

.main-nav ul {
    display: flex;
    list-style: none;
    gap: 2rem;
}

.main-nav a {
    text-decoration: none;
    color: var(--text-color);
    font-weight: 500;
    transition: color 0.3s ease;
}

.main-nav a:hover {
    color: var(--primary-color);
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-color);
    cursor: pointer;
}

/* Button Styles */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: color-mix(in srgb, var(--primary-color) 90%, black);
    transform: translateY(-2px);
}

.btn-secondary {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-secondary:hover {
    background: var(--primary-color);
    color: white;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

/* Section Styles */
section {
    padding: 4rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-header h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--text-color);
}

.section-header p {
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 600px;
    margin: 0 auto;
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 8rem 0 4rem;
    margin-top: 80px;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-tagline {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.hero-description {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.8;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.placeholder-image {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    padding: 4rem 2rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: var(--shadow);
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 4rem;
    height: 4rem;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.feature-card h3 {
    margin-bottom: 1rem;
    color: var(--text-color);
}

/* Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.service-card {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-icon {
    width: 3rem;
    height: 3rem;
    background: var(--accent-color);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
    font-size: 1.2rem;
}

.service-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

/* Testimonials */
.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.testimonial-card {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: var(--shadow);
}

.quote-icon {
    color: var(--primary-color);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
}

.author-avatar {
    width: 3rem;
    height: 3rem;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.author-info h4 {
    margin-bottom: 0.25rem;
}

.author-info span {
    color: var(--text-light);
    font-size: 0.9rem;
}

/* CTA Section */
.general-cta, .lead-form {
    background: var(--primary-color);
    color: white;
}

.cta-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.cta-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.contact-info {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Footer */
.footer {
    background: var(--text-color);
    color: white;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h3,
.footer-section h4 {
    margin-bottom: 1rem;
}

.footer-section ul {
    list-style: none;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section a:hover {
    color: white;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.social-links a {
    width: 2.5rem;
    height: 2.5rem;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 2rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-nav {
        display: none;
    }
    
    .mobile-menu-toggle {
        display: block;
    }
    
    .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .features-grid,
    .services-grid,
    .testimonials-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-actions,
    .contact-info {
        flex-direction: column;
        align-items: center;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
}';
    }
    
    private function build_js_functionality($concept_data) {
        return '
// AI Web Builder Demo JavaScript
document.addEventListener("DOMContentLoaded", function() {
    
    // Mobile menu functionality
    const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
    const mainNav = document.querySelector(".main-nav");
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener("click", function() {
            mainNav.classList.toggle("active");
        });
    }
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll("a[href^=\"#\"]");
    navLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const targetId = this.getAttribute("href");
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });
    
    // Button click handlers
    const buttons = document.querySelectorAll(".btn");
    buttons.forEach(button => {
        button.addEventListener("click", function(e) {
            if (this.textContent.includes("Get Started") || 
                this.textContent.includes("Free Quote") ||
                this.textContent.includes("Contact")) {
                e.preventDefault();
                showContactModal();
            }
        });
    });
    
    // Contact modal functionality
    function showContactModal() {
        // Create modal if it doesnt exist
        let modal = document.getElementById("contact-modal");
        if (!modal) {
            modal = createContactModal();
            document.body.appendChild(modal);
        }
        modal.style.display = "flex";
    }
    
    function createContactModal() {
        const modal = document.createElement("div");
        modal.id = "contact-modal";
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        
        modal.innerHTML = `
            <div class="modal-content" style="
                background: white;
                padding: 2rem;
                border-radius: 1rem;
                max-width: 500px;
                width: 90%;
                position: relative;
            ">
                <button class="close-modal" style="
                    position: absolute;
                    top: 1rem;
                    right: 1rem;
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                ">&times;</button>
                <h3 style="margin-bottom: 1rem;">Get In Touch</h3>
                <p style="margin-bottom: 2rem; color: #6b7280;">
                    This is a demo website. In a real implementation, this would connect to your contact form or CRM system.
                </p>
                <form style="display: flex; flex-direction: column; gap: 1rem;">
                    <input type="text" placeholder="Your Name" style="
                        padding: 0.75rem;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.5rem;
                        font-size: 1rem;
                    ">
                    <input type="email" placeholder="Your Email" style="
                        padding: 0.75rem;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.5rem;
                        font-size: 1rem;
                    ">
                    <textarea placeholder="Your Message" rows="4" style="
                        padding: 0.75rem;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.5rem;
                        font-size: 1rem;
                        resize: vertical;
                    "></textarea>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        `;
        
        // Close modal functionality
        modal.querySelector(".close-modal").addEventListener("click", function() {
            modal.style.display = "none";
        });
        
        modal.addEventListener("click", function(e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
        
        return modal;
    }
    
    // Scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
            }
        });
    }, observerOptions);
    
    // Observe all cards and sections
    const animatedElements = document.querySelectorAll(".feature-card, .service-card, .testimonial-card");
    animatedElements.forEach(el => {
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        observer.observe(el);
    });
    
    // Demo notification
    setTimeout(function() {
        showDemoNotification();
    }, 3000);
    
    function showDemoNotification() {
        const notification = document.createElement("div");
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            max-width: 300px;
            animation: slideIn 0.5s ease;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Demo Website</strong><br>
                    <small>Generated by AI Web Builder</small>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="
                    background: none;
                    border: none;
                    color: white;
                    cursor: pointer;
                    margin-left: auto;
                ">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 10 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }
    
    // Add slide-in animation
    const style = document.createElement("style");
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
});';
    }
    
    private function save_demo_info($demo_id, $concept_data, $demo_url) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'awb_generated_concepts';
        
        // Update existing record with demo URL
        $wpdb->update(
            $table_name,
            array('demo_url' => $demo_url),
            array('id' => $wpdb->insert_id),
            array('%s'),
            array('%d')
        );
    }
}