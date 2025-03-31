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
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'center',
                'z-index': '9999'
            });
            
            // Add text and close button container
            var $content = $('<div></div>').css({
                'flex': '1',
                'text-align': 'center'
            }).text(HyroesStickyBarData.barText);
            
            var $closeButton = $('<span></span>').text('×').css({
                'cursor': 'pointer',
                'font-size': '1.25em',
                'margin-left': '10px'
            });
            
            $closeButton.on('click', function() {
                $bar.hide();
                // Set cookie
                document.cookie = 'HyroesStickyBarClosed=true; path=/';
            });
            
            $bar.append($content).append($closeButton);
        }
    });
})(jQuery);