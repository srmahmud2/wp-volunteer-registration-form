<?php
// Ajax handler for fetching volunteers for datatable.php
function fetch_volunteers() {
    global $wpdb;
    check_ajax_referer('fetch-volunteers-nonce', 'security');

    $table_name = $wpdb->prefix . 'volunteers';
    $query = "SELECT * FROM {$table_name}";
    $volunteers = $wpdb->get_results($query, ARRAY_A);

    // Prepare data for DataTables
    $data = array('data' => $volunteers);
    wp_send_json($data);

    wp_die();
}
add_action('wp_ajax_fetch_volunteers', 'fetch_volunteers');
add_action('wp_ajax_nopriv_fetch_volunteers', 'fetch_volunteers');

//functions to populate the front end form with specific id, to be edited any fields
function fetch_volunteer_data() {
    global $wpdb;
    check_ajax_referer('fetch-volunteer-data-nonce', 'security'); //check referer er first parameter diyei wp_create_nonce kora lage

	// Extracting and validating 'my_id'
    $my_id = isset($_POST['my_id']) && is_numeric($_POST['my_id']) && $_POST['my_id'] > 0 ? intval($_POST['my_id']) : null;
    if (!$my_id) {
        wp_send_json_error('Invalid Volunteer ID');
        wp_die();
    }
    $table_name = $wpdb->prefix . 'volunteers';
    $volunteer_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE my_id = %d", $my_id), ARRAY_A);

    if ($volunteer_data) {
        wp_send_json_success($volunteer_data);
    } else {
        wp_send_json_error('Volunteer not found');
    }

    wp_die();
}

add_action('wp_ajax_fetch_volunteer_data', 'fetch_volunteer_data');
add_action('wp_ajax_nopriv_fetch_volunteer_data', 'fetch_volunteer_data');