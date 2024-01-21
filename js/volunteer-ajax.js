jQuery(document).ready(function($) {
    // Function to display error messages
    function displayErrors(errors) {
        var errorsHtml = errors.map(function(error) {
            return '<span>' + error + '</span>';
        }).join('');
        $('#form-errors').html(errorsHtml).show();
    }
    //Function to display success message
    function displaySuccess(success){
        var successHtml = success.map(function(success) {
            return '<span>' + success + '</span>';
        }).join('');
        $('#form-success').html(successHtml).show();
    }
    
    // Function to clear the form fields
    function clearForm() {
        $('#volunteerForm').trigger('reset'); // Resets all form fields
        // Optionally, reset any additional dynamic elements or UI states
    }

    // Function to handle both registration and edit submissions
    $('#volunteerForm').submit(function(e) {
        e.preventDefault();

        // Perform client-side validation first
        if (!validateForm()) {
            return; // Stop here if validation fails
        }

        // Extracting the volunteerId and determining the action
        var volunteerId = $('#volunteer_id_hidden').val();
        var isEdit = volunteerId && !isNaN(volunteerId) && parseInt(volunteerId, 10) > 0;
        if (isEdit && !volunteerId) {
            alert('Volunteer ID is required and must be numeric.');
            return;
        }
        var action = isEdit ? 'edit_volunteer' : 'register_volunteer';

        // Preparing formData for AJAX submission
        var formData = new FormData(this);
        formData.append('action', action);
        formData.append('security', isEdit ? volunteer_ajax_obj.edit_nonce : volunteer_ajax_obj.register_nonce);

        $.ajax({
            url: volunteer_ajax_obj.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,

            //success message
            success: function(response) {
                if (!response.success) {
                    if (response.errors) {
                        // Handle server-side validation errors
                        handleServerSideErrors(response.errors);
                    } else {
                        // Handle other kinds of errors
                        displayErrors([response.data || 'An unknown error occurred.']);
                    }
                } else {
                    // Handle success, e.g., clear the form, display a success message, or redirect
                    displaySuccess([response.data || 'Operation successful']);
                    // If it's an edit operation
                    if (isEdit) {
                        // Redirect to the previous page
                        // window.history.back();
                    } else {
                        // For new registration, clear the form or perform other actions
                        clearForm();
                    }
                }
            },
            error: function() {
                displayErrors(['Failed to send request. Please try again.']);
            }

        });
    });
 
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
        $('#volunteer_email').val(data.volunteer_email);
        $('#education').val(data.education);
        $('#profession').val(data.profession);
        $('#encaminhado').val(data.encaminhado);
        $('#a_date').val(data.a_date);
        $('#pref1').val(data.pref1);
        $('#pref2').val(data.pref2);
        $('#pref3').val(data.pref3);
        $('#pref_other').val(data.pref_other);
        // ...populate other fields similarly
    }

    // Function to fetch and populate volunteer data for editing
    function fetchAndPopulateVolunteerData() {
        var urlParams = new URLSearchParams(window.location.search);
        var volunteerId = urlParams.get('id');

        if (volunteerId) {
            $.ajax({
                url: volunteer_ajax_obj.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fetch_volunteer_data',
                    volunteer_id: volunteerId,
                    security: volunteer_ajax_obj.fetchToEdit_nonce
                },
                success: function(response) {
                    if (response.success) {
                        populateFormWithData(response.data);
                    } else {
                        displayErrors([response.data || 'Failed to fetch data.']);
                    }
                },
                error: function() {
                    displayErrors(['Failed to send request. Please try again.']);
                }
            });
        }
    }

    // Call the function if we are on the dataform page
    if(window.location.pathname.includes('volunteer-registration-form')) {
        fetchAndPopulateVolunteerData();
    }
});