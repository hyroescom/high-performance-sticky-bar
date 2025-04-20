=== Lightweight High Performance Sticky Bar ===
Contributors: alexgodlewski
World Class Websites: https://hyroes.com
Tags: notification bar, sticky bar, countdown timer, announcement bar, promotion bar
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.0
Stable tag: 1.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a customizable sticky notification bar with countdown functionality to your website with minimal performance impact.

== Description ==

The Lightweight High Performance Sticky Bar is perfect for announcements, promotions, or important messages that need visibility without disrupting the user experience. It adds a customizable sticky bar to the top of your website with options for a countdown timer.

= Features =

* **Smooth Animations**: Clean transitions when showing and hiding the bar
* **Customizable Text**: Set your own message to display
* **Color Picker**: Choose any background color with the built-in WordPress color picker
* **Cookie Integration**: Bar stays hidden for visitors who close it
* **Configurable Duration**: Set how many hours the bar stays hidden after being closed
* **Admin Bar Compatible**: Automatically adjusts position when WordPress admin bar is present
* **Content-Friendly**: Adds padding to avoid covering your website content
* **Lightweight**: Minimal impact on page load times and performance
* **Mobile-Friendly**: Fully responsive design that works on all devices
* **No Dependencies**: Doesn't require any external libraries beyond jQuery (included with WordPress)
* **Countdown Timer**: Display a countdown to a specific date and time
* **Cache-Compatible**: Works with page caching plugins
* **Customizable Countdown**: Position, colors, labels, and end actions

== Installation ==

= Automatic Installation (Recommended) =
1. Log in to your WordPress dashboard and navigate to Plugins → Add New
2. Search for "Lightweight High Performance Sticky Bar"
3. Click "Install Now" and then "Activate"

= Manual Installation =
1. Download the plugin zip file from WordPress.org repository
2. Log in to your WordPress dashboard and navigate to Plugins → Add New
3. Click the "Upload Plugin" button at the top of the page
4. Choose the downloaded zip file and click "Install Now"
5. Configure your settings under Tools → Sticky Bar
6. Save your settings

== Technical Details ==

The sticky bar appears at the top of your website as the first element in the header. It includes:

* **Smart Positioning**: Detects and adjusts for the WordPress admin bar when logged in
* **Body Padding**: Automatically adds padding to prevent content from being hidden
* **Responsive Layout**: Adapts to all screen sizes from mobile to desktop
* **Performance Optimized**: JavaScript and CSS are minified and only loaded when needed
* **Cookie-Based Memory**: Respects user preferences by remembering when they've dismissed the bar
* **Countdown Feature**: Display days, hours, minutes, and seconds remaining until a target date
* **AJAX Updates**: Ensures countdown accuracy even with cached pages

= Configuration Options =

* **Bar Text**: The message displayed in the sticky bar
* **Background Color**: Color picker for the bar background
* **Hide Duration**: Hours the bar stays hidden after closing
* **Enable Bar**: Toggle to activate/deactivate the bar
* **Enable Countdown**: Toggle to enable the countdown timer
* **Target Date**: The date and time that the countdown targets
* **After Countdown Ends**: What happens when countdown reaches zero (Show Zeros, Remove Countdown, or Remove Bar)
* **Countdown Position**: Where to display the countdown relative to text
* **Numbers Background**: Background color for countdown digits
* **Numbers Font Color**: Text color for countdown digits
* **Show Labels**: Toggle to show/hide time unit labels (Days, Hours, etc.)
* **Labels Position**: Display labels above or below the numbers
* **Custom Labels**: Customize the text for each time unit label

== Requirements ==

* WordPress 5.0 or higher
* PHP 7.4 or higher
* JavaScript enabled in the browser
* Cookies enabled for remembering user preferences

== Frequently Asked Questions ==

= Can I add HTML to the sticky bar text? =

Yes, the plugin now supports limited HTML in the sticky bar text. You can enable this feature in the settings and use tags like links, line breaks, emphasis, and spans. All HTML is sanitized to prevent security issues.

= Will the sticky bar appear on mobile devices? =

Yes, the sticky bar is fully responsive and will display properly on all devices, from smartphones to desktop computers.

