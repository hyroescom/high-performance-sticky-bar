/**
 * Lightweight High Performance Sticky Bar - Client-side functionality
 * 
 * This script handles the front-end behavior of the sticky notification bar:
 * - Shows/hides the sticky bar with smooth animations
 * - Sets and checks cookies to remember user preferences
 * - Adjusts page layout when the bar is visible or hidden
 * - Handles countdown functionality with caching support
 * 
 * @package Hyroes-Sticky-Bar
 * @version 1.5
 */
(function($) {
    'use strict';
    
    // Global variables for countdown functionality
    let countdownInterval = null; // Stores the interval timer for countdown updates
    let targetDate = null;        // Stores the target date timestamp in milliseconds
    let countdownAction = null;   // Stores the action to take when countdown ends
    
    $(document).ready(function() {
        // Define cookie name for storing visitor preferences - keeps bar hidden for returning visitors
        var cookieName = 'HyroesStickyBarClosed';
        
        // Don't display the bar if the visitor has previously closed it - early exit pattern
        if (getCookie(cookieName)) {
            return;
        }

        // Initialize countdown if enabled in admin settings
        if (HyroesStickyBarData.countdownEnabled) {
            initializeCountdown();
        }

        // Mark body for spacing - this adds padding to prevent content from being hidden
        $('body').addClass('has-hyroes-sticky-bar');
        
        // Show the bar with a smooth fade-in animation
        $('#hyroes-sticky-bar').fadeIn(300);
        
        // Handle close button click event - triggered when user clicks X
        $('#hyroes-sticky-bar-close').on('click', function() {
            // Hide the bar with fade-out animation and remove padding when complete
            $('#hyroes-sticky-bar').fadeOut(300, function() {
                $('body').removeClass('has-hyroes-sticky-bar');
            });
            
            // Set cookie to remember closed state for this visitor
            // This prevents the bar from showing again until cookie expires
            setCookie(cookieName, 'closed', HyroesStickyBarData.cookieHours);
            
            // Stop countdown updates to prevent memory leaks and unnecessary processing
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        });
    });
    
    /**
     * Initialize countdown functionality with caching support
     * 
     * This function sets up the countdown display and updates, handling:
     * - Initial setup of countdown target date
     * - Regular updates of the countdown timer (every second)
     * - Periodic AJAX updates to handle cached pages (every minute)
     * - Different end actions (show zeros, remove bar, remove countdown)
     */
    function initializeCountdown() {
        // First get the target date from the localized data passed from PHP
        targetDate = new Date(HyroesStickyBarData.countdownTargetDate).getTime();
        countdownAction = HyroesStickyBarData.countdownAction;
        
        /**
         * Update countdown data from server via AJAX
         * 
         * This function is critical for cache compatibility:
         * - Cached pages will still show accurate countdown time
         * - Server provides fresh data regardless of page cache
         * - Security handled via nonce verification
         */
        function updateCountdownData() {
            $.ajax({
                url: HyroesStickyBarData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'hyroes_sticky_bar_update', // WordPress AJAX action hook
                    nonce: HyroesStickyBarData.nonce    // Security nonce for verification
                },
                success: function(response) {
                    if (response.success) {
                        // Update values with fresh data from server
                        targetDate = new Date(response.data.target_date).getTime();
                        countdownAction = response.data.action;
                        // Force immediate update of countdown display
                        updateCountdown();
                    }
                }
            });
        }
        
        // Update countdown data every minute to handle caching
        // This interval is separate from the display update interval
        setInterval(updateCountdownData, 60000); // 60,000 ms = 1 minute
        
        /**
         * Update countdown time calculations and display
         * 
         * This function:
         * - Calculates the time remaining until target date
         * - Handles the countdown end actions when time expires
         * - Updates the display with new values
         */
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            // Check if countdown has ended (current time is past target date)
            if (distance < 0) {
                // Stop the interval to prevent unnecessary updates
                clearInterval(countdownInterval);
                
                // Handle different end actions based on settings
                if (countdownAction === 'remove') {
                    // Remove the entire bar when countdown ends
                    $('#hyroes-sticky-bar').fadeOut(300, function() {
                        $('body').removeClass('has-hyroes-sticky-bar');
                    });
                } else if (countdownAction === 'remove_countdown') {
                    // Remove only the countdown element, keep the bar
                    $('.hyroes-countdown').fadeOut(300);
                } else {
                    // Default: show zeros for all time units
                    updateDisplay(0, 0, 0, 0);
                }
                return;
            }
            
            // Calculate time units from milliseconds remaining
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Update the display with calculated values
            updateDisplay(days, hours, minutes, seconds);
        }
        
        /**
         * Updates the DOM with new countdown values
         * 
         * This function updates all time unit displays with proper formatting
         * 
         * @param {number} days - Days remaining
         * @param {number} hours - Hours remaining
         * @param {number} minutes - Minutes remaining
         * @param {number} seconds - Seconds remaining
         */
        function updateDisplay(days, hours, minutes, seconds) {
            // Update each number display with properly padded values
            $('.hyroes-countdown-number.days').text(padNumber(days));
            $('.hyroes-countdown-number.hours').text(padNumber(hours));
            $('.hyroes-countdown-number.minutes').text(padNumber(minutes));
            $('.hyroes-countdown-number.seconds').text(padNumber(seconds));
        }
        
        // Initialize: update immediately to avoid blank display
        updateCountdown();
        // Set interval for regular updates (every second)
        countdownInterval = setInterval(updateCountdown, 1000);
    }
    
    /**
     * Pad a number with leading zeros
     * 
     * Ensures all displayed numbers have at least 2 digits for consistent formatting
     * 
     * @param {number} num - The number to pad
     * @returns {string} - The padded number (e.g. 1 -> "01", 10 -> "10")
     */
    function padNumber(num) {
        return num.toString().padStart(2, '0');
    }
    
    /**
     * Get cookie value
     * 
     * Parses the document.cookie string to find a specific named cookie
     * 
     * @param {string} name - The name of the cookie to retrieve
     * @return {string|null} - Cookie value or null if not found
     */
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            // Remove whitespace at the beginning of the cookie entry
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            // If this cookie is the one we're looking for, return its value
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        // Cookie not found
        return null;
    }
    
    /**
     * Set cookie with expiration
     * 
     * Creates a cookie with the specified name, value, and expiration time.
     * The cookie is set with path=/ to ensure it works across the entire site.
     * 
     * @param {string} name - The name of the cookie to set
     * @param {string} value - The value to store in the cookie
     * @param {number} hours - Number of hours until the cookie expires
     */
    function setCookie(name, value, hours) {
        var date = new Date();
        // Calculate expiration time in milliseconds
        date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
        var expires = '; expires=' + date.toUTCString();
        // Set the cookie with expiration and site-wide path
        document.cookie = name + '=' + value + expires + '; path=/';
    }
})(jQuery);