# Lightweight High Performance Sticky Bar

A lightweight, high-performance WordPress plugin that adds a customizable sticky notification bar to your website with minimal performance impact. Perfect for announcements, promotions, or important messages that need visibility without disrupting the user experience.

The sticky bar appears at the top of your website and includes a close button. When a visitor closes the bar, their preference is remembered using cookies for the duration you specify.

## Features

- **Smooth Animations**: Clean transitions when showing and hiding the bar
- **Customizable Text**: Set your own message to display
- **Color Picker**: Choose any background color with the built-in WordPress color picker
- **Cookie Integration**: Bar stays hidden for visitors who close it
- **Configurable Duration**: Set how many hours the bar stays hidden after being closed
- **Admin Bar Compatible**: Automatically adjusts position when WordPress admin bar is present
- **Content-Friendly**: Adds padding to avoid covering your website content
- **Lightweight**: Minimal impact on page load times and performance
- **Mobile-Friendly**: Fully responsive design that works on all devices
- **No Dependencies**: Doesn't require any external libraries beyond jQuery (included with WordPress)

## Installation

### Automatic Installation (Recommended)
1. Log in to your WordPress dashboard and navigate to Plugins → Add New
2. Search for "Lightweight High Performance Sticky Bar"
3. Click "Install Now" and then "Activate"

### Manual Installation
1. Download the plugin zip file from WordPress.org repository
2. Log in to your WordPress dashboard and navigate to Plugins → Add New
3. Click the "Upload Plugin" button at the top of the page
4. Choose the downloaded zip file and click "Install Now"
3. Select a background color using the color picker
4. Set the number of hours the bar should stay hidden after a visitor closes it
5. Check "Enable Sticky Bar" to activate
6. Save your settings

## Technical Details

The sticky bar appears at the top of your website as the first element in the header. It includes:

- **Smart Positioning**: Detects and adjusts for the WordPress admin bar when logged in
- **Body Padding**: Automatically adds padding to prevent content from being hidden
- **Responsive Layout**: Adapts to all screen sizes from mobile to desktop
- **Performance Optimized**: JavaScript and CSS are minified and only loaded when needed
- **Cookie-Based Memory**: Respects user preferences by remembering when they've dismissed the bar

### Configuration Options

| Setting | Description | Default |
|---------|-------------|---------|
| Bar Text | The message displayed in the sticky bar | "Welcome to our site!" |
| Background Color | Color picker for the bar background | #333333 |
| Hide Duration | Hours the bar stays hidden after closing | 24 |
| Enable Bar | Toggle to activate/deactivate the bar | Disabled |

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- JavaScript enabled in the browser
- Cookies enabled for remembering user preferences

## Frequently Asked Questions

### Can I add HTML to the sticky bar text?

No, for security reasons the plugin sanitizes all input to prevent XSS attacks. Only plain text is supported.

### Will the sticky bar appear on mobile devices?

Yes, the sticky bar is fully responsive and will display properly on all devices, from smartphones to desktop computers.

### Does this plugin slow down my website?

No, the plugin is designed to be extremely lightweight. The JavaScript and CSS are minimal, and they're only loaded when the sticky bar is enabled in your settings.

### How can I change the text color?

Currently, the text color is set to white (#ffffff) for maximum readability. A future update may include an option to customize the text color.

## Screenshots

1. **Admin Settings Page** - The settings page under Tools → Lightweight High Performance Sticky Bar
2. **Sticky Bar in Action** - How the sticky bar appears on your website
3. **Color Picker** - Selecting a custom background color

*Note: Screenshots are stored in the /assets directory in the WordPress SVN repository, not in the plugin itself.*

## Changelog

### 1.4
* Added configurable cookie duration setting
* Improved mobile responsiveness
* Fixed admin bar compatibility issues

### 1.3
* Added color picker for background customization
* Performance optimizations

## Credits

Developed by Alex Godlewski, [Hyroes.com](https://hyroes.com)

## Privacy

This plugin sets a cookie (HyroesStickyBarClosed) in the visitor's browser when they close the sticky bar. This cookie only stores a simple "closed" status and does not collect any personal information or track users.

The cookie expires after the configured number of hours (default: 24 hours). No data is sent to external servers.