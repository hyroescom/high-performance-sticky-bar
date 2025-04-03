# Lightweight High Performance Sticky Bar - Developer Documentation

## Project Overview
This WordPress plugin implements a high-performance, customizable sticky notification bar that appears at the top of websites. The plugin is designed with performance in mind, featuring minimal resource usage and clean code organization.

## File Structure
```
high-performance-sticky-bar/
├── README.md               # User documentation and plugin information
├── languages/              # Directory for translation files
├── hyroes-sticky-bar.php   # Main plugin file with PHP functionality
└── sticky-bar.js           # Client-side JavaScript functionality
```

## Core Components

### 1. Main Plugin File (hyroes-sticky-bar.php)
The main plugin file handles all server-side functionality and is organized into several key sections:

#### Plugin Header
- Contains WordPress plugin metadata
- Defines version, author, and licensing information
- Sets text domain for internationalization

#### Security and Initialization Functions
- Prevents direct file access
- Creates language directory upon activation
- Registers settings and default options

#### Core Functions
1. `hyroes_sticky_bar_register_settings()`
   - Registers plugin settings in WordPress options API
   - Sets default options if none exist
   - Uses sanitization callback for security
   - Default values:
     - bar_text: "Welcome to our site!"
     - bar_bgcolor: "#333333"
     - enable_bar: 0 (disabled)
     - cookie_hours: 24
     - countdown_enabled: 0 (disabled)
     - countdown_target_date: "" (empty)
     - countdown_action: "zeros"
     - countdown_position: "right"
     - countdown_bg_color: "#000000"
     - countdown_font_color: "#ffffff"
     - countdown_show_labels: 1 (enabled)
     - countdown_labels_position: "bottom"
     - countdown_label_days: "Days"
     - countdown_label_hours: "Hours"
     - countdown_label_minutes: "Minutes"
     - countdown_label_seconds: "Seconds"

2. `hyroes_sticky_bar_sanitize_settings()`
   - Comprehensive sanitization function for all settings
   - Type-specific sanitization for each input (text, color, number)
   - Validation of select/radio fields against allowed values
   - Enforces secure data handling

3. `hyroes_sticky_bar_add_admin_menu()`
   - Adds settings page under Tools menu
   - Sets up admin interface integration
   - Controls user capability requirements

4. `hyroes_sticky_bar_admin_scripts()`
   - Handles admin-side asset loading
   - Loads WordPress color picker functionality
   - Applies conditional loading for performance

5. `hyroes_sticky_bar_options_page()`
   - Renders the admin settings interface
   - Handles settings form submission and validation
   - Implements nonce verification for security
   - Organizes settings into logical sections

6. `hyroes_sticky_bar_enqueue_scripts()`
   - Manages front-end asset loading
   - Injects dynamic CSS based on settings
   - Passes PHP variables to JavaScript
   - Implements early exit pattern for performance

7. `hyroes_sticky_bar_add_html()`
   - Outputs the sticky bar HTML
   - Only runs when the bar is enabled
   - Generates countdown HTML based on settings
   - Uses proper escaping for security

8. `hyroes_sticky_bar_ajax_update()`
   - Handles AJAX requests for countdown data
   - Implemented for cache compatibility
   - Uses nonce verification for security
   - Provides fresh countdown data to JavaScript

### 2. JavaScript File (sticky-bar.js)
Handles all client-side functionality using jQuery:

#### File Organization
- IIFE pattern for scope isolation
- Clear function separation
- Comprehensive documentation
- Global variables for state management
- Event handlers for user interaction

#### Key Features
1. Initialization and Display
   - Document ready handler
   - Cookie checking on page load
   - Conditional countdown initialization
   - Smooth animations for showing/hiding

2. Cookie Management
   - `getCookie()`: Retrieves stored preferences
   - `setCookie()`: Stores user preferences
   - Configurable expiration time

