<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  
// Enqueuing css and js
function oceanwp_child_style() {
    // Enqueue parent style
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue child style
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));

    // Enqueue Bootstrap and DataTables styles
    wp_enqueue_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css');
    wp_enqueue_style('datatables', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css');
    // wp_enqueue_style('bootstrap-icon', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css');

    // Enqueue DataTables scripts
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), '1.13.7', true);
    wp_enqueue_script('datatables-bs-js', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', array('jquery'), '1.13.7', true);

    // Enqueue custom scripts
    wp_enqueue_script('registration-script', get_stylesheet_directory_uri() . '/js/registration.js', array('jquery'), '1.0', true);
    wp_enqueue_script('form-validation', get_stylesheet_directory_uri() . '/js/form-validation.js', array('jquery'), '1.1', true);

	// Enqueue ajax script to edit volunteer
	wp_enqueue_script('volunteer-ajax', get_stylesheet_directory_uri() . '/js/volunteer-ajax.js', array('jquery'), null, true );
	
	// Localize the script with necessary data for all actions
	wp_localize_script('volunteer-ajax', 'volunteer_ajax_obj', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'register_nonce' => wp_create_nonce('register-volunteer-nonce'), // Nonce for registering
		'edit_nonce' => wp_create_nonce('edit-volunteer-nonce'), // Nonce for editing
		'fetchToEdit_nonce' => wp_create_nonce('fetch-volunteer-data-nonce'), // Nonce for editing
	));

	// Enqueue the DataTables initialization script
	wp_enqueue_script('volunteer-datatables', get_stylesheet_directory_uri() . '/js/volunteer-datatables.js', array('jquery'), '1.0', true);

	// Localize the script with necessary data for all actions
	wp_localize_script('volunteer-datatables', 'volunteer_datatables_obj', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'fetch_nonce' => wp_create_nonce('fetch-volunteers-nonce'),
		'delete_nonce' => wp_create_nonce('delete-volunteer-nonce') // Nonce for delete action
	));

    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'), '1.1', true);
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_style' );

// Function to add data attribute to the registration script tag
function add_data_to_registration_script($tag, $handle, $src) {
    global $registration_message;

    if ($handle === 'registration-script') {
        return '<script src="' . esc_url($src) . '" data-message="' . esc_attr($registration_message) . '"></script>';
    }

    return $tag;
}

// New ajax code to register volunteer starts form here
function register_volunteer() {
    global $wpdb; // database connection
	check_ajax_referer('register-volunteer-nonce', 'security');
	
	// Extracting, Validate and sanitizing input data
	$volunteer_id = isset($_POST['volunteer_id']) && is_numeric($_POST['volunteer_id']) && $_POST['volunteer_id'] > 0 ? intval($_POST['volunteer_id']) : null;
	//validate data_inscricao date
	$data_inscricao = isset($_POST['data_inscricao']) ? $_POST['data_inscricao'] : '';
	$validated_date = DateTime::createFromFormat('Y-m-d', $data_inscricao);
	$is_valid_date = $validated_date && $validated_date->format('Y-m-d') === $data_inscricao;
	// Sanitize the valid date
	$data_inscricao = sanitize_text_field($data_inscricao);

    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
	$is_valid_first_name = !empty($first_name) && ctype_alpha(str_replace(' ', '', $first_name));
	if (!$is_valid_first_name) {
		// Handle the error, e.g., by sending a JSON error response or setting an error flag
		wp_send_json_error('Invalid first name. Please enter a valid text.');
		wp_die();
	}

    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
	$is_valid_last_name = !empty($last_name) && ctype_alpha(str_replace(' ', '', $last_name));
	if (!$is_valid_last_name) {
		// Handle the error, e.g., by sending a JSON error response or setting an error flag
		wp_send_json_error('Invalid first name. Please enter a valid text.');
		wp_die();
	}



    $post_code = isset($_POST['post_code']) ? sanitize_text_field($_POST['post_code']) : '';
	$morada = isset($_POST['morada']) ? sanitize_text_field($_POST['morada']) : '';
	$localidade = isset($_POST['localidade']) ? sanitize_text_field($_POST['localidade']) : '';
	$telemovel = isset($_POST['telemovel']) ? preg_replace('/[^\d\+]|^\+?(00)/', '', $_POST['telemovel']) : '';
	$volunteer_email = isset($_POST['volunteer_email']) ? sanitize_email($_POST['volunteer_email']) : '';
	$education = isset($_POST['education']) ? sanitize_text_field($_POST['education']) : '';
	$profession = isset($_POST['profession']) ? sanitize_text_field($_POST['profession']) : '';
	$encaminhado = isset($_POST['encaminhado']) ? sanitize_text_field($_POST['encaminhado']) : '';
	
	// Validate and sanitize 'a_date'
	$a_date = isset($_POST['a_date']) ? $_POST['a_date'] : '';
	$validated_a_date = DateTime::createFromFormat('Y-m-d', $a_date);
	$is_valid_a_date = $validated_a_date && $validated_a_date->format('Y-m-d') === $a_date;
	// Sanitize the valid date
	$a_date = sanitize_text_field($a_date);

	$pref1 = isset($_POST['pref1']) ? sanitize_text_field($_POST['pref1']) : '';
	$pref2 = isset($_POST['pref2']) ? sanitize_text_field($_POST['pref2']) : '';
	$pref3 = isset($_POST['pref3']) ? sanitize_text_field($_POST['pref3']) : '';
	$pref_other = isset($_POST['pref_other']) ? sanitize_text_field($_POST['pref_other']) : '';


	// Additional validation check
	if (!$is_valid_date || !$is_valid_a_date || empty($first_name) || empty($last_name) ) {
		wp_send_json_error('Validation failed.');
		wp_die();
	}
    // Prepare and execute the insert query
    $table_name = $wpdb->prefix . 'volunteers'; // Replace with your table name

	//prepare the data
	$data = array(
		'volunteer_id' => $volunteer_id,
		'data_inscricao' => $data_inscricao,
		'first_name' => $first_name,
		'last_name' => $last_name,
		'post_code' => $post_code,
		'morada' => $morada,
		'localidade' => $localidade,
		'telemovel' => $telemovel,
		'volunteer_email' => $volunteer_email,
		'education' => $education,
		'profession' => $profession,
		'encaminhado' => $encaminhado,
		'a_date' => $a_date,
		'pref1' => $pref1,
		'pref2' => $pref2,
		'pref3' => $pref3,
		'pref_other' => $pref_other,
	);
	//data format
	$format = array(
		'%d', // volunteer_id
		'%s', // $data_inscricao
		'%s', // first_name
		'%s', // last_name
		'%s', // post_code
		'%s', // morada
		'%s', // localidade
		'%d', // telemovel
		'%s', // volunteer_email
		'%s', // education
		'%s', // profession
		'%s', // encaminhado
		'%s', // a_date
		'%s', // pref1
		'%s', // pref2
		'%s', // pref3
		'%s', // pref_other
	);
    if ($wpdb->insert($table_name, $data, $format)) {
        wp_send_json_success('Volunteer registered successfully.');
    } else {
        wp_send_json_error('Error in registering volunteer.');
    }

    wp_die(); // to terminate immediately and return a proper response
}

