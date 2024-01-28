(function($) {

    // Function to display the loading spinner
    window.showSpinner = function() {
        $('#spinner').show();
    };

    // Function to hide the loading spinner
    window.hideSpinner = function() {
        $('#spinner').hide();
    };

    // Function to show a message with a zoom-out effect
    window.showMessageWithZoomOutEffect = function(message, messageType) {
        var messageDiv = messageType === 'error' ? $('#form-errors') : $('#form-success');

        // Set the message and show the div with initial scale
        messageDiv.html(message).show().css('transform', 'scale(1)');

        // Apply the zoom-out effect and then hide the message div
        setTimeout(function() {
            messageDiv.css('transform', 'scale(0)').fadeOut(1000, function() {
                $(this).hide().css('transform', 'scale(1)');
            });
        }, 5000); // Duration before the zoom-out effect starts
    };

})(jQuery);
