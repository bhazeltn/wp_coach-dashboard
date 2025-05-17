<?php
/**
 * Template: Create or Edit Injury Log
 */

acf_form_head();
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

// Get post ID if editing
$post_id = get_query_var('edit_injury_log');
$is_edit = $post_id && is_numeric($post_id);

if (!is_user_logged_in()) {
    auth_redirect();
}

// Get skater slug for redirect (if available)
$skater = $is_edit ? get_field('injured_skater', $post_id) : null;
$skater_slug = $skater ? $skater->post_name : null;

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Injury Log' : 'Create New Injury Log') . '</h1>';

// Display the ACF form
acf_form([
    'post_id'      => $is_edit ? intval($post_id) : 'new_post',
    'post_title'   => false,
    'post_content' => false,
    'field_groups' => ['group_68242bb05b02a'], // Injury Details group
    'new_post'     => [
        'post_type'   => 'injury_log',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Injury Log' : 'Create Injury Log',
    'return'       => $skater_slug
        ? site_url('/skater/' . $skater_slug . '/')
        : site_url('/coach-dashboard'),
]);

echo '</div>';
get_footer();
