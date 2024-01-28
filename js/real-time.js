jQuery(document).ready(function($) {
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
    // AJAX call to check volunteer_id uniqueness
    // Refactored function using async/await for volunteer ID uniqueness check
    const checkVolunteerIdUniqueness = async (value) => {
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
            throw new Error("Error checking uniqueness");
        }
    };

    const checkEmailUniqueness = async (value) => {
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
            throw new Error("Error checking uniqueness");
        }
    };

    // Generalized validation function       
    const validateFieldWithAjax = async (field, validationRegex, ajaxFunction, successMessage, errorMessage, emptyMessage) => {
        const value = field.val();
        // Check for empty value and display emptyMessage if provided
        if (value === "") {
            displayErrorMessage(field, emptyMessage, false);
            return { isValid: emptyMessage === "", message: emptyMessage };
        }
        if (!validationRegex.test(value)) {
            displayErrorMessage(field, errorMessage, false);
            return { isValid: false, message: errorMessage };
        }
        // console.log(ajaxFunction); // Check what is being passed as ajaxFunction
        if (typeof ajaxFunction === 'function') {
            try {
                const result = await ajaxFunction(value);
                displayErrorMessage(field, result.isUnique ? '' : result.message, result.isUnique);
                return { isValid: result.isUnique, message: result.isUnique ? successMessage : result.message };
            } catch (error) {
                displayErrorMessage(field, error.message, false);
                return { isValid: false, message: error.message };
            }
        }else {
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
            // validateFieldWithAjax(
            //     $('#morada'),
            //     /.+/, // Any non-empty string
            //     null,
            //     '',
            //     'Invalid address format',
            //     'Address is required'
            // ),
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
        return results.every(result => result.isValid);
    }
    // Attach validation to form submission event   
    $('#volunteerForm').on('submit', async function(event) {
        event.preventDefault();
        const isFormValid = await validateForm();
        if (isFormValid) {                    
            // Extracting the my_id and determining the action
            var myId = $('#my_id').val();
            var isEdit = myId && !isNaN(myId) && parseInt(myId, 10) > 0;
            var action = isEdit ? 'edit_volunteer' : 'register_volunteer';

            // Preparing formData for AJAX submission
            var formData = new FormData(this);
            formData.append('action', action);
            formData.append('security', isEdit ? volunteer_realtime_obj.edit_nonce : volunteer_realtime_obj.register_nonce);

            if (isEdit) {
                formData.append('my_id', myId);
            }
                
            showSpinner();
            // AJAX request...
            $.ajax({
                url: volunteer_realtime_obj.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false, // Needed for FormData
                contentType: false,
                success: function(response) {
                    // Handle success - You can show a success message or redirect
                    console.log('Form submitted successfully');
                    hideSpinner();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle errors - You can show an error message
                    console.error('Error submitting form: ' + textStatus, errorThrown);
                    hideSpinner();
                }
            });
        }else {
            // Form is not valid, don't submit and let the user correct inputs
            console.log('Form validation failed');
            hideSpinner();
        }
    });
});