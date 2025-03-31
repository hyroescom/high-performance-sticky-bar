// Use jQuery for convenience
(function($) {
    $(document).ready(function() {
        var cookieName = 'HyroesStickyBarClosed';
        
        // Check if user closed the sticky bar
        if (!getCookie(cookieName)) {
            var $bar = $('#hyroes-sticky-bar');
            
            // Ensure we have the element
            if ($bar.length) {
                // Apply style from localized script data
                $bar.css({
                    'background-color': HyroesStickyBarData.bgColor,
                    'color': '#fff',
                    'display': 'flex',
                    'align-items': 'center',
                    'justify-content': 'center',
                    'opacity': '0',
                    'box-sizing': 'border-box',
                    'overflow': 'hidden',
                    'position': 'fixed',
                    'top': $('body').hasClass('admin-bar') ? ($('#wpadminbar').height() + 'px') : '0',
                    'left': '0',
                    'width': '100%',
                    'z-index': '999999',
                    'height': '40px', // Fixed height to avoid layout shifts
                    'margin': '0'
                });
                
                // Create content first before showing for better performance
                // Add text container
                var $content = $('<div></div>').css({
                    'width': '100%',
                    'text-align': 'center',
                    'margin': '0 auto',
                    'padding': '0 30px', // Make space for close button
                    'word-wrap': 'break-word', // Ensure text wraps properly on mobile
                    'white-space': 'normal'
                }).text(HyroesStickyBarData.barText);
                
                var $closeButton = $('<span></span>').text('×').css({
                    'display': 'block',
                    'cursor': 'pointer',
                    'font-size': '24px',
                    'position': 'absolute',
                    'right': '15px',
                    'top': '50%', 
                    'transform': 'translateY(-50%)',
                    'line-height': '1',
                    'padding': '0 5px',
                    'font-weight': 'bold'
                });
                
                $closeButton.on('click', function() {
                    // First fade out
                    $bar.css({
                        'opacity': '0'
                    });
                    
                    // Set cookie with expiration
                    setCookie(cookieName, 'true', 0, HyroesStickyBarData.cookieHours);
                    
                    // After fade completes, collapse height and remove
                    setTimeout(function() {
                        document.body.classList.remove('has-hyroes-sticky-bar');
                        $bar.remove();
                    }, 300);
                });
                
                // Append elements to the bar
                $bar.append($content).append($closeButton);
                
                // Show with animation after a delay for better rendering
                setTimeout(function() {
                    $bar.css({
                        'opacity': '1'
                    });
                }, 100);
                
                // Handle window resize for admin bar height adjustments
                $(window).on('resize', function() {
                    if ($('body').hasClass('admin-bar') && $('#wpadminbar').length) {
                        $bar.css('top', $('#wpadminbar').height() + 'px');
                    }
                });
            }
        } else {
            // If the bar should be hidden due to cookie, remove the body class
            document.body.classList.remove('has-hyroes-sticky-bar');
        }
    });
    
    // Helper function to set a cookie with expiration
    function setCookie(name, value, days, hours) {
        var expires = '';
        var date = new Date();
        
        // Calculate expiration time
        if (days && days > 0) {
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        } else if (hours && hours > 0) {
            date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
        } else {
            // Default: 1 day if neither specified
            date.setTime(date.getTime() + (24 * 60 * 60 * 1000));
        }
        
        expires = '; expires=' + date.toUTCString();
        
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }
    
    // Helper function to get a cookie value
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
})(jQuery);