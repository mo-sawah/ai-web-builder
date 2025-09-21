<?php
/**
 * Frontend Form Template
 * Renders the AI Web Builder form
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="ai-web-builder-container" class="awb-container">
    <div class="awb-header">
        <div class="awb-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <h1 class="awb-title">AI Website Builder</h1>
        <p class="awb-subtitle">Describe your business and watch AI create a complete website concept with wireframes, color schemes, cost estimates, and even build a live demo for you to preview.</p>
    </div>

    <form id="awb-form" class="awb-form">
        <!-- Business Information Section -->
        <div class="awb-section">
            <h3 class="awb-section-title">
                <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Business Information
            </h3>
            
            <div class="awb-grid">
                <div class="awb-field">
                    <label for="businessType">Business Name/Type *</label>
                    <input type="text" id="businessType" name="businessType" placeholder="e.g., TechFlow Solutions, Local Restaurant, Law Firm" required>
                </div>

                <div class="awb-field">
                    <label for="industry">Industry *</label>
                    <select id="industry" name="industry" required>
                        <option value="">Select Industry</option>
                        <option value="Technology & Software">Technology & Software</option>
                        <option value="Healthcare & Medical">Healthcare & Medical</option>
                        <option value="E-commerce & Retail">E-commerce & Retail</option>
                        <option value="Education & Training">Education & Training</option>
                        <option value="Finance & Banking">Finance & Banking</option>
                        <option value="Real Estate">Real Estate</option>
                        <option value="Restaurant & Food">Restaurant & Food</option>
                        <option value="Legal Services">Legal Services</option>
                        <option value="Creative Agency">Creative Agency</option>
                        <option value="Non-Profit">Non-Profit</option>
                        <option value="Fitness & Wellness">Fitness & Wellness</option>
                        <option value="Travel & Tourism">Travel & Tourism</option>
                        <option value="Automotive">Automotive</option>
                        <option value="Fashion & Beauty">Fashion & Beauty</option>
                        <option value="Construction">Construction</option>
                        <option value="Consulting">Consulting</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Photography">Photography</option>
                        <option value="Marketing & Advertising">Marketing & Advertising</option>
                        <option value="Professional Services">Professional Services</option>
                        <option value="Architecture & Design">Architecture & Design</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="companySize">Company Size</label>
                    <select id="companySize" name="companySize">
                        <option value="">Select Company Size</option>
                        <option value="Solo/Freelancer">Solo/Freelancer</option>
                        <option value="2-10 employees">2-10 employees</option>
                        <option value="11-50 employees">11-50 employees</option>
                        <option value="51-200 employees">51-200 employees</option>
                        <option value="200+ employees">200+ employees</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="targetAudience">Target Audience</label>
                    <input type="text" id="targetAudience" name="targetAudience" placeholder="e.g., Small businesses, Young professionals, Families">
                </div>

                <div class="awb-field">
                    <label for="businessGoal">Primary Business Goal</label>
                    <select id="businessGoal" name="businessGoal">
                        <option value="">Select Primary Goal</option>
                        <option value="Generate Leads">Generate Leads</option>
                        <option value="Sell Products Online">Sell Products Online</option>
                        <option value="Build Brand Awareness">Build Brand Awareness</option>
                        <option value="Provide Information">Provide Information</option>
                        <option value="Showcase Portfolio">Showcase Portfolio</option>
                        <option value="Accept Bookings/Appointments">Accept Bookings/Appointments</option>
                        <option value="Customer Support">Customer Support</option>
                        <option value="Community Building">Community Building</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="budget">Project Budget</label>
                    <select id="budget" name="budget">
                        <option value="">Select Budget Range</option>
                        <option value="$1,000 - $3,000">$1,000 - $3,000</option>
                        <option value="$3,000 - $5,000">$3,000 - $5,000</option>
                        <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                        <option value="$10,000 - $20,000">$10,000 - $20,000</option>
                        <option value="$20,000 - $50,000">$20,000 - $50,000</option>
                        <option value="$50,000+">$50,000+</option>
                    </select>
                </div>

                <div class="awb-field awb-field-full">
                    <label for="businessDescription">Brief Description of Your Business</label>
                    <textarea id="businessDescription" name="businessDescription" rows="3" placeholder="Tell us about your business, what you do, and what makes you unique..."></textarea>
                </div>
            </div>
        </div>

        <!-- Website Requirements Section -->
        <div class="awb-section">
            <h3 class="awb-section-title">
                <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Website Requirements
            </h3>
            
            <div class="awb-grid">
                <div class="awb-field">
                    <label for="pageCount">Expected Number of Pages</label>
                    <select id="pageCount" name="pageCount">
                        <option value="">Select Page Count</option>
                        <option value="1-5 pages">1-5 pages</option>
                        <option value="6-10 pages">6-10 pages</option>
                        <option value="11-20 pages">11-20 pages</option>
                        <option value="21-50 pages">21-50 pages</option>
                        <option value="50+ pages">50+ pages</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="contentStatus">Content Status</label>
                    <select id="contentStatus" name="contentStatus">
                        <option value="">Select Content Status</option>
                        <option value="Content ready">Content ready</option>
                        <option value="Content partially ready">Content partially ready</option>
                        <option value="Need content creation">Need content creation</option>
                        <option value="Need content strategy">Need content strategy</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="timeline">Preferred Launch Timeline</label>
                    <select id="timeline" name="timeline">
                        <option value="">Select Timeline</option>
                        <option value="ASAP (Rush)">ASAP (Rush)</option>
                        <option value="2-4 weeks">2-4 weeks</option>
                        <option value="1-2 months">1-2 months</option>
                        <option value="2-3 months">2-3 months</option>
                        <option value="3+ months">3+ months</option>
                        <option value="Flexible">Flexible</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="currentWebsite">Current Website Status</label>
                    <select id="currentWebsite" name="currentWebsite">
                        <option value="">Select Current Status</option>
                        <option value="No website">No website</option>
                        <option value="Outdated website">Outdated website</option>
                        <option value="Need redesign">Need redesign</option>
                        <option value="Need improvements">Need improvements</option>
                        <option value="Starting fresh">Starting fresh</option>
                    </select>
                </div>

                <div class="awb-field awb-field-full">
                    <label for="existingWebsite">Existing Website URL (if applicable)</label>
                    <input type="url" id="existingWebsite" name="existingWebsite" placeholder="https://yourwebsite.com">
                </div>

                <div class="awb-field awb-field-full">
                    <label for="competitors">Competitor Websites (for reference)</label>
                    <textarea id="competitors" name="competitors" rows="3" placeholder="List competitor websites or sites you admire (one per line)..."></textarea>
                </div>
            </div>
        </div>

        <!-- Website Type and Design Style -->
        <div class="awb-section">
            <div class="awb-grid awb-grid-2">
                <div class="awb-subsection">
                    <h4 class="awb-subsection-title">
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Website Type
                    </h4>
                    
                    <div class="awb-button-grid" id="websiteTypes">
                        <button type="button" class="awb-option-btn" data-value="Business Website">
                            <div class="awb-option-title">Business Website</div>
                            <div class="awb-option-desc">For companies</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="E-commerce Store">
                            <div class="awb-option-title">E-commerce Store</div>
                            <div class="awb-option-desc">Sell products online</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="Portfolio Site">
                            <div class="awb-option-title">Portfolio Site</div>
                            <div class="awb-option-desc">For creatives</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="Blog/Magazine">
                            <div class="awb-option-title">Blog/Magazine</div>
                            <div class="awb-option-desc">For publishers</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="SaaS Platform">
                            <div class="awb-option-title">SaaS Platform</div>
                            <div class="awb-option-desc">For SaaS startups</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="Landing Page">
                            <div class="awb-option-title">Landing Page</div>
                            <div class="awb-option-desc">For campaigns</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="Booking/Appointment">
                            <div class="awb-option-title">Booking/Appointment</div>
                            <div class="awb-option-desc">For services</div>
                        </button>
                        <button type="button" class="awb-option-btn" data-value="Membership/Community">
                            <div class="awb-option-title">Membership/Community</div>
                            <div class="awb-option-desc">For communities</div>
                        </button>
                    </div>
                </div>

                <div class="awb-subsection">
                    <h4 class="awb-subsection-title">
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                        </svg>
                        Design Style Preference
                    </h4>
                    
                    <div class="awb-button-grid awb-button-grid-small" id="designStyles">
                        <button type="button" class="awb-style-btn" data-value="Modern & Clean">Modern & Clean</button>
                        <button type="button" class="awb-style-btn" data-value="Professional & Corporate">Professional & Corporate</button>
                        <button type="button" class="awb-style-btn" data-value="Creative & Artistic">Creative & Artistic</button>
                        <button type="button" class="awb-style-btn" data-value="Minimalist">Minimalist</button>
                        <button type="button" class="awb-style-btn" data-value="Bold & Vibrant">Bold & Vibrant</button>
                        <button type="button" class="awb-style-btn" data-value="Elegant & Luxury">Elegant & Luxury</button>
                        <button type="button" class="awb-style-btn" data-value="Tech & Futuristic">Tech & Futuristic</button>
                        <button type="button" class="awb-style-btn" data-value="Retro & Vintage">Retro & Vintage</button>
                        <button type="button" class="awb-style-btn" data-value="Fun & Playful">Fun & Playful</button>
                        <button type="button" class="awb-style-btn" data-value="Dark & Edgy">Dark & Edgy</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="awb-section">
            <h3 class="awb-section-title">
                <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Core Features & Functionality
            </h3>
            
            <div class="awb-features-grid">
                <div class="awb-feature-category">
                    <h4>Essential Features</h4>
                    <div class="awb-feature-list" id="essentialFeatures">
                        <button type="button" class="awb-feature-btn" data-value="Contact Forms">Contact Forms</button>
                        <button type="button" class="awb-feature-btn" data-value="Mobile Responsive">Mobile Responsive</button>
                        <button type="button" class="awb-feature-btn" data-value="SEO Optimization">SEO Optimization</button>
                        <button type="button" class="awb-feature-btn" data-value="Social Media Integration">Social Media Integration</button>
                        <button type="button" class="awb-feature-btn" data-value="Google Analytics">Google Analytics</button>
                        <button type="button" class="awb-feature-btn" data-value="Newsletter Signup">Newsletter Signup</button>
                        <button type="button" class="awb-feature-btn" data-value="Image Gallery">Image Gallery</button>
                        <button type="button" class="awb-feature-btn" data-value="Testimonials">Testimonials</button>
                        <button type="button" class="awb-feature-btn" data-value="FAQ Section">FAQ Section</button>
                    </div>
                </div>

                <div class="awb-feature-category">
                    <h4>Business Features</h4>
                    <div class="awb-feature-list" id="businessFeatures">
                        <button type="button" class="awb-feature-btn" data-value="Online Booking">Online Booking</button>
                        <button type="button" class="awb-feature-btn" data-value="E-commerce Store">E-commerce Store</button>
                        <button type="button" class="awb-feature-btn" data-value="Payment Gateway">Payment Gateway</button>
                        <button type="button" class="awb-feature-btn" data-value="Customer Portal">Customer Portal</button>
                        <button type="button" class="awb-feature-btn" data-value="Inventory Management">Inventory Management</button>
                        <button type="button" class="awb-feature-btn" data-value="CRM Integration">CRM Integration</button>
                        <button type="button" class="awb-feature-btn" data-value="Lead Generation">Lead Generation</button>
                        <button type="button" class="awb-feature-btn" data-value="Quote Calculator">Quote Calculator</button>
                        <button type="button" class="awb-feature-btn" data-value="Multi-language">Multi-language</button>
                    </div>
                </div>

                <div class="awb-feature-category">
                    <h4>Advanced Features</h4>
                    <div class="awb-feature-list" id="advancedFeatures">
                        <button type="button" class="awb-feature-btn" data-value="Live Chat">Live Chat</button>
                        <button type="button" class="awb-feature-btn" data-value="AI Chatbot">AI Chatbot</button>
                        <button type="button" class="awb-feature-btn" data-value="Blog System">Blog System</button>
                        <button type="button" class="awb-feature-btn" data-value="User Registration">User Registration</button>
                        <button type="button" class="awb-feature-btn" data-value="Forum/Community">Forum/Community</button>
                        <button type="button" class="awb-feature-btn" data-value="API Integration">API Integration</button>
                        <button type="button" class="awb-feature-btn" data-value="Custom Database">Custom Database</button>
                        <button type="button" class="awb-feature-btn" data-value="Advanced Analytics">Advanced Analytics</button>
                        <button type="button" class="awb-feature-btn" data-value="Performance Optimization">Performance Optimization</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Design & Branding Section -->
        <div class="awb-section">
            <h3 class="awb-section-title">
                <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Design & Branding Preferences
            </h3>
            
            <div class="awb-grid">
                <div class="awb-field">
                    <label for="existingBranding">Do you have existing branding?</label>
                    <select id="existingBranding" name="existingBranding">
                        <option value="">Select Option</option>
                        <option value="Complete brand guidelines">Complete brand guidelines</option>
                        <option value="Logo and basic colors">Logo and basic colors</option>
                        <option value="Logo only">Logo only</option>
                        <option value="Need complete branding">Need complete branding</option>
                    </select>
                </div>

                <div class="awb-field">
                    <label for="colorPreference">Color Preference</label>
                    <select id="colorPreference" name="colorPreference">
                        <option value="">Select Color Scheme</option>
                        <option value="Use existing brand colors">Use existing brand colors</option>
                        <option value="Professional blues/grays">Professional blues/grays</option>
                        <option value="Warm earth tones">Warm earth tones</option>
                        <option value="Bold and vibrant">Bold and vibrant</option>
                        <option value="Minimalist black/white">Minimalist black/white</option>
                        <option value="Open to suggestions">Open to suggestions</option>
                    </select>
                </div>

                <div class="awb-field awb-field-full">
                    <label for="designInspiration">Inspiration Websites or Design References</label>
                    <textarea id="designInspiration" name="designInspiration" rows="3" placeholder="Share any websites, designs, or styles you like (include URLs if possible)..."></textarea>
                </div>

                <div class="awb-field awb-field-full">
                    <label for="additionalRequirements">Additional Requirements or Special Requests</label>
                    <textarea id="additionalRequirements" name="additionalRequirements" rows="4" placeholder="Any specific features, integrations, or requirements we should know about..."></textarea>
                </div>
            </div>
        </div>

        <!-- Generate Button -->
        <div class="awb-submit">
            <button type="submit" id="awb-generate-btn" class="awb-btn awb-btn-primary">
                <svg class="awb-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Generate AI Website Concept
            </button>
            
            <p id="awb-validation-message" class="awb-error-message" style="display: none;">
                Please fill in business name and industry to continue
            </p>
        </div>
    </form>

    <!-- Loading Section -->
    <div id="awb-loading" class="awb-loading" style="display: none;">
        <div class="awb-loading-content">
            <div class="awb-spinner"></div>
            <h3>AI is Creating Your Website Concept...</h3>
            <div class="awb-progress-steps" id="awb-progress-steps">
                <div class="awb-step">
                    <div class="awb-step-icon"></div>
                    <span>Analyzing your business requirements...</span>
                </div>
                <div class="awb-step">
                    <div class="awb-step-icon"></div>
                    <span>Researching industry best practices...</span>
                </div>
                <div class="awb-step">
                    <div class="awb-step-icon"></div>
                    <span>Generating design concepts and wireframes...</span>
                </div>
                <div class="awb-step">
                    <div class="awb-step-icon"></div>
                    <span>Creating color palettes and typography...</span>
                </div>
                <div class="awb-step">
                    <div class="awb-step-icon"></div>
                    <span>Calculating costs and timelines...</span>
                </div>
                <div class="awb-step">
                    <div class="awb-step-icon"></div>
                    <span>Finalizing recommendations...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="awb-results" class="awb-results" style="display: none;">
        <!-- Results will be populated by JavaScript -->
    </div>
</div>