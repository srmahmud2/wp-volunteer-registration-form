function validateForm() {
    var valid = true;
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

    // Validate volunteer_id 
    // console.log("Form Element: ", document.forms["volunteerForm"]);

    var volunteerId = document.forms["volunteerForm"]["volunteer_id"].value;
    if (!volunteerId || !/^\d+$/.test(volunteerId) || parseInt(volunteerId, 10) <= 0) {
        setError('volunteer_id', "Invalid Volunteer Number");
        valid = false;
    }


    var dataInscricao = document.forms["volunteerForm"]["data_inscricao"].value;
    if (dataInscricao === "") {
        setError('data_inscricao', "Inscrição date must be filled out");
        valid = false;
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(dataInscricao)) {
        setError('data_inscricao', "Invalid date format (YYYY-MM-DD expected)");
        valid = false;
    }

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

    // Validate phone number (telemovel)
    var telemovel = document.forms["volunteerForm"]["telemovel"].value;
    if (telemovel === "") {
        setError('telemovel', "Phone number must be filled out");
        valid = false;
    } else if (!/^\+?\d{10,15}$/.test(telemovel)) {
        setError('telemovel', "Invalid phone number format");
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

jQuery(document).ready(function($) {
    // Attach validation to form submission event in volunteer-ajax.js
    function attachFormValidation() {
        $('#volunteerForm').on('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    }
    attachFormValidation();

    handleServerSideErrors(); // Only if needed for initial load
});

// Updated to handle server-side errors from Ajax response
function handleServerSideErrors(errors) {
    for (var key in errors) {
        if (errors.hasOwnProperty(key)) {
            setError(key, errors[key]);
        }
    }
}
