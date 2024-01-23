<?php
// New ajax code to edit volunteer starts form here
// Utility function to sanitize and validate input
function validate_input_edit($input, $regex, $error_msg) {
    $sanitized_input = sanitize_text_field(trim($input));
    if (!preg_match($regex, $sanitized_input)) {
        wp_send_json_error($error_msg);
        wp_die();
    }
    return $sanitized_input;
}

function edit_volunteer() {
    global $wpdb;
    check_ajax_referer('edit-volunteer-nonce', 'security');

    // Validate 'my_id'
    $my_id = isset($_POST['my_id']) && is_numeric($_POST['my_id']) && $_POST['my_id'] > 0 ? intval($_POST['my_id']) : null;
    if (!$my_id) {
        wp_send_json_error('Invalid Volunteer Row ID');
        wp_die();
    }

    // Extract and validate inputs
    $data_inscricao = validate_input_edit($_POST['data_inscricao'] ?? '', '/^\d{4}-\d{2}-\d{2}$/', 'Invalid inscrição date');
    $first_name = validate_input_edit($_POST['first_name'] ?? '', '/^[a-zA-Z ]+$/', 'Invalid first name');
    $last_name = validate_input_edit($_POST['last_name'] ?? '', '/^[a-zA-Z ]+$/', 'Invalid last name');
    $post_code = validate_input_edit($_POST['post_code'] ?? '', '/^\S+$/', 'Invalid post code');
    $localidade = validate_input_edit($_POST['localidade'] ?? '', '/^\S+$/', 'Invalid localidade');
    $telemovel = validate_input_edit($_POST['telemovel'] ?? '', '/^\+?\d{1,4}[\s-]?\d{1,15}$/', 'Invalid phone number');
    
    // Sanitize other fields without specific validation rules
    $morada = sanitize_text_field($_POST['morada'] ?? '');
    $education = sanitize_text_field($_POST['education'] ?? '');
    $profession = sanitize_text_field($_POST['profession'] ?? '');
    $encaminhado = sanitize_text_field($_POST['encaminhado'] ?? '');
    $volunteer_email = sanitize_email($_POST['volunteer_email'] ?? '');
    $a_date = validate_input_edit($_POST['a_date'] ?? '', '/^\d{4}-\d{2}-\d{2}$/', 'Invalid A date');
    $pref1 = sanitize_text_field($_POST['pref1'] ?? '');
    $pref2 = sanitize_text_field($_POST['pref2'] ?? '');
    $pref3 = sanitize_text_field($_POST['pref3'] ?? '');
    $pref_other = sanitize_text_field($_POST['pref_other'] ?? '');

    // Validate email format
    if (!is_email($volunteer_email)) {
        wp_send_json_error('Invalid email format.');
        wp_die();
    }

    // Check for unique email excluding the current volunteer
    if ($wpdb->get_var($wpdb->prepare("SELECT my_id FROM {$wpdb->prefix}volunteers WHERE volunteer_email = %s AND my_id != %d", $volunteer_email, $my_id))) {
        wp_send_json_error('This email is already registered.');
        wp_die();
    }

    // Prepare and execute the update query
    $table_name = $wpdb->prefix . 'volunteers';
    $data = compact('volunteer_id', 'data_inscricao', 'first_name', 'last_name', 'post_code', 'morada', 'localidade', 'telemovel', 'volunteer_email', 'education', 'profession', 'encaminhado', 'a_date', 'pref1', 'pref2', 'pref3', 'pref_other');
    $format = array_fill(0, count($data), '%s');
    $format['telemovel'] = '%d';

    if ($wpdb->update($table_name, $data, ['my_id' => $my_id], $format) !== false) {
        wp_send_json_success('Volunteer updated successfully.');
    } else {
        wp_send_json_error('Error in updating volunteer.');
    }

    wp_die();
}
add_action('wp_ajax_edit_volunteer', 'edit_volunteer'); // Hook for logged-in users
add_action('wp_ajax_nopriv_edit_volunteer', 'edit_volunteer'); // Hook for non-logged-in users
