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
    wp_enqueue_style('bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css');
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css');

    // Enqueue DataTables scripts
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), '1.13.7', true);
    wp_enqueue_script('datatables-bs-js', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', array('jquery'), '1.13.7', true);

    // Enqueue custom scripts
    wp_enqueue_script('registration-script', get_stylesheet_directory_uri() . '/js/registration.js', array('jquery'), '1.0', true);
    wp_enqueue_script('form-validation', get_stylesheet_directory_uri() . '/js/form-validation.js', array('jquery'), '1.1', true);
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'), '1.1', true);
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_style' );


// when clicked on register
function handle_volunteer_registration() {
	if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == 'register_volunteer_form') {
        if (isset($_POST['register']) && check_admin_referer('volunteer_form_nonce', 'nonce_field')) {
            global $wpdb;
			$table_name = $wpdb->prefix . 'volunteers';
			
			// Server-side validation and sanitization
			$volunteer_id = isset($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : '';
			
			// Validate and sanitize 'data_inscricao'
			$data_inscricao = isset($_POST['data_inscricao']) ? $_POST['data_inscricao'] : '';
			$validated_date = DateTime::createFromFormat('Y-m-d', $data_inscricao);
			$is_valid_date = $validated_date && $validated_date->format('Y-m-d') === $data_inscricao;

			// Sanitize the valid date
			$data_inscricao = sanitize_text_field($data_inscricao);

			$first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
			$last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
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

			// Collect error messages
            $error_messages = [];
            if (!$is_valid_date) {
                $error_messages['data_inscricao'] = 'Please check the inscricao date.';
            }
            if (!$is_valid_a_date) {
                $error_messages['a_date'] = 'Please check the a_date.';
            }

			// Handle errors
            if (!empty($error_messages)) {
                // Pass error messages back to the form
                // For example, via query parameters or a session variable
                // Redirect to form with error messages
                wp_redirect(home_url('/volunteer-registration-form/?errors=' . urlencode(json_encode($error_messages))));
                exit;
            }

			$pref1 = isset($_POST['pref1']) ? sanitize_text_field($_POST['pref1']) : '';
			$pref2 = isset($_POST['pref2']) ? sanitize_text_field($_POST['pref2']) : '';
			$pref3 = isset($_POST['pref3']) ? sanitize_text_field($_POST['pref3']) : '';
			$pref_other = isset($_POST['pref_other']) ? sanitize_text_field($_POST['pref_other']) : '';

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
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			);
			// Prepared statement for SQL Insert
			$result = $wpdb->insert($table_name,$data,$format);
			
			// Check for errors or success
			if (is_wp_error($result)) {
				// Handle error
				$registration_message = "Unable to register. Please try again later. " . esc_html($result->get_error_message());
			} elseif ($result) {
				// Success message
				$registration_message = "Registration successful!";
			} else {
				// Failure message
				$registration_message = "Registration failed. Please try again.";
			}
			
			// Store the message in a transient or user meta instead of session
			set_transient('registration_message', $registration_message, 60*60); // 1 hour expiry

			// Redirect to prevent form resubmission
			wp_redirect(home_url('/volunteer-registration-form/?message=' . urlencode($registration_message)));
			exit;			
		}
		
	}
}

add_action('init', 'handle_volunteer_registration');


// Function to add data attribute to the registration script tag
function add_data_to_registration_script($tag, $handle, $src) {
    global $registration_message;

    if ($handle === 'registration-script') {
        return '<script src="' . esc_url($src) . '" data-message="' . esc_attr($registration_message) . '"></script>';
    }

    return $tag;
}