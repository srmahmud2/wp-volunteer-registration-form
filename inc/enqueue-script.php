<?php
// Enqueuing css and js
function oceanwp_child_style() {
    // Enqueue parent style
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue child style
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));

    // Enqueue Bootstrap and DataTables styles
    wp_enqueue_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css');
    wp_enqueue_style('datatables', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css');

    // Enqueue DataTables scripts
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), '1.13.7', true);
    wp_enqueue_script('datatables-bs-js', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', array('jquery'), '1.13.7', true);

    // Enqueue custom scripts

    wp_enqueue_script('real-time-script', get_stylesheet_directory_uri() . '/js/real-time.js', array('jquery'), '1.0', true);
	// Localize the script with necessary data all actions
	wp_localize_script('real-time-script', 'volunteer_realtime_obj', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'checkId_nonce' => wp_create_nonce('unique_volunteer_id_nonce'),
		'checkEmail_nonce' => wp_create_nonce('unique_email_nonce'),
		'register_nonce' => wp_create_nonce('register-volunteer-nonce'), // Nonce for registering
	));
	wp_enqueue_script('registration-script', get_stylesheet_directory_uri() . '/js/registration.js', array('jquery'), '1.0', true);
    // wp_enqueue_script('form-validation', get_stylesheet_directory_uri() . '/js/form-validation.js', array('jquery'), '1.1', true);

	// Enqueue ajax script to edit volunteer
	// wp_enqueue_script('volunteer-ajax', get_stylesheet_directory_uri() . '/js/volunteer-ajax.js', array('jquery'), null, true );
	
	// Localize the script with necessary data for all actions
	// wp_localize_script('volunteer-ajax', 'volunteer_ajax_obj', array(
	// 	'ajaxurl' => admin_url('admin-ajax.php'),
	// 	'register_nonce' => wp_create_nonce('register-volunteer-nonce'), // Nonce for registering
	// 	'edit_nonce' => wp_create_nonce('edit-volunteer-nonce'), // Nonce for editing
	// 	'fetchToEdit_nonce' => wp_create_nonce('fetch-volunteer-data-nonce'), // Nonce for editing
	// ));

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
