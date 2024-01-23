// Function to show message with zoom-out effect
// Make any function globally available for wp.
(function($) {

    //function to display loading
    window.showSpinner = function() {
        $('#spinner').show();
    };
    
    window.hideSpinner = function() {
        $('#spinner').hide();
    };
    
    // Define your function here
    window.showMessageWithZoomOutEffect = function(message, messageType) {
        var messageDiv = messageType === 'error' ? $('#form-errors') : $('#form-success');
        messageDiv.html(message).show().css('transform', 'scale(1)');

        setTimeout(function() {
            messageDiv.css('transform', 'scale(0)').fadeOut(1000, function() {
                $(this).hide().css('transform', 'scale(1)');
            });
        }, 500); // Hide the message after 0.5 seconds
    };
})(jQuery);



// Function to update the registration message on the page
function updateRegistrationMessage(message) {
    var messageElement = document.getElementById('registration-message');
    if (messageElement) {
        // Sanitize message
        messageElement.textContent = message;
    }
}

// Attach this function to be called after Ajax submission in volunteer-ajax.js
function handleServerSideErrors(response) {
    if (response && response.message) {
        updateRegistrationMessage(response.message);
    }
}