- **Smart Positioning**: Detects and adjusts for the WordPress admin bar when logged in
- **Body Padding**: Automatically adds padding to prevent content from being hidden
- **Responsive Layout**: Adapts to all screen sizes from mobile to desktop
- **Performance Optimized**: JavaScript and CSS are minified and only loaded when needed
- **Cookie-Based Memory**: Respects user preferences by remembering when they've dismissed the bar
- **Countdown Feature**: Display days, hours, minutes, and seconds remaining until a target date
- **AJAX Updates**: Ensures countdown accuracy even with cached pages

### Configuration Options

| Setting | Description | Default |
|---------|-------------|---------|
| Bar Text | The message displayed in the sticky bar | "Welcome to our site!" |
| Background Color | Color picker for the bar background | #333333 |
| Hide Duration | Hours the bar stays hidden after closing | 24 |
| Enable Bar | Toggle to activate/deactivate the bar | Disabled |
| Enable Countdown | Toggle to enable the countdown timer | Disabled |
| Target Date | The date and time that the countdown targets | Empty |
| After Countdown Ends | What happens when countdown reaches zero | Show Zeros, Remove Countdown, or Remove Bar |
| Countdown Position | Where to display the countdown relative to text | Right of Text |
| Numbers Background | Background color for countdown digits | #000000 |
| Numbers Font Color | Text color for countdown digits | #FFFFFF |
| Show Labels | Toggle to show/hide time unit labels (Days, Hours, etc.) | Enabled |
| Labels Position | Display labels above or below the numbers | Below Numbers |
| Custom Labels | Customize the text for each time unit label | "Days", "Hours", "Minutes", "Seconds" |

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- JavaScript enabled in the browser
- Cookies enabled for remembering user preferences

## Frequently Asked Questions

### Can I add HTML to the sticky bar text?

Yes, the plugin now supports limited HTML in the sticky bar text. You can enable this feature in the settings and use tags like links, line breaks, emphasis, and spans. All HTML is sanitized to prevent security issues.

### Will the sticky bar appear on mobile devices?

Yes, the sticky bar is fully responsive and will display properly on all devices, from smartphones to desktop computers.

### Does this plugin slow down my website?

No, the plugin is designed to be extremely lightweight. The JavaScript and CSS are minimal, and they're only loaded when the sticky bar is enabled in your settings.

### How can I change the text color?

Currently, the text color is set to white (#ffffff) for maximum readability. A future update may include an option to customize the text color.

### Does the countdown work with caching plugins?

Yes, the plugin uses AJAX to periodically update the countdown data, ensuring accuracy even with page caching enabled.

### Can I customize the countdown labels?

Yes, you can customize the text for each time unit label (Days, Hours, Minutes, Seconds) and choose whether to display them above or below the numbers.

## Screenshots

1. **Admin Settings Page** - The settings page under Tools → Lightweight High Performance Sticky Bar
2. **Sticky Bar in Action** - How the sticky bar appears on your website
3. **Color Picker** - Selecting a custom background color
4. **Countdown Timer** - Countdown timer in action with custom styling

*Note: Screenshots are stored in the /assets directory in the WordPress SVN repository, not in the plugin itself.*

## Changelog

### 1.5.2
* Added support for HTML in sticky bar text
* Added customizable button with position, color, and link options
* Improved performance with minified CSS and deferred JavaScript
* Enhanced security with proper escaping and validation
* Added compatibility checks for WordPress version
* Fixed text domain consistency issues
* Added proper uninstall cleanup

### 1.5.1
* Fixed settings saving issue
* Improved settings form field compatibility
* Enhanced error handling for settings updates
* Added proper admin notices

### 1.5
* Added countdown timer functionality
* Added option to customize countdown position
* Added countdown styling options
* Added countdown end action options
* Added compatibility with caching plugins
* Added customizable labels for time units
* Added label position options
* Fixed turning off countdown functionality

### 1.4
* Added configurable cookie duration setting
* Improved mobile responsiveness
* Fixed admin bar compatibility issues

### 1.3
* Added color picker for background customization
* Performance optimizations

## License
This plugin is licensed under the GPLv2 or later.

## Credits

Developed by Alex Godlewski, [Hyroes.com](https://hyroes.com)

## Privacy

This plugin sets a cookie (HyroesStickyBarClosed) in the visitor's browser when they close the sticky bar. This cookie only stores a simple "closed" status and does not collect any personal information or track users.

The cookie expires after the configured number of hours (default: 24 hours). No data is sent to external servers except for the AJAX requests used to update the countdown timer, which do not contain any personal information.