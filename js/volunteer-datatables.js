jQuery(document).ready(function($) {
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
            $(row).attr('data-id', data.id);
        },
        // ... other DataTable options ...
        "columns": [
            // Assuming the first two columns are for 'Edit' and 'Delete' actions
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<a href="volunteer-registration-form/?id=' + row.id + '" class="text-primary edit-button"><i class="fas fa-pencil-alt"></i></a>';
                }
            },            
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<a href="#" class="text-danger delete-button"><i class="fas fa-trash-alt"></i></a>';
                }
            },
            
            // Columns for each field
            { "data": "id" },
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
        var volunteerId = $(this).closest('tr').data('id');
        deleteVolunteer(volunteerId);
    });

    // Delete volunteer function
    function deleteVolunteer(volunteerId) {
        console.log("Deleting volunteer with ID:", volunteerId);
        if (confirm('Are you sure you want to delete this?')) {
            $.ajax({
                url: volunteer_datatables_obj.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_volunteer', // Match this with your PHP action hook
                    security: volunteer_datatables_obj.delete_nonce, 
                    volunteer_id: volunteerId
                },
                success: function(response) {
                    if (response.success) {
                        alert("Volunteer deleted successfully.");
                        table.ajax.reload(); // Reload the table data
                    } else {
                        alert("Error: " + response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error: ", status, error);
                }
            });
        }
    }
});
