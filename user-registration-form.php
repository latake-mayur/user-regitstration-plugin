<?php
/*
Plugin Name: Simple User Registration Form
Description: A simple plugin to show a registration form and save data in a custom table.
Version: 1.1
Author: Mayur Latake
*/

add_shortcode('user_registration_form', 'urf_show_form');

function urf_show_form() {
    ob_start();

    if (isset($_GET['success']) && $_GET['success'] === '1') {
        echo '<p style="color: green; font-weight: bold;">Thank you! Your data has been submitted.</p>';
    }

    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="urf_form_handler">
        <?php wp_nonce_field('urf_form_submit', 'urf_form_nonce'); ?>

        <label>Name: <input type="text" name="name" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Gender:
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </label><br>
        <label>State:
            <select name="state" required>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Kerala">Kerala</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
            </select>
        </label><br>
        <label>City:
            <select name="city" required>
                <option value="Pune">Pune</option>
                <option value="Mumbai">Mumbai</option>
            </select>
        </label><br>
        <label>Branch:
            <select name="branch" required>
                <option value="Art">Art</option>
                <option value="Science">Science</option>
                <option value="Commerce">Commerce</option>
            </select>
        </label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Submit</button>
    </form>
    <?php

    return ob_get_clean();
}

// Handle form submission
add_action('admin_post_urf_form_handler', 'urf_handle_form');
add_action('admin_post_nopriv_urf_form_handler', 'urf_handle_form');

function urf_handle_form() {
    if (!isset($_POST['urf_form_nonce']) || !wp_verify_nonce($_POST['urf_form_nonce'], 'urf_form_submit')) {
        wp_die('Security check failed');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'formdata';

    $data = [
        'name'     => sanitize_text_field($_POST['name']),
        'gender'   => sanitize_text_field($_POST['gender']),
        'email'    => sanitize_email($_POST['email']),
        'password' => wp_hash_password($_POST['password']),
        'state'    => sanitize_text_field($_POST['state']),
        'city'     => sanitize_text_field($_POST['city']),
        'branch'   => sanitize_text_field($_POST['branch']),
    ];

    $wpdb->insert($table, $data);

    wp_redirect(add_query_arg('success', '1', wp_get_referer()));
    exit;
}
