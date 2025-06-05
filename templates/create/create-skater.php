<?php
/**
 * Template: Create or Edit Skater
 */
acf_form_head();
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

// Get skater ID from query var
$skater_id = get_query_var('edit_skater');
$is_edit   = $skater_id && get_post_type($skater_id) === 'skater';

$post_id = $is_edit ? intval($skater_id) : 'new_post';

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Skater Profile' : 'Create New Skater') . '</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

// ACF form config
acf_form([
    'post_id'      => $post_id,
    'post_title'   => false,
    'post_content' => false,
    'field_groups' => ['group_6819871fd44c9'], // ✅ Skater Profile group
    'new_post'     => [
        'post_type'   => 'skater',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Skater' : 'Create Skater',
    'uploader'     => 'wp',
    'return'       => site_url('/coach-dashboard'),
]);

echo '</div>';
get_footer();