3. UI Interactions
   - Smooth show/hide animations
   - Body padding adjustments
   - Close button functionality
   - DOM manipulation for countdown display

4. Countdown Implementation
   - Initialization with target date handling
   - Regular interval updates (every second)
   - Time unit calculations (days, hours, minutes, seconds)
   - End action handling based on settings
   - Number formatting with leading zeros

5. Cache Compatibility
   - AJAX-based data updates
   - Periodic server checks (every minute)
   - Nonce security verification
   - Dynamic target date updates

## Implementation Details

### Settings Storage
- Uses WordPress options API
- Settings stored in `hyroes_sticky_bar_settings` option
- Includes validation and sanitization
- Default values provided for first use

### Security Measures
1. Direct access prevention using ABSPATH check
2. User capability verification with `current_user_can('manage_options')`
3. Input sanitization with WordPress sanitization functions:
   - `sanitize_text_field()` for text inputs
   - `sanitize_hex_color()` for color values
   - `absint()` for numeric values
   - `in_array()` checks for select/radio values
4. Output escaping with:
   - `esc_html__()` for translatable text
   - `esc_attr()` for HTML attributes
   - `esc_html()` for user-generated text content
   - `wp_kses_post()` for HTML content
5. CSRF protection with nonces:
   - Form submission verification
   - AJAX request validation
6. Early exit patterns to prevent unnecessary code execution

### Internationalization
- Text domain defined in plugin header
- Translation-ready with `esc_html__()`
- Languages directory created on activation
- All user-facing strings are translatable

### Countdown Implementation
1. Settings
   - Enable/disable countdown feature
   - Target date and time selection (HTML5 datetime-local input)
   - Action after countdown ends (show zeros, remove countdown, or remove bar)
   - Position selection (left, right, or below text)
   - Custom background color for numbers
   - Custom font color for numbers
   - Show/hide time unit labels
   - Label position (above or below numbers)
   - Customizable label text for each time unit

2. HTML Structure
```html
<div class="hyroes-countdown" data-position="[position]">
    <div class="hyroes-countdown-item">
        <span class="hyroes-countdown-label">[Days]</span>
        <span class="hyroes-countdown-number days">00</span>
    </div>
    <span class="hyroes-countdown-separator">:</span>
    <div class="hyroes-countdown-item">
        <span class="hyroes-countdown-label">[Hours]</span>
        <span class="hyroes-countdown-number hours">00</span>
    </div>
    <span class="hyroes-countdown-separator">:</span>
    <div class="hyroes-countdown-item">
        <span class="hyroes-countdown-label">[Minutes]</span>
        <span class="hyroes-countdown-number minutes">00</span>
    </div>
    <span class="hyroes-countdown-separator">:</span>
    <div class="hyroes-countdown-item">
        <span class="hyroes-countdown-label">[Seconds]</span>
        <span class="hyroes-countdown-number seconds">00</span>
    </div>
</div>
```

3. JavaScript Implementation
   - Initialization on document ready
   - Real-time countdown updates using setInterval
   - Automatic cleanup on bar close
   - End actions (show zeros, remove countdown, or remove bar)
   - Number padding with leading zeros
   - AJAX-based updates for cache compatibility
   - Periodic server check for updated target date
   - Dynamic countdown action handling

4. CSS Styling
```css
.hyroes-countdown {
    display: inline-block;
    margin: 0 10px;
}
.hyroes-countdown-item {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}
.hyroes-countdown-number {
    background-color: [user-setting];
    color: [user-setting];
    padding: 2px 6px;
    border-radius: 3px;
    margin: 0 2px;
    display: inline-block;
    min-width: 2em;
    text-align: center;
}
.hyroes-countdown-label {
    display: block;
    font-size: 12px;
    margin: [position-dependent];
    color: [user-setting];
}
.hyroes-countdown-separator {
    color: [user-setting];
    margin: 0 2px;
}
.hyroes-countdown-below {
    margin-top: 5px;
}
```

