<?php
/**
 * Sanitizes and validates a given input based on a regular expression.
 * 
 * @param string $input The input string to be sanitized and validated.
 * @param string $regex The regular expression pattern to validate the input against.
 * @param string $error_msg The error message to return if validation fails.
 * 
 * @return string The sanitized input if validation passes.
 * 
 * @throws JsonErrorResponse If the input fails to validate, a JSON response is sent and execution is terminated.
 */
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
/**
 * Processes the registration of a volunteer by inserting data into the database.
 * 
 * @param wpdb $wpdb WordPress database global object.
 * @param array $data Associative array containing volunteer data to be inserted.
 * @return void Returns nothing. Outputs JSON response and terminates execution.
 */
//process volunteer registration
function process_volunteer_registration($wpdb, $data) {
    $table_name = $wpdb->prefix . 'volunteers';
    $format = array_fill(0, count($data), '%s');
    $format['volunteer_id'] = '%d';
    $format['telemovel'] = '%d';

    // Determine if this is an edit operation
    $is_edit = isset($data['my_id']) && !empty($data['my_id']) && is_numeric($data['my_id']);

    if ($is_edit) {
        // Update existing volunteer
        $my_id = intval($data['my_id']);
        unset($data['volunteer_id']); // Do not update volunteer_id in edit mode
        unset($data['my_id']); // Remove my_id from data as it's used in WHERE clause
        $where = array('my_id' => $my_id);
        if ($wpdb->update($table_name, $data, $where, $format, array('%d')) !== false) {
            wp_send_json_success('Volunteer updated successfully.');
        } else {
            wp_send_json_error('Error in updating volunteer: ' . $wpdb->last_error);
        }
    } else {
        // Insert new volunteer
        if ($wpdb->insert($table_name, $data, $format) !== false) {
            wp_send_json_success('Volunteer registered successfully.');
        } else {
            wp_send_json_error('Error in registering volunteer: ' . $wpdb->last_error);
        }
    }
    wp_die();
}

/**
 * Handles the AJAX request to check for the uniqueness of a volunteer ID.
 * This function checks if the given volunteer ID is already present in the database.
 * It uses the WordPress global `$wpdb` object to interact with the database and
 * employs nonce verification for security.
 *
 * @global wpdb $wpdb WordPress database access abstraction object.
 * @return void Outputs JSON response indicating whether the ID is unique and terminates execution.
 */

// php function to check volunteer_id uniqueness 
function check_volunteer_id_uniqueness_function() {
    global $wpdb;
    check_ajax_referer('unique_volunteer_id_nonce', 'security');
    $volunteer_id = $_POST['volunteer_id'];

    // Check if volunteer_id is set and is a number
    if ($volunteer_id !== null && is_numeric($volunteer_id)) {
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}volunteers WHERE volunteer_id = %d", $volunteer_id));
        if ($exists > 0) {
            // If volunteer_id already exists in the database
            wp_send_json_error('Volunteer number not available.');
        } else {
            // If volunteer_id is unique
            wp_send_json_success(array('isUnique' => true));
        }
    } else {
        // If volunteer_id is not set or not a valid number
        wp_send_json_error('Invalid Volunteer number.');
    }

    wp_die();
}
add_action('wp_ajax_check_volunteer_id_uniqueness', 'check_volunteer_id_uniqueness_function');
add_action('wp_ajax_nopriv_check_volunteer_id_uniqueness', 'check_volunteer_id_uniqueness_function');
/**
 * Handles an AJAX request to check the uniqueness of a volunteer's email.
 * Verifies the nonce for security and checks if the provided email already exists in the database.
 *
 * @return void Returns nothing. Outputs JSON response and terminates execution.
 */

// php function to check volunteer_email uniqueness 
function check_email_uniqueness_function() {
    global $wpdb;
    check_ajax_referer('unique_email_nonce', 'security');

    $volunteer_email = $_POST['volunteer_email'];
    $my_id = isset($_POST['my_id']) ? intval($_POST['my_id']) : null;
    error_log('Email Uniqueness Check: ' . print_r($_POST, true));
    // Check if volunteer_email is set
    if ($volunteer_email !== null) {
        $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}volunteers WHERE volunteer_email = %s", $volunteer_email);

        // Exclude the current volunteer from the check if my_id is provided
        if ($my_id) {
            $query .= $wpdb->prepare(" AND my_id != %d", $my_id);
        }

        $exists = $wpdb->get_var($query);
        if ($exists > 0) {
            wp_send_json_error('This email is already registered.');
        } else {
            wp_send_json_success(array('isUnique' => true));
        }
    } else {
        wp_send_json_error('Invalid Email.');
    }

}
/**
 * Handles the registration of a new volunteer.
 * Validates and sanitizes the input data, checks for existing volunteer ID and email,
 * and then processes the registration by inserting data into the database.
 * Outputs a JSON response based on the success or failure of the operation.
 *
 * @return void Returns nothing. Outputs JSON response and terminates execution.
 */

