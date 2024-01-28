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


