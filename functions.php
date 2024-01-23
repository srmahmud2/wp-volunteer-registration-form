<?php
/**
 * OceanWP Child Theme Functions and Definitions
 */

 add_action('admin_init', function() {
    $required_php_version = '7'; // Set your required PHP version

    if (version_compare(PHP_VERSION, $required_php_version, '<')) {
        add_action('admin_notices', function() use ($required_php_version) {
            echo '<div class="notice notice-error"><p>';
            echo 'This theme requires PHP version ' . $required_php_version . ' or higher. Your current PHP version is ' . PHP_VERSION . '.';
            echo '</p></div>';
        });
    }
});
add_action('after_switch_theme', function() {
    $required_php_version = '7'; // Set your required PHP version

    if (version_compare(PHP_VERSION, $required_php_version, '<')) {
        switch_theme(get_option('stylesheet'));
        remove_action('after_switch_theme', 'check_php_version');

        add_action('admin_notices', function() use ($required_php_version) {
            echo '<div class="notice notice-error"><p>';
            echo 'Switched back to the previous theme because your PHP version is lower than the required ' . $required_php_version . '.';
            echo '</p></div>';
        });
    }
});







// Enqueue scripts and styles
require get_stylesheet_directory() . '/inc/enqueue-script.php';

// Function to add data attribute to the registration script tag
function add_data_to_registration_script($tag, $handle, $src) {
    global $registration_message;

    if ($handle === 'registration-script') {
        return '<script src="' . esc_url($src) . '" data-message="' . esc_attr($registration_message) . '"></script>';
    }

    return $tag;
}

// AJAX handlers
require get_stylesheet_directory() . '/inc/register-ajax.php';
require get_stylesheet_directory() . '/inc/edit-ajax.php';
require get_stylesheet_directory() . '/inc/fetch-ajax.php';
require get_stylesheet_directory() . '/inc/delete-ajax.php';
