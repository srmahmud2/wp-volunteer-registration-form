jQuery(document).ready(function($) {
    // Function to determine if the form is in edit mode
    function isEditMode() {
        var urlParams = new URLSearchParams(window.location.search);
        var myId = urlParams.get('id'); // Replace 'id' with your parameter name if different
        return myId && !isNaN(myId) && parseInt(myId, 10) > 0;
        // var editMode = myId && !isNaN(myId) && parseInt(myId, 10) > 0;
        // console.log("isEditMode check:", editMode, "myId:", myId); // Debugging log
        // return editMode;
    }


    // Common function to show/hide spinner
    const toggleSpinner = (show) => {
        show ? showSpinner() : hideSpinner();
    };
    // Common function to display error messages
    const displayErrorMessage = (field, message, addClass) => {
        const messageElement = field.siblings('.error-message');
        messageElement.text(message);
        if (addClass) {
            field.addClass('success-message');
        } else {
            field.removeClass('success-message');
        }
    };
    // Helper Function for fetchAndPopulateVolunteerData() to populate form fields with current id's data
    function populateFormWithData(data) {
        // Populate the form fields with the data
        $('#volunteer_id').val(data.volunteer_id);
        $('#data_inscricao').val(data.data_inscricao);
        $('#first_name').val(data.first_name);
        $('#last_name').val(data.last_name);
        $('#post_code').val(data.post_code);
        $('#morada').val(data.morada);
        $('#localidade').val(data.localidade);
        $('#telemovel').val(data.telemovel);
        // $('#volunteer_email').val(data.volunteer_email);
        $('#volunteer_email').val(data.volunteer_email).data('original-email', data.volunteer_email);
        $('#education').val(data.education);
        $('#profession').val(data.profession);
        $('#encaminhado').val(data.encaminhado);
        $('#a_date').val(data.a_date);
        $('#pref1').val(data.pref1);
        $('#pref2').val(data.pref2);
        $('#pref3').val(data.pref3);
        $('#pref_other').val(data.pref_other);
    }
    // Function to check if the email has been changed
    function isEmailChanged() {
        var currentEmail = $('#volunteer_email').val();
        var originalEmail = $('#volunteer_email').data('original-email');
        return currentEmail !== originalEmail;
    }
    // AJAX call to check volunteer_id uniqueness
    // Refactored function using async/await for volunteer ID uniqueness check
    const checkVolunteerIdUniqueness = async (value) => {

        if (isEditMode()) {
            return { isValid: true };
        }
        try {
            toggleSpinner(true);
            const response = await $.ajax({
                url: volunteer_realtime_obj.ajaxurl,
                method: 'POST',
                data: {
                    action: 'check_volunteer_id_uniqueness',
                    security: volunteer_realtime_obj.checkId_nonce,
                    volunteer_id: value
                }
            });
            toggleSpinner(false);
            return response.success && response.data && response.data.isUnique ?
                { isUnique: true, message: "Volunteer number is available" } :
                { isUnique: false, message: "Volunteer number is not available" };
        } catch (error) {
            toggleSpinner(false);
            throw new Error("Error checking ID uniqueness");
        }
    };

    const checkEmailUniqueness = async (value) => {
        if (!isEmailChanged()) {
            return { isValid: true };
        }
        // Check if the email has been changed and needs uniqueness validation
        // if (isEmailChanged()) {
            try {
                toggleSpinner(true);
                const response = await $.ajax({
                    url: volunteer_realtime_obj.ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'check_email_uniqueness',
                        security: volunteer_realtime_obj.checkEmail_nonce,
                        volunteer_email: value
                    }
                });
                toggleSpinner(false);
                return response.success && response.data && response.data.isUnique ?
                    { isUnique: true, message: "Volunteer email is available" } :
                    { isUnique: false, message: "This email is registered already" };
            } catch (error) {
                toggleSpinner(false);
                // throw new Error("Error checking Email uniqueness");
                console.error("Error checking email uniqueness:", error);
                console.log("Checking email uniqueness for:", value);
                console.log("Nonce:", volunteer_realtime_obj.checkEmail_nonce);
                return { isValid: false, message: "Error during validation" };
            }
        // } else {
        //     // If email hasn't changed, no need to validate for uniqueness
        //     return { isValid: true };
        // }
    };

    // Generalized validation function       
    const validateFieldWithAjax = async (field, validationRegex, ajaxFunction, successMessage, errorMessage, emptyMessage) => {
        const value = field.val();
        // Directly return valid if email hasn't changed
        // if (field.attr('id') === 'volunteer_email' && !isEmailChanged()) {
        //     return { isValid: true };
        // }
        // Check for empty value and display emptyMessage if provided
        if (value === "") {
            displayErrorMessage(field, emptyMessage, false);
            return { isValid: emptyMessage === "", message: emptyMessage };
        }
        if (!validationRegex.test(value)) {
            displayErrorMessage(field, errorMessage, false);
            return { isValid: false, message: errorMessage };
        }
        console.log(ajaxFunction); // Check what is being passed as ajaxFunction
        // AJAX validation if provided
        if (typeof ajaxFunction === 'function') {
            const result = await ajaxFunction(value);
            if (result.isValid !== undefined) {
                displayErrorMessage(field, result.isValid ? '' : result.message, result.isValid);
                return result;
            } else {
                displayErrorMessage(field, 'Unexpected response from server', false);
                return { isValid: false, message: 'Unexpected response from server' };
            }
        } else {
            // If ajaxFunction is not provided, assume field is valid
            displayErrorMessage(field, '', true);
            return { isValid: true };
        }
    };

    // Example usage for volunteer ID field
    $('#volunteer_id').on('keyup change', debounce(async function() {
        await validateFieldWithAjax(
            $(this),
            /^\d+$/,
            checkVolunteerIdUniqueness,
            'Volunteer number is available',
            'Positive number ID only',
            ''
        );
    }, 500));
    // Example usage for volunteer Email field
    $('#volunteer_email').on('keyup change', debounce(async function() {
        await validateFieldWithAjax(
            $(this),
            /^\S+@\S+\.\S+$/, // Email regex
            checkEmailUniqueness, // AJAX function for checking uniqueness
            'Email is available',
            'Invalid email format',
            'Email is required'
        );
    }, 500));
    // ... Similar implementations for other fields like email, etc. ...
    $('#data_inscricao').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            /^\d{4}-\d{2}-\d{2}$/, // Date regex in YYYY-MM-DD format
            null, // No AJAX function required
            '',
            'Invalid date format (YYYY-MM-DD expected)',
            'Date is required'
        );
    }, 500));
    $('#first_name').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            /^[A-Za-z\s]+$/, // Only letters and spaces
            null,
            '',
            'Invalid first name',
            'First name is required'
        );
    }, 500));
    $('#last_name').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            /^[A-Za-z\s]+$/, // Only letters and spaces
            null,
            '',
            'Invalid last name',
            'Last name is required'
        );
    }, 500));
    $('#post_code').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            /^[a-zA-Z0-9\/,\- ]+$/, // Alphanumeric characters and spaces
            null,
            '',
            'Invalid post code format',
            'Post code is required'
        );
    }, 500));
    $('#localidade').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            /^[a-zA-Z0-9\/,\- ]+$/, // Alphanumeric characters and spaces
            null,
            '',
            'Invalid city format',
            'City is required'
        );
    }, 500));
    $('#telemovel').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            // /^\+?\d{1,4}[\s-]?\d{1,15}$/, // Phone number regex
            /^\+?([0-9]{1,3})?[-. (]?([0-9]{1,4})[)-. ]?([0-9]{1,4})[-. ]?([0-9]{1,4})[-. ]?([0-9]{1,9})$/, // Phone number regex
            null,
            '',
            'Invalid phone number format',
            'Phone number is required'
        );
    }, 500));
    $('#a_date').on('keyup change', debounce(function() {
        validateFieldWithAjax(
            $(this),
            /^\d{4}-\d{2}-\d{2}$/, // Date regex in YYYY-MM-DD format
            null,
            '',
            'Invalid date format (YYYY-MM-DD expected)',
            'Date is required'
        );
    }, 500));

    // Debounce function to limit the rate of execution
    function debounce(func, delay) {
        let debounceTimer;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Function to validate the entire form
    async function validateForm() {
        toggleSpinner(true);
        let validations = [
            validateFieldWithAjax(
                $('#volunteer_id'), 
                /^\d+$/, 
                checkVolunteerIdUniqueness, 
                'Volunteer number is available', 
                'Positive number ID only', 
                ''
            ),
            // ... Add other field validations ...
            validateFieldWithAjax(
                $('#data_inscricao'),
                /^\d{4}-\d{2}-\d{2}$/, // Date format: YYYY-MM-DD
                null,
                '',
                'Invalid date format (YYYY-MM-DD expected)',
                'Inscrição date is required'
            ),
            validateFieldWithAjax(
                $('#first_name'),
                /^[A-Za-z\s]+$/, // Only letters and spaces
                null,
                '',
                'Invalid first name',
                'First name is required'
            ),
            validateFieldWithAjax(
                $('#last_name'),
                /^[A-Za-z\s]+$/, // Only letters and spaces
                null,
                '',
                'Invalid last name',
                'Last name is required'
            ),
            validateFieldWithAjax(
                $('#post_code'),
                /^[a-zA-Z0-9\/,\- ]+$/, // Alphanumeric characters, spaces, and hyphens
                null,
                '',
                'Invalid post code format',
                'Post code is required'
            ),
            validateFieldWithAjax(
                $('#localidade'),
                /^[a-zA-Z0-9\/,\- ]+$/, // Any non-empty string
                null,
                '',
                'Invalid city format',
                'City is required'
            ),
            validateFieldWithAjax(
                $('#telemovel'),
                /^\+?([0-9]{1,3})?[-. (]?([0-9]{1,4})[)-. ]?([0-9]{1,4})[-. ]?([0-9]{1,4})[-. ]?([0-9]{1,9})$/, // Phone number format
                null,
                '',
                'Invalid phone number format',
                'Phone number is required'
            ),
            validateFieldWithAjax(
                $('#volunteer_email'),
                /^\S+@\S+\.\S+$/, // Email format
                checkEmailUniqueness, // AJAX call to check uniqueness
                'Email is available',
                'Invalid email format or already registered',
                'Email is required'
            ),
            validateFieldWithAjax(
                $('#a_date'),
                /^\d{4}-\d{2}-\d{2}$/, // Date format: YYYY-MM-DD
                null,
                '',
                'Invalid date format (YYYY-MM-DD expected)',
                'A date is required'
            ),
        ];
        let results = await Promise.all(validations);
        toggleSpinner(false);
        
        // Log each validation result
        results.forEach((result, index) => {
            console.log(`Validation ${index}: `, result);
        });

        return results.every(result => result.isValid);
    }
    // Function to clear the form fields and reset validation states
    function clearForm() {
        if (!isEditMode()) {
            $('#volunteerForm').trigger('reset'); 
        }
            // Remove 'success-message' class from all fields
            $('#volunteerForm').find('.success-message').removeClass('success-message');

            // Clear all error messages
            $('.error-message').text('');
       
    }
    
    // Attach validation to form submission event   
    $('#volunteerForm').on('submit', async function(event) {
        event.preventDefault();
        const isFormValid = await validateForm();

        if (isFormValid) {                    
            // Extracting the my_id
            var myId = $('#my_id').val();
            // var action = 'process_volunteer'; // General action for both edit and register
            var action = 'register_volunteer'; // General action for both edit and register

            // Preparing formData for AJAX submission
            var formData = new FormData(this);
            formData.append('action', action);
            formData.append('security', volunteer_realtime_obj.register_nonce); // Using register nonce for simplicity

            if (myId) {
                formData.append('my_id', myId); // Add my_id only if it exists
            }
                
            showSpinner();
            $.ajax({
                url: volunteer_realtime_obj.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideSpinner();
                    if (response.success) {
                        // Show the success message and clear the form
                        showMessageWithZoomOutEffect(response.data, 'success');
                        clearForm();
                    } else {
                        // Show the error message if the response contains an error
                        showMessageWithZoomOutEffect(response.data || 'Error occurred.', 'error');
                    }
                },
                // error: function(jqXHR) {
                //     hideSpinner();
                //     let errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.data ? jqXHR.responseJSON.data : "An unknown error occurred.";
                //     showMessageWithZoomOutEffect(errorMessage, 'error');
                // }
                error: function(jqXHR, textStatus, errorThrown) {
                    hideSpinner();
                    let errorMessage = "An unknown error occurred.";
                    if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.data) {
                        errorMessage = jqXHR.responseJSON.data;
                    } else if (textStatus) {
                        errorMessage = "AJAX Error: " + textStatus;
                    }
                    showMessageWithZoomOutEffect(errorMessage, 'error');
                }
            });
        } else {
            hideSpinner();
            showMessageWithZoomOutEffect('Please correct the errors in the form.', 'error');
        }
    });
 

    // Function to fetch and populate volunteer data for editing
    function fetchAndPopulateVolunteerData() {
        var urlParams = new URLSearchParams(window.location.search);
        var myId  = urlParams.get('id');
        if (myId ) {
            showSpinner();
            $.ajax({
                url: volunteer_realtime_obj.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fetch_volunteer_data',
                    my_id: myId, 
                    security: volunteer_realtime_obj.fetchToEdit_nonce
                },
                success: function(response) {
                    if (response.success) {
                        populateFormWithData(response.data);
                        hideSpinner();
                    } else {
                        displayErrors([response.data || 'Failed to fetch data.']);
                        hideSpinner();
                    }
                },
                error: function() {
                    displayErrors(['Failed to send request. Please try again.']);
                    hideSpinner();
                }
            });
        }
    }

    // Call the function if we are on the dataform page
    if(window.location.pathname.includes('volunteer-registration-form')) {
        fetchAndPopulateVolunteerData();
    }

}); 