function register_volunteer() {
    global $wpdb;
    check_ajax_referer('register-volunteer-nonce', 'security');
    // Determine if this is an edit operation
    $is_edit = isset($_POST['my_id']) && is_numeric($_POST['my_id']);
    $my_id = $is_edit ? intval($_POST['my_id']) : null;
    

    // Check if volunteer_id is provided and is a positive number
    if (isset($_POST['volunteer_id']) && $_POST['volunteer_id'] !== '') {
        if (is_numeric($_POST['volunteer_id']) && intval($_POST['volunteer_id']) > 0) {
            $volunteer_id = intval($_POST['volunteer_id']);
        } else {
            wp_send_json_error('Invalid Volunteer number.');
            wp_die();
        }
    } else {
        $volunteer_id = null;
    }
    
    
    // Check if volunteer_id already exists in the database
    if ($volunteer_id !== null && $wpdb->get_var($wpdb->prepare("SELECT my_id FROM {$wpdb->prefix}volunteers WHERE volunteer_id = %d", $volunteer_id))) {
        wp_send_json_error('Volunteer number not available.');
        wp_die();
    }
    
    // Extract and validate inputs
    $data_inscricao = validate_input($_POST['data_inscricao'] ?? '', '/^\d{4}-\d{2}-\d{2}$/', 'Invalid inscrição date');
    $first_name = validate_input($_POST['first_name'] ?? '', '/^[a-zA-Z ]+$/', 'Invalid first name');
    $last_name = validate_input($_POST['last_name'] ?? '', '/^[a-zA-Z ]+$/', 'Invalid last name');
    $post_code = validate_input($_POST['post_code'] ?? '', '/^[a-zA-Z0-9\/,\- ]+$/', 'Invalid post code');
    $localidade = validate_input($_POST['localidade'] ?? '', '/^[a-zA-Z0-9\/,\- ]+$/', 'Invalid localidade');
    $telemovel = validate_input($_POST['telemovel'] ?? '', '/^\+?([0-9]{1,3})?[-. (]?([0-9]{1,4})[)-. ]?([0-9]{1,4})[-. ]?([0-9]{1,4})[-. ]?([0-9]{1,9})$/', 'Invalid phone number');
    // Check for valid email
    $volunteer_email = sanitize_email($_POST['volunteer_email'] ?? '');
    if (!is_email($volunteer_email)) {
        wp_send_json_error('Invalid email format.');
        wp_die();
    }
    // Check for unique email
    // if ($wpdb->get_var($wpdb->prepare("SELECT my_id FROM {$wpdb->prefix}volunteers WHERE volunteer_email = %s", $volunteer_email))) {
    //     wp_send_json_error('This email is already registered.');
    //     wp_die();
    // } 
    // Check for unique email based on edit or not
    if (!$is_edit || ($is_edit && $volunteer_email != get_existing_volunteer_email($wpdb, $my_id))) {
        // Check for unique email only if it's a new registration or email has changed in edit
        if (email_exists_in_db($wpdb, $volunteer_email, $my_id)) {
            wp_send_json_error('This email is already registered.');
            wp_die();
        }
    }
    $a_date = validate_input($_POST['a_date'] ?? '', '/^\d{4}-\d{2}-\d{2}$/', 'Invalid A date');
    $morada = sanitize_text_field($_POST['morada'] ?? '');
    $education = sanitize_text_field($_POST['education'] ?? '');
    $profession = sanitize_text_field($_POST['profession'] ?? '');
    $encaminhado = sanitize_text_field($_POST['encaminhado'] ?? '');
    $pref1 = sanitize_text_field($_POST['pref1'] ?? '');
    $pref2 = sanitize_text_field($_POST['pref2'] ?? '');
    $pref3 = sanitize_text_field($_POST['pref3'] ?? '');
    $pref_other = sanitize_text_field($_POST['pref_other'] ?? '');

    // Prepare and execute the insert query
    $table_name = $wpdb->prefix . 'volunteers';
    $data = compact('volunteer_id', 'data_inscricao', 'first_name', 'last_name', 'post_code', 'morada', 'localidade', 'telemovel', 'volunteer_email', 'education', 'profession', 'encaminhado', 'a_date', 'pref1', 'pref2', 'pref3', 'pref_other');

    if ($is_edit) {
        $data['my_id'] = $my_id; // Include my_id for update
    }


    process_volunteer_registration($wpdb, $data);
}

// Helper function to get existing volunteer email
function get_existing_volunteer_email($wpdb, $my_id) {
    return $wpdb->get_var($wpdb->prepare("SELECT volunteer_email FROM {$wpdb->prefix}volunteers WHERE my_id = %d", $my_id));
}

// Helper function to check if email exists in the database, excluding current volunteer
function email_exists_in_db($wpdb, $email, $exclude_id = null) {
    $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}volunteers WHERE volunteer_email = %s", $email);
    if ($exclude_id !== null) {
        $query .= $wpdb->prepare(" AND my_id != %d", $exclude_id);
    }
    return $wpdb->get_var($query) > 0;
}
add_action('wp_ajax_register_volunteer', 'register_volunteer');
add_action('wp_ajax_nopriv_register_volunteer', 'register_volunteer');