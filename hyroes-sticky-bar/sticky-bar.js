// Use jQuery for convenience
(function($) {
    $(document).ready(function() {
        var cookieName = 'HyroesStickyBarClosed';
        
        // Check if user closed the sticky bar
        if (!getCookie(cookieName)) {
            var $bar = $('#hyroes-sticky-bar').hide();
            
            // Apply style from localized script data
            $bar.css({
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'right': '0',
                'background-color': HyroesStickyBarData.bgColor,
                'color': '#fff',
                'padding': '10px',
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'center',
                'z-index': '9999',
                'opacity': '0',
                'transform': 'translateY(-100%)',
                'transition': 'opacity 0.3s ease, transform 0.3s ease'
            });
            
            // Create content first before showing for better performance
            // Add text container
            var $content = $('<div></div>').css({
                'width': '100%',
                'text-align': 'center',
                'margin': '0 auto'
            }).text(HyroesStickyBarData.barText);
            
            var $closeButton = $('<span></span>').text('×').css({
                'display': 'block',
                'cursor': 'pointer',
                'font-size': '1.25em',
                'position': 'absolute',
                'right': '15px',
                'top': '50%', 
                'transform': 'translateY(-50%)'
            });
            
            $closeButton.on('click', function() {
                // Animate out
                $bar.css({
                    'opacity': '0',
                    'transform': 'translateY(-100%)'
                });
                
                // Set cookie with expiration days from settings
                setCookie(cookieName, 'true', HyroesStickyBarData.cookieDays);
                
                // Remove after animation completes
                setTimeout(function() {
                    $bar.hide();
                }, 300);
            });
            
            // Append elements to the bar
            $bar.append($content).append($closeButton);
            
            // Show with animation after a tiny delay for better rendering
            setTimeout(function() {
                $bar.show().css({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                });
            }, 10);
        }
        
        // Helper function to set a cookie with expiration
        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
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
    });
})(jQuery);