// Use jQuery for convenience
(function($) {
    $(document).ready(function() {
        var cookieName = 'HyroesStickyBarClosed';
        
        // Check if user closed the sticky bar
        if (!getCookie(cookieName)) {
            var $bar = $('#hyroes-sticky-bar');
            
            // Ensure we have the element (it was added via jQuery in footer)
            if ($bar.length) {
                // Apply style from localized script data
                $bar.css({
                    'width': '100%',
                    'background-color': HyroesStickyBarData.bgColor,
                    'color': '#fff',
                    'padding': '10px',
                    'display': 'flex',
                    'align-items': 'center',
                    'justify-content': 'center',
                    'opacity': '0',
                    'transition': 'opacity 0.3s ease, height 0.3s ease',
                    'box-sizing': 'border-box',
                    'overflow': 'hidden',
                    'height': 'auto',
                    'position': 'relative'
                });
                
                // Create content first before showing for better performance
                // Add text container
                var $content = $('<div></div>').css({
                    'width': '100%',
                    'text-align': 'center',
                    'margin': '0 auto',
                    'word-wrap': 'break-word', // Ensure text wraps properly on mobile
                    'white-space': 'normal'
                }).text(HyroesStickyBarData.barText);
                
                var $closeButton = $('<span></span>').text('×').css({
                    'display': 'block',
                    'cursor': 'pointer',
                    'font-size': '1.5em',
                    'position': 'absolute',
                    'right': '15px',
                    'top': '50%', 
                    'transform': 'translateY(-50%)',
                    'line-height': '1',
                    'padding': '0 5px'
                });
                
                $closeButton.on('click', function() {
                    // Get the current height so we can animate to zero
                    var currentHeight = $bar.outerHeight();
                    
                    // First fade out
                    $bar.css({
                        'opacity': '0'
                    });
                    
                    // Set cookie with expiration
                    setCookie(cookieName, 'true', 0, HyroesStickyBarData.cookieHours);
                    
                    // After fade completes, collapse height
                    setTimeout(function() {
                        $bar.css({
                            'height': '0',
                            'padding': '0',
                            'margin': '0',
                            'border': 'none'
                        });
                        
                        // Remove completely after animation
                        setTimeout(function() {
                            $bar.remove();
                        }, 300);
                    }, 250);
                });
                
                // Append elements to the bar
                $bar.append($content).append($closeButton);
                
                // Show with animation after a delay for better rendering
                setTimeout(function() {
                    $bar.css({
                        'opacity': '1'
                    });
                }, 10);
            }
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