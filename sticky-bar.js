/**
 * Hyroes Sticky Bar
 * Lightweight script for managing the sticky bar notification
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        var cookieName = 'HyroesStickyBarClosed';
        
        // Don't proceed if cookie exists
        if (getCookie(cookieName)) {
            return;
        }
        
        // Mark body for spacing
        $('body').addClass('has-hyroes-sticky-bar');
        
        // Show the bar
        $('#hyroes-sticky-bar').fadeIn(300);
        
        // Handle close button
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
     * @param {string} name - Cookie name
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
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number} hours - Hours until expiration
     */
    function setCookie(name, value, hours) {
        var date = new Date();
        date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
        var expires = '; expires=' + date.toUTCString();
        document.cookie = name + '=' + value + expires + '; path=/';
    }
})(jQuery);