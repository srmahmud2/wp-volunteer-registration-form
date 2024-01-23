<?php
// Ajax handler for deleting a volunteer
function delete_volunteer() {
    global $wpdb;
    check_ajax_referer('delete-volunteer-nonce', 'security');

    // Extracting and validating 'my_id' from the AJAX request
    $my_id = isset($_POST['my_id']) && is_numeric($_POST['my_id']) && $_POST['my_id'] > 0 ? intval($_POST['my_id']) : null;
    if (!$my_id) {
        wp_send_json_error('Invalid Volunteer Row ID');
        wp_die();
    }

    $table_name = $wpdb->prefix . 'volunteers';
    $result = $wpdb->delete($table_name, array('my_id' => $my_id), array('%d'));

    if ($result) {
        wp_send_json_success('Volunteer deleted successfully');
    } else {
        wp_send_json_error('Error in deleting volunteer');
    }

    wp_die(); // Required to terminate and return a proper response
}

add_action('wp_ajax_delete_volunteer', 'delete_volunteer'); // Hook for logged-in users
add_action('wp_ajax_nopriv_delete_volunteer', 'delete_volunteer'); // Hook for non-logged-in users