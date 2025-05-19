<?php
/**
 * Template: Create or Edit Session Log
 */

acf_form_head();
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

// Get post ID if editing
$post_id = get_query_var('edit_session_log');
$is_edit = $post_id && is_numeric($post_id);

if (!is_user_logged_in()) {
    auth_redirect();
}

// Get skater object (if editing) or from global context
$skater = $is_edit ? get_field('skater', $post_id) : null;
$skater_slug = $skater ? $skater->post_name : null;

$prefill_skater_id = !$is_edit
    ? ($_GET['skater_id'] ?? ($GLOBALS['skater_id'] ?? null))
    : null;

// Prefill logic for skater dropdown
add_filter('acf/load_field/name=skater', function ($field) use ($prefill_skater_id) {
    if ($prefill_skater_id) {
        $field['default_value'] = $prefill_skater_id;
    }
    return $field;
});

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Session Log' : 'Create New Session Log') . '</h1>';

// Render ACF form
acf_form([
    'post_id'      => $is_edit ? intval($post_id) : 'new_post',
    'post_title'   => false,
    'post_content' => false,
    'field_groups' => ['group_681c4231d6279'], // Session Log Details
    'new_post'     => [
        'post_type'   => 'session_log',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Session Log' : 'Create Session Log',
    'return'       => $skater_slug
        ? site_url('/skater/' . $skater_slug . '/')
        : site_url('/coach-dashboard'),
]);

echo '</div>';
get_footer();
