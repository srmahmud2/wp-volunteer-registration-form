// registration.js

// Function to update the registration message on the page
function updateRegistrationMessage(message) {
    var messageElement = document.getElementById('registration-message');
    if (messageElement) {
        // Sanitize message
        messageElement.textContent = message;
    }
}

// Check if the script tag has a data-message attribute
var scriptTag = document.querySelector('script[src*="registration.js"]');
if (scriptTag && scriptTag.dataset.message) {
    var registrationMessage = scriptTag.dataset.message;
    // Call the function with the message passed from PHP
    updateRegistrationMessage(registrationMessage);
}
