/**
 * Lightweight High Performance Sticky Bar - Client-side functionality
 * 
 * This script handles the front-end behavior of the sticky notification bar:
 * - Shows/hides the sticky bar with smooth animations
 * - Sets and checks cookies to remember user preferences
 * - Adjusts page layout when the bar is visible or hidden
 * 
 * @package Hyroes-Sticky-Bar
 * @version 1.4
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Define cookie name for storing visitor preferences
        var cookieName = 'HyroesStickyBarClosed';
        
        // Don't display the bar if the visitor has previously closed it
        // (checks if the cookie exists indicating previous interaction)
        if (getCookie(cookieName)) {
            return;
        }

        // The bar variables are passed from PHP using wp_localize_script
        // and available through the global HyroesStickyBarData object
        
        // Mark body for spacing
        $('body').addClass('has-hyroes-sticky-bar');
        
        // Show the bar
        $('#hyroes-sticky-bar').fadeIn(300);
        
        // Handle close button click event
        $('#hyroes-sticky-bar-close').on('click', function() {
            $('#hyroes-sticky-bar').fadeOut(300, function() {
                $('body').removeClass('has-hyroes-sticky-bar');
            });
            
            // Set cookie to remember closed state
            setCookie(cookieName, 'closed', HyroesStickyBarData.cookieHours);
        });
    });
    
    /**
     * Get cookie value
     * 
     * @param {string} name - The name of the cookie to retrieve
     * @return {string|null} - Cookie value or null if not found
     */
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
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
        date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
        var expires = '; expires=' + date.toUTCString();
        document.cookie = name + '=' + value + expires + '; path=/';
    }
})(jQuery);