add_action('wp_ajax_register_volunteer', 'register_volunteer'); // Hook for logged-in users
add_action('wp_ajax_nopriv_register_volunteer', 'register_volunteer'); // Hook for non-logged-in users

// New ajax code to edit volunteer starts form here
function edit_volunteer() {
    global $wpdb; // Assuming $wpdb is your database connection
	check_ajax_referer('edit-volunteer-nonce', 'security');

    // Extracting and validating 'volunteer_id'
    $volunteer_id = isset($_POST['volunteer_id']) && is_numeric($_POST['volunteer_id']) && $_POST['volunteer_id'] > 0 ? intval($_POST['volunteer_id']) : null;
    if (!$volunteer_id) {
        wp_send_json_error('Invalid Volunteer ID');
        wp_die();
    }

	$data_inscricao = isset($_POST['data_inscricao']) ? $_POST['data_inscricao'] : '';
	$validated_date = DateTime::createFromFormat('Y-m-d', $data_inscricao);
	$is_valid_date = $validated_date && $validated_date->format('Y-m-d') === $data_inscricao;
	// Sanitize the valid date
	$data_inscricao = sanitize_text_field($data_inscricao);

	// Extract, validate, and sanitize input data
	$first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
	$is_valid_first_name = !empty($first_name) && ctype_alpha(str_replace(' ', '', $first_name));
	if (!$is_valid_first_name) {
		// Handle the error, e.g., by sending a JSON error response or setting an error flag
		wp_send_json_error('Invalid first name. Please enter a valid text.');
		wp_die();
	}

    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
	$is_valid_last_name = !empty($last_name) && ctype_alpha(str_replace(' ', '', $last_name));
	if (!$is_valid_last_name) {
		// Handle the error, e.g., by sending a JSON error response or setting an error flag
		wp_send_json_error('Invalid first name. Please enter a valid text.');
		wp_die();
	}
	
    // ... similar for other fields
	$post_code = isset($_POST['post_code']) ? sanitize_text_field($_POST['post_code']) : '';
	$morada = isset($_POST['morada']) ? sanitize_text_field($_POST['morada']) : '';
	$localidade = isset($_POST['localidade']) ? sanitize_text_field($_POST['localidade']) : '';
	$telemovel = isset($_POST['telemovel']) ? preg_replace('/[^\d\+]|^\+?(00)/', '', $_POST['telemovel']) : '';
	$volunteer_email = isset($_POST['volunteer_email']) ? sanitize_email($_POST['volunteer_email']) : '';
	$education = isset($_POST['education']) ? sanitize_text_field($_POST['education']) : '';
	$profession = isset($_POST['profession']) ? sanitize_text_field($_POST['profession']) : '';
	$encaminhado = isset($_POST['encaminhado']) ? sanitize_text_field($_POST['encaminhado']) : '';
	
	$a_date = isset($_POST['a_date']) ? $_POST['a_date'] : '';
	// Validate and sanitize 'a_date'
	$a_date = isset($_POST['a_date']) ? $_POST['a_date'] : '';
	$validated_a_date = DateTime::createFromFormat('Y-m-d', $a_date);
	$is_valid_a_date = $validated_a_date && $validated_a_date->format('Y-m-d') === $a_date;
	// Sanitize the valid date
	$a_date = sanitize_text_field($a_date);
	$pref1 = isset($_POST['pref1']) ? sanitize_text_field($_POST['pref1']) : '';
	$pref2 = isset($_POST['pref2']) ? sanitize_text_field($_POST['pref2']) : '';
	$pref3 = isset($_POST['pref3']) ? sanitize_text_field($_POST['pref3']) : '';
	$pref_other = isset($_POST['pref_other']) ? sanitize_text_field($_POST['pref_other']) : '';

    // Prepare and execute the update query
	$table_name = $wpdb->prefix . 'volunteers'; // Replace with your table name

	//prepare the data
	$data = array(
		'volunteer_id' => $volunteer_id,
		'data_inscricao' => $data_inscricao,
		'first_name' => $first_name,
		'last_name' => $last_name,
		'post_code' => $post_code,
		'morada' => $morada,
		'localidade' => $localidade,
		'telemovel' => $telemovel,
		'volunteer_email' => $volunteer_email,
		'education' => $education,
		'profession' => $profession,
		'encaminhado' => $encaminhado,
		'a_date' => $a_date,
		'pref1' => $pref1,
		'pref2' => $pref2,
		'pref3' => $pref3,
		'pref_other' => $pref_other,
	);
	//data format
	$format = array(
		'%d', // volunteer_id
		'%s', // $data_inscricao
		'%s', // first_name
		'%s', // last_name
		'%s', // post_code
		'%s', // morada
		'%s', // localidade
		'%d', // telemovel
		'%s', // volunteer_email
		'%s', // education
		'%s', // profession
		'%s', // encaminhado
		'%s', // a_date
		'%s', // pref1
		'%s', // pref2
		'%s', // pref3
		'%s', // pref_other
	);

    if ($wpdb->update($table_name, $data, array('ID' => $volunteer_id), $format, array('%d')) !== false) {
        wp_send_json_success('Volunteer updated successfully.');
    } else {
        wp_send_json_error('Error in updating volunteer.');
    }

    wp_die(); // to terminate immediately and return a proper response
}

