jQuery(document).ready(function($) {
    // Close the modal when user clicks on 'x' (span.close)
    $('.close').on('click', function() {
        $('#deleteConfirmationModal').hide();
    });
    // Initialize DataTable
    var table = $('#volunteerTable').DataTable({
        "ajax": {
            "url": volunteer_datatables_obj.ajaxurl,
            "type": "POST",
            "data": {
                "action": "fetch_volunteers",
                "security": volunteer_datatables_obj.fetch_nonce
            }
        },
        "createdRow": function(row, data, dataIndex) {
            // Assuming 'data' has a property 'id' which is the volunteer ID
            $(row).attr('data-id', data.my_id);
            $(row).attr('data-first-name', data.first_name);
            $(row).attr('data-last-name', data.last_name);

        },
        // ... other DataTable options ...
        "columns": [
            // Assuming the first two columns are for 'Edit' and 'Delete' actions
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<a href="volunteer-registration-form/?id=' + row.my_id + '" class="text-primary edit-button"><i class="fas fa-pencil-alt"></i></a>';
                }
            },            
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<a href="#" class="text-danger delete-button"><i class="fas fa-trash-alt"></i></a>';
                }
            },
            
            // Columns for each field
            { "data": "my_id" },
            { "data": "volunteer_id" },
            { "data": "data_inscricao" },
            { "data": "first_name" },
            { "data": "last_name" },
            { "data": "post_code" },
            { "data": "morada" },
            { "data": "localidade" },
            { "data": "telemovel" },
            { "data": "volunteer_email" },
            { "data": "education" },
            { "data": "profession" },
            { "data": "encaminhado" },
            { "data": "a_date" },
            { "data": "pref1" },
            { "data": "pref2" },
            { "data": "pref3" },
            { "data": "pref_other" }
        ]
    });

    
    // Event listener for delete buttons
    $('#volunteerTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
        
        // deleteVolunteer(myId);
        var myId = $(this).closest('tr').data('id');
        var firstName = $(this).closest('tr').data('first-name');
        var lastName = $(this).closest('tr').data('last-name');
        $('#volunteerNameToDelete').text(firstName + " " + lastName);
        $('#deleteConfirmationModal').show();
        $('#confirmDelete').off().on('click', function() {
            deleteVolunteer(myId);
            $('#deleteConfirmationModal').hide();
        });
        $('#cancelDelete').on('click', function() {
            $('#deleteConfirmationModal').hide();
        });

        

    });
    
    $('.close').on('click', function() {
        $('#deleteConfirmationModal').hide();
    });
    // Delete volunteer function
    function deleteVolunteer(myId) {
        // console.log("Deleting volunteer with ID:", myId);
        // if (confirm('Are you sure you want to delete this?' + myId)) {
            showSpinner();
            $.ajax({
                url: volunteer_datatables_obj.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_volunteer', // Match this with your PHP action hook
                    security: volunteer_datatables_obj.delete_nonce, 
                    my_id: myId
                },
                success: function(response) {
                    if (response.success) {
                        showMessageWithZoomOutEffect('Volunteer deleted successfully', 'success');
                        // $('#form-success').text('Volunteer deleted successfully from').show();
                        table.ajax.reload(); // Reload the table data
                        hideSpinner();
                    } else {
                        var errorMessage = 'Error deleting volunteer: ' + (response.data || 'Unknown error');
                        showMessageWithZoomOutEffect(errorMessage, 'error');
                        // $('#form-errors').text('Error deleting volunteer' + response.data).show();
                        hideSpinner();
                    }
                },
                error: function(xhr, status, error) {
                    // console.error("Error: ", status, error);
                    var errorMessage = "Error: " + status + " " + error;
                    showMessageWithZoomOutEffect(errorMessage, 'error');
                    hideSpinner();
                }
            });
        // }
    }
});