### Cache Compatibility
1. AJAX Implementation
   - WordPress admin-ajax.php endpoint
   - Nonce verification for security
   - Periodic data refresh (every 60 seconds)
   - Fallback to initial data if server unavailable
   - Immediate display update after receiving new data

2. Data Handling
   - Server provides fresh target date
   - Client recalculates countdown based on updated data
   - Works with page caching plugins and CDNs
   - Minimal server load (one small request per minute)
   - Clean interval management to prevent memory leaks

### Cookie Implementation
- Cookie Name: `HyroesStickyBarClosed`
- Stores: Simple closed status
- Duration: Configurable (default 24 hours)
- Path: Site-wide (/)
- Set on user action (close button click)
- Checked on page load to respect user preferences

### CSS Implementation
- Dynamically generated based on settings
- Injected via `wp_add_inline_style`
- Responsive design with media queries
- z-index management for proper stacking
- Admin bar compatibility handling
- Conditional inclusion of countdown styles

## Code Organization Best Practices

### PHP Best Practices
1. **Function Prefixing**: All functions use the `hyroes_sticky_bar_` prefix to avoid namespace collisions
2. **Early Returns**: Using early exits to prevent unnecessary code execution
3. **Sanitization**: Comprehensive input sanitization before database storage
4. **Escaping**: Output escaping at the point of display, not storage
5. **Capability Checking**: Permission verification before displaying admin pages
6. **Nonce Verification**: CSRF protection for form submissions and AJAX
7. **Conditional Loading**: Assets only loaded when needed
8. **Inline Documentation**: PHPDoc blocks for all functions
9. **Structured Organization**: Clear separation of admin and front-end code
10. **Translation Support**: All user-facing strings are translation-ready

### JavaScript Best Practices
1. **Scope Isolation**: IIFE pattern to avoid global namespace pollution
2. **Event Delegation**: Proper event binding for better performance
3. **Consistent Error Handling**: Validation and fallbacks for all operations
4. **Memory Management**: Clearing intervals when no longer needed
5. **Caching DOM Selectors**: Storing frequently used selectors for efficiency
6. **Descriptive Variable Names**: Clear naming conventions
7. **Function Separation**: Single-responsibility principle for functions
8. **Inline Documentation**: JSDoc blocks for all functions
9. **Defensive Coding**: Checking for existence before operations
10. **Resource Cleanup**: Proper teardown on bar close or page unload

## Development Guidelines

### Adding New Features
1. Maintain the existing performance-first approach
2. Follow WordPress coding standards
3. Implement proper sanitization and escaping
4. Add appropriate hooks for extensibility
5. Document all changes comprehensively
6. Test thoroughly before integration

### Testing Considerations
1. Test with various WordPress versions
2. Verify mobile responsiveness
3. Check admin bar compatibility
4. Validate cookie functionality
5. Verify color picker operation
6. Test countdown functionality:
   - Accurate time calculations
   - Different positions
   - End actions
   - Time zone handling
   - Browser tab switching
   - Mobile device sleep mode
   - Caching plugin compatibility
   - Label display and positioning
   - Custom label text

### Performance Optimization
1. Assets are only loaded when needed
2. CSS is inlined to reduce HTTP requests
3. JavaScript is loaded in footer
4. Minimal DOM manipulation
5. Efficient cookie handling
6. AJAX requests limited to once per minute
7. Early exit patterns for quick non-applicable scenarios
8. Conditional code execution based on settings

## Hook Reference

### Actions
- `admin_init`: Settings registration
- `admin_menu`: Admin menu integration
- `admin_enqueue_scripts`: Admin asset loading
- `wp_enqueue_scripts`: Front-end asset loading
- `wp_footer`: Bar HTML injection
- `wp_ajax_hyroes_sticky_bar_update`: AJAX handler for logged-in users
- `wp_ajax_nopriv_hyroes_sticky_bar_update`: AJAX handler for non-logged-in users

### Filters
None currently implemented, but potential for:
- Bar text modification
- Cookie duration adjustment
- Style customization
- Countdown format customization
- Time zone adjustment
- Position customization
- Label text filters

## Version History and Changes

### Version 1.5.1 (March 4, 2025)
- Fixed settings saving issue by properly implementing WordPress Settings API
- Improved form field naming for better compatibility
- Enhanced error handling for settings submission
- Added proper admin notices for settings updates

### Version 1.5
- Added countdown timer functionality
- Added target date picker using HTML5 datetime-local input
- Added end action options (show zeros, remove countdown, remove bar)
- Added countdown position options (left, right, below)
- Added custom styling for countdown numbers
- Added label display options and customization
- Implemented AJAX updates for cache compatibility
- Added comprehensive inline documentation
- Enhanced security measures
- Fixed turning off countdown functionality

### Version 1.4
- Added configurable cookie duration setting
- Improved mobile responsiveness
- Fixed admin bar compatibility issues

### Version 1.3
- Added color picker for background customization
- Performance optimizations

## Future Development Considerations

### Potential Enhancements
1. Text color customization
2. Multiple bar support
3. Custom HTML support
4. Animation options
5. Position customization (top/bottom)
6. Advanced styling options
7. Template system for content
8. Countdown Enhancements:
   - Custom format options
   - Different time units (weeks, months)
   - Multiple countdown instances
   - Timezone selection
   - Animation effects
   - Sound notifications
   - Custom end actions
   - Recurring countdowns
   - Custom separator characters
   - Label styling options
   - Conditional display rules

### Known Limitations
1. HTML not supported in bar text
2. Fixed text color (white)
3. Single bar instance only
4. Limited animation options
5. Countdown Limitations:
   - Single countdown per bar
   - Limited to days/hours/minutes/seconds
   - No timezone selection
   - Basic end actions only
   - Fixed separators (:)

## Troubleshooting Guide

### Common Issues
1. Bar not showing
   - Check if enabled in settings
   - Verify cookie status
   - Check JavaScript errors
   - Ensure bar is not hidden by other elements

2. Style conflicts
   - Check z-index values
   - Verify CSS specificity
   - Review theme compatibility
   - Inspect element for overriding styles

3. Mobile display issues
   - Verify responsive CSS
   - Check admin bar handling
   - Test viewport settings
   - Check for media query conflicts

4. Countdown Issues
   - Incorrect time calculations
   - Browser tab switching delays
   - Mobile device sleep mode
   - Time zone mismatches
   - Position display problems
   - End action not triggering
   - Labels not displaying correctly
   - Cached page showing outdated countdown

### Debugging Tips
1. General Issues
   - Check JavaScript console for errors
   - Verify settings are saved correctly
   - Test in incognito/private browsing mode
   - Try multiple browsers for comparison

2. Countdown Specific
   - Validate the target date format
   - Test with shorter countdown durations
   - Monitor AJAX requests in browser developer tools
   - Check network tab for failed requests
   - Verify nonce is being passed correctly
   - Test with caching disabled temporarily
   - Add console.log statements to track data flow
   - Verify time calculations

3. CSS Issues
   - Use browser inspector to identify style conflicts
   - Test with theme's CSS disabled temporarily
   - Check for !important declarations overriding styles
   - Verify media queries are correctly applied

## Contributing Guidelines

When contributing to this plugin, please follow these guidelines:

1. **Code Standards**: Follow WordPress coding standards
2. **Documentation**: Update documentation for all changes
3. **Testing**: Test thoroughly on multiple environments
4. **Security**: Ensure all input is properly sanitized
5. **Internationalization**: Make all strings translatable
6. **Performance**: Maintain the performance-first approach
7. **Compatibility**: Test with latest WordPress version
8. **Accessibility**: Ensure features are accessible
9. **Mobile**: Test on various screen sizes
10. **Progressive Enhancement**: Ensure basic functionality without JavaScript 