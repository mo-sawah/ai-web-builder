# AI Web Builder WordPress Plugin

A comprehensive WordPress plugin that generates detailed website concepts using AI, complete with wireframes, cost estimates, and live demo generation.

## Features

### Core Functionality

- **AI-Powered Concept Generation**: Creates detailed website concepts tailored to specific business needs
- **Visual Wireframe Creation**: Generates interactive wireframe previews with section-by-section breakdowns
- **Comprehensive Cost Analysis**: Provides realistic project cost breakdowns and payment schedules
- **Live Demo Generation**: Creates functional HTML demos that users can preview and interact with
- **Dual AI Integration**: Supports both OpenAI and OpenRouter APIs for maximum reliability
- **Smart Caching System**: Reduces API costs and improves performance
- **Professional Admin Interface**: Easy-to-use settings and management dashboard

### Advanced Features

- **Industry-Specific Optimization**: Tailored recommendations based on business industry
- **Responsive Design Analysis**: Mobile, tablet, and desktop wireframe variations
- **Technical Specifications**: Hosting requirements and technology stack recommendations
- **SEO Analysis**: Keyword suggestions and optimization recommendations
- **Performance Metrics**: Load time estimates and optimization priorities
- **Color Palette Generation**: AI-generated color schemes matching business goals
- **Feature Recommendations**: Essential, recommended, and advanced feature suggestions

## Installation

1. Download the plugin files
2. Upload to your WordPress `/wp-content/plugins/` directory
3. Activate the plugin through the WordPress admin panel
4. Configure your API keys in **AI Web Builder > Settings**

## Configuration

### API Keys Required

#### OpenAI API Key

1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Create a new API key
3. Enter it in the plugin settings

#### OpenRouter API Key (Recommended)

1. Visit [OpenRouter](https://openrouter.ai/keys)
2. Create a new API key
3. Enter it in the plugin settings

### Settings Options

- **Default AI Model**: Choose primary AI service (OpenAI or OpenRouter)
- **Enable Caching**: Cache AI responses to reduce costs
- **Cache Duration**: How long to store cached responses (300-86400 seconds)

## Usage

### Shortcode

Add the AI Web Builder form to any page or post:

```
[ai_web_builder]
```

### Shortcode Parameters

- `style` - Form styling (default: "default")
- `show_demo_button` - Show/hide demo generation button (default: "true")

### Example with Parameters

```
[ai_web_builder style="minimal" show_demo_button="false"]
```

## File Structure

```
ai-web-builder/
├── ai-web-builder.php                 # Main plugin file
├── includes/
│   ├── class-ai-generator.php         # AI concept generation logic
│   ├── class-wireframe-generator.php  # Wireframe creation system
│   ├── class-demo-generator.php       # Live demo generation
│   └── functions.php                  # Helper functions
├── admin/
│   ├── admin-page.php                 # Admin dashboard
│   └── settings-page.php              # Settings interface
├── templates/
│   └── frontend-form.php              # Frontend form template
├── assets/
│   ├── css/
│   │   ├── frontend.css               # Frontend styles
│   │   └── admin.css                  # Admin styles
│   └── js/
│       ├── frontend.js                # Frontend JavaScript
│       └── admin.js                   # Admin JavaScript
└── README.md                          # This file
```

## How It Works

### 1. Data Collection

The plugin collects comprehensive business information through a detailed form:

- Business details (name, industry, size, goals)
- Website requirements (pages, content status, timeline)
- Design preferences (style, colors, branding)
- Feature selections (essential, business, advanced)

### 2. AI Processing

- Sends structured prompts to AI services (OpenAI/OpenRouter)
- Generates detailed business analysis and recommendations
- Creates technical specifications and cost breakdowns
- Produces SEO and performance optimization suggestions

### 3. Wireframe Generation

- Creates responsive wireframe layouts
- Generates section-by-section breakdowns
- Provides interaction patterns and component libraries
- Explains design rationale and layout choices

### 4. Demo Creation

- Builds functional HTML/CSS/JavaScript demos
- Creates realistic content and interactions
- Generates responsive layouts for all devices
- Provides downloadable demo files

## API Integration

### OpenAI Integration

- Uses GPT-4 for high-quality concept generation
- Structured prompts for consistent JSON responses
- Error handling and retry logic
- Token usage optimization

### OpenRouter Integration

- Access to multiple AI models (Claude, Gemini, etc.)
- Fallback system for improved reliability
- Cost optimization across different providers
- Unified API interface

## Caching System

### Benefits

- Reduces API costs by caching similar requests
- Improves response times for repeat concepts
- Configurable cache duration
- Automatic cleanup of expired cache

### Cache Keys

- Generated based on form data hash
- Excludes timestamp-sensitive information
- Provides consistent caching for similar inputs

## Security Features

### Data Protection

- All user inputs are sanitized and validated
- API keys stored securely in WordPress options
- Rate limiting to prevent abuse
- Nonce verification for all AJAX requests

### File Security

- Demo files created in secure upload directory
- Automatic cleanup of old demo files
- File type restrictions and validation
- Directory traversal protection

## Performance Optimizations

### Frontend

- Efficient CSS with minimal dependencies
- Optimized JavaScript with event delegation
- Responsive images and layouts
- Progressive enhancement approach

### Backend

- Efficient database queries with proper indexing
- Minimal plugin overhead
- Optimized AI API calls
- Smart caching strategies

## Troubleshooting

### Common Issues

#### "No API keys configured" error

- Ensure at least one API key (OpenAI or OpenRouter) is configured
- Check API key validity and permissions
- Verify network connectivity to API endpoints

#### Demo generation fails

- Check upload directory permissions
- Ensure sufficient disk space
- Verify PHP file creation permissions

#### Slow response times

- Enable caching in plugin settings
- Consider using OpenRouter for faster responses
- Check server resources and API quotas

### Debug Mode

Enable WordPress debug mode to see detailed error logs:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Hooks and Filters

### Actions

- `awb_concept_generated` - Fired after concept generation
- `awb_demo_created` - Fired after demo creation
- `awb_daily_cleanup` - Daily maintenance tasks

### Filters

- `awb_concept_data` - Modify concept data before display
- `awb_wireframe_sections` - Customize wireframe sections
- `awb_demo_styles` - Modify demo CSS styles

## Requirements

### Server Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- cURL extension enabled
- File write permissions for uploads directory

### API Requirements

- OpenAI API key (GPT-4 access recommended)
- OR OpenRouter API key
- Sufficient API quota for your usage

## License

GPL v2 or later

## Support

For support and questions:

- Visit: [https://sawahsolutions.com](https://sawahsolutions.com)
- Contact: AI Web Builder Support

## Changelog

### Version 1.0.0

- Initial release
- AI concept generation with OpenAI/OpenRouter
- Visual wireframe creation
- Live demo generation
- Comprehensive admin interface
- Smart caching system
- Security and performance optimizations

## Contributing

This plugin was developed by Mohamed Sawah at Sawah Solutions. For feature requests or bug reports, please contact our support team.

---

**AI Web Builder** - Transforming business ideas into comprehensive website concepts with the power of AI.
