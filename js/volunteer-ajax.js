jQuery(document).ready(function($) {



    // Function to display error messages
    function displayErrors(errors) {
        var errorsHtml = errors.map(function(error) {
            return '<span>' + error + '</span>';
        }).join('');
        // $('#form-errors').html(errorsHtml).show();
        showMessageWithZoomOutEffect(errorsHtml, 'error');

    }
    
    //Function to display success message
    function displaySuccess(success){
        var successHtml = success.map(function(success) {
            return '<span>' + success + '</span>';
        }).join('');
        showMessageWithZoomOutEffect(successHtml, 'success');
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

        // Extracting the my_id and determining the action
        var myId = $('#my_id').val();
        var isEdit = myId && !isNaN(myId) && parseInt(myId, 10) > 0;
        if (isEdit && !myId) {
            alert('Volunteer ID is required and must be numeric.');
            return;
        }
        var action = isEdit ? 'edit_volunteer' : 'register_volunteer';
        // Preparing formData for AJAX submission
        var formData = new FormData(this);
        formData.append('action', action);
        formData.append('security', isEdit ? volunteer_ajax_obj.edit_nonce : volunteer_ajax_obj.register_nonce);
    
        if (isEdit) {
            formData.append('my_id', myId); // Add my_id to the formData for edit operation
        }
        showSpinner();
        // AJAX request...
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
                        hideSpinner();
                    } else {
                        // Handle other kinds of errors
                        displayErrors([response.data || 'An unknown error occurred.']);
                        hideSpinner();
                    }
                } else {
                    // Handle success, e.g., clear the form, display a success message, or redirect
                    displaySuccess([response.data || 'Operation successful']);
                    hideSpinner();
                    // If it's an edit operation
                    if (isEdit) {
                        // Redirect to the previous page
                        // window.history.back();
                        hideSpinner();
                    } else {
                        // For new registration, clear the form or perform other actions
                        hideSpinner();
                        clearForm();
                    }
                }
            },
            error: function() {
                displayErrors(['Failed to send request. Please try again.']);
                hideSpinner();
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
    }

    // Function to fetch and populate volunteer data for editing
    function fetchAndPopulateVolunteerData() {
        var urlParams = new URLSearchParams(window.location.search);
        var myId  = urlParams.get('id');
        if (myId ) {
            showSpinner();
            $.ajax({
                url: volunteer_ajax_obj.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fetch_volunteer_data',
                    my_id: myId, 
                    security: volunteer_ajax_obj.fetchToEdit_nonce
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