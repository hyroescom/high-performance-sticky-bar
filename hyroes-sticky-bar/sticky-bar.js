// Use jQuery for convenience
(function($) {
    $(document).ready(function() {
        // Check if user closed the sticky bar
        if (document.cookie.indexOf('HyroesStickyBarClosed=true') === -1) {
            var $bar = $('#hyroes-sticky-bar');
            
            // Apply style from localized script data
            $bar.css({
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'right': '0',
                'background-color': HyroesStickyBarData.bgColor,
                'color': '#fff',
                'padding': '10px',
                'text-align': 'center',
                'z-index': '9999',
                'display': 'block'
            });
            
            // Add text and close button
            var closeButton = $('<span></span>').text('×').css({
                'cursor': 'pointer',
                'float': 'right',
                'font-size': '1.25em',
                'margin-right': '10px'
            });
            
            closeButton.on('click', function() {
                $bar.hide();
                // Set cookie
                document.cookie = 'HyroesStickyBarClosed=true; path=/';
            });
            
            $bar.html(HyroesStickyBarData.barText).append(closeButton);
        }
    });
})(jQuery);