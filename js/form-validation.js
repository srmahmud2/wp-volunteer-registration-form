function validateForm() {
    var valid = true;

    // Helper function for setting error messages
    function setError(id, message) {
        var errorElement = document.getElementById('error-' + id);
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    // Reset all error messages
    var errorElements = document.querySelectorAll('[id^="error-"]');
    errorElements.forEach(function(element) {
        element.textContent = "";
    });

    // Validate first name
    var firstName = document.forms["volunteerForm"]["first_name"].value;
    if (firstName === "") {
        setError('first_name', "First name must be filled out");
        valid = false;
    }

    // Validate last name
    var lastName = document.forms["volunteerForm"]["last_name"].value;
    if (lastName === "") {
        setError('last_name', "Last name must be filled out");
        valid = false;
    }

    // Validate email
    var email = document.forms["volunteerForm"]["volunteer_email"].value;
    if (email === "") {
        setError('volunteer_email', "Email must be filled out");
        valid = false;
    } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        setError('volunteer_email', "Invalid email format");
        valid = false;
    }

    // Validate 'data_inscricao' (date field)
    var dataInscricao = document.forms["volunteerForm"]["data_inscricao"].value;
    if (dataInscricao === "") {
        setError('data_inscricao', "Inscrição date must be filled out");
        valid = false;
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(dataInscricao)) {
        setError('data_inscricao', "Invalid date format (YYYY-MM-DD expected)");
        valid = false;
    }

    // Validate phone number (telemovel)
    var telemovel = document.forms["volunteerForm"]["telemovel"].value;
    if (telemovel === "") {
        setError('telemovel', "Phone number must be filled out");
        valid = false;
    } else if (!/^\+?\d{10,15}$/.test(telemovel)) {
        setError('telemovel', "Invalid phone number format");
        valid = false;
    }

    // Validate other text fields (post_code, morada, localidade, etc.)
    // Assuming these are text inputs that should not be empty
    var textFields = ['post_code', 'localidade'];
    textFields.forEach(function(field) {
        var fieldValue = document.forms["volunteerForm"][field].value;
        if (fieldValue === "") {
            setError(field, "This field must be filled out");
            valid = false;
        }
    });

    // Validate 'a_date'
    var aDate = document.forms["volunteerForm"]["a_date"].value;
    if (aDate === "") {
        document.getElementById('error-a_date').textContent = "A_date must be filled out";
        valid = false;
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(aDate)) {
        document.getElementById('error-a_date').textContent = "Invalid date format (YYYY-MM-DD expected)";
        valid = false;
    } else {
        document.getElementById('error-a_date').textContent = "";
    }

    return valid;
}

// Function to display server-side error messages
function displayServerSideErrors() {
    // Function to get query parameters
    function getQueryParam(param) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Get the error messages from the query parameter
    var errors = getQueryParam('errors');
    if (errors) {
        try {
            var errorMessages = JSON.parse(decodeURIComponent(errors));
            for (var key in errorMessages) {
                if (errorMessages.hasOwnProperty(key)) {
                    setError(key, errorMessages[key]);
                }
            }
        } catch (e) {
            console.error('Error parsing server-side validation messages:', e);
        }
    }
}

// Call function to display server-side error messages
displayServerSideErrors();