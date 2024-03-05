<?php
// New ajax code to register volunteer starts form here
// Utility function to sanitize and validate input
function validate_input($input, $regex, $error_msg) {
    $sanitized_input = sanitize_text_field(trim($input));
    if (!preg_match($regex, $sanitized_input)) {
        wp_send_json_error($error_msg);
        wp_die();
    }
    return $sanitized_input;
}
function register_volunteer() {
    global $wpdb;
    check_ajax_referer('register-volunteer-nonce', 'security');

    // Extract and validate volunteer_id
    $volunteer_id = isset($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : null;
    if ($volunteer_id !== null && ($volunteer_id <= 0 || !is_numeric($volunteer_id))) {
        wp_send_json_error('Invalid Volunteer ID.');
        wp_die();
    }

    // Check if volunteer_id already exists in the database
    if ($volunteer_id !== null && $wpdb->get_var($wpdb->prepare("SELECT my_id FROM {$wpdb->prefix}volunteers WHERE volunteer_id = %d", $volunteer_id))) {
        wp_send_json_error('Volunteer ID already exists.');
        wp_die();
    }

    
    // Extract and validate inputs
    $data_inscricao = validate_input($_POST['data_inscricao'] ?? '', '/^\d{4}-\d{2}-\d{2}$/', 'Invalid inscrição date');
    $first_name = validate_input($_POST['first_name'] ?? '', '/^[a-zA-Z ]+$/', 'Invalid first name');
    $last_name = validate_input($_POST['last_name'] ?? '', '/^[a-zA-Z ]+$/', 'Invalid last name');
    $post_code = validate_input($_POST['post_code'] ?? '', '/^\S+$/', 'Invalid post code');
    $localidade = validate_input($_POST['localidade'] ?? '', '/^\S+$/', 'Invalid localidade');
    $telemovel = validate_input($_POST['telemovel'] ?? '', '/^\+?\d{1,4}[\s-]?\d{1,15}$/', 'Invalid phone number');
    $volunteer_email = sanitize_email($_POST['volunteer_email'] ?? '');
    $a_date = validate_input($_POST['a_date'] ?? '', '/^\d{4}-\d{2}-\d{2}$/', 'Invalid A date');
    $morada = sanitize_text_field($_POST['morada'] ?? '');
    $education = sanitize_text_field($_POST['education'] ?? '');
    $profession = sanitize_text_field($_POST['profession'] ?? '');
    $encaminhado = sanitize_text_field($_POST['encaminhado'] ?? '');
    $pref1 = sanitize_text_field($_POST['pref1'] ?? '');
    $pref2 = sanitize_text_field($_POST['pref2'] ?? '');
    $pref3 = sanitize_text_field($_POST['pref3'] ?? '');
    $pref_other = sanitize_text_field($_POST['pref_other'] ?? '');

    // Check for valid email
    if (!is_email($volunteer_email)) {
        wp_send_json_error('Invalid email format.');
        wp_die();
    }

    // Check for unique email
    if ($wpdb->get_var($wpdb->prepare("SELECT my_id FROM {$wpdb->prefix}volunteers WHERE volunteer_email = %s", $volunteer_email))) {
        wp_send_json_error('This email is already registered.');
        wp_die();
    }

    // Prepare and execute the insert query
    $table_name = $wpdb->prefix . 'volunteers';
    $data = compact('volunteer_id', 'data_inscricao', 'first_name', 'last_name', 'post_code', 'morada', 'localidade', 'telemovel', 'volunteer_email', 'education', 'profession', 'encaminhado', 'a_date', 'pref1', 'pref2', 'pref3', 'pref_other');
    $format = array_fill(0, count($data), '%s');
    $format['volunteer_id'] = '%d';
    $format['telemovel'] = '%d';

    if ($wpdb->insert($table_name, $data, $format)) {
        wp_send_json_success('Volunteer registered successfully.');
    } else {
        wp_send_json_error('Error in registering volunteer.');
    }

    wp_die();
}
add_action('wp_ajax_register_volunteer', 'register_volunteer');
add_action('wp_ajax_nopriv_register_volunteer', 'register_volunteer');