add_action('wp_ajax_edit_volunteer', 'edit_volunteer'); // Hook for logged-in users
add_action('wp_ajax_nopriv_edit_volunteer', 'edit_volunteer'); // Hook for non-logged-in users

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

// Ajax handler for deleting a volunteer
function delete_volunteer() {
    global $wpdb;
    check_ajax_referer('delete-volunteer-nonce', 'security');

    // $volunteer_id = isset($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : 0;
	// Extracting and validating 'volunteer_id'
    $volunteer_id = isset($_POST['volunteer_id']) && is_numeric($_POST['volunteer_id']) && $_POST['volunteer_id'] > 0 ? intval($_POST['volunteer_id']) : null;
    if (!$volunteer_id) {
        wp_send_json_error('Invalid Volunteer ID');
        wp_die();
    }

    $table_name = $wpdb->prefix . 'volunteers';
    $result = $wpdb->delete($table_name, array('id' => $volunteer_id), array('%d'));

    if ($result) {
        wp_send_json_success('Volunteer deleted successfully');
    } else {
        wp_send_json_error('Error in deleting volunteer');
    }

    wp_die(); // Required to terminate and return a proper response
}

add_action('wp_ajax_delete_volunteer', 'delete_volunteer'); // Hook for logged-in users
add_action('wp_ajax_nopriv_delete_volunteer', 'delete_volunteer'); // Hook for non-logged-in users

//functions to populate the front end form with specific id, to be edited any fields
function fetch_volunteer_data() {
    global $wpdb;
    check_ajax_referer('fetch-volunteer-data-nonce', 'security'); //check referer er first parameter diyei wp_create_nonce kora lage

    // $volunteer_id = isset($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : 0;
	// Extracting and validating 'volunteer_id'
	$volunteer_id = isset($_POST['volunteer_id']) && is_numeric($_POST['volunteer_id']) && $_POST['volunteer_id'] > 0 ? intval($_POST['volunteer_id']) : null;
	if (!$volunteer_id) {
		wp_send_json_error('Invalid Volunteer ID');
		wp_die();
	}

    $table_name = $wpdb->prefix . 'volunteers';
    $volunteer_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $volunteer_id), ARRAY_A);

    if ($volunteer_data) {
        wp_send_json_success($volunteer_data);
    } else {
        wp_send_json_error('Volunteer not found');
    }

    wp_die();
}

add_action('wp_ajax_fetch_volunteer_data', 'fetch_volunteer_data');
add_action('wp_ajax_nopriv_fetch_volunteer_data', 'fetch_volunteer_data');

