<?php
/**
 * Template: Create or Edit Competition
 */
acf_form_head();
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

$comp_id = get_query_var('edit_competition');
$is_edit = $comp_id && get_post_type($comp_id) === 'competition';

$post_id = $is_edit ? intval($comp_id) : 'new_post';
$return_url = $_SERVER['HTTP_REFERER'] ?? site_url('/coach-dashboard');


echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Competition' : 'Create New Competition') . '</h1>';

// Load the ACF form
acf_form([
    'post_id'      => $post_id,
    'post_title'   => true, // Event name/title
    'post_content' => false,
    'field_groups' => ['group_681c237c3ab7d'],
    'new_post'     => [
        'post_type'   => 'competition',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Competition' : 'Create Competition',
    'return'       => $return_url,
]);

echo '</div>';
get_footer();
