<?php
/**
 * Template: Create or Edit Weekly Plan
 */

acf_form_head();
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

// Determine if we're editing
$post_id = get_query_var('edit_weekly_plan');
$is_edit = $post_id && is_numeric($post_id);

if (!is_user_logged_in()) {
    auth_redirect();
}

// Get skater for redirect or prefill
$skater = $is_edit ? get_field('skater', $post_id) : null;
$skater_slug = $skater ? $skater->post_name : null;

$prefill_skater_id = !$is_edit
    ? ($_GET['skater_id'] ?? ($GLOBALS['skater_id'] ?? null))
    : null;

// Prefill Skater on create
add_filter('acf/load_field/name=skater', function ($field) use ($prefill_skater_id) {
    if ($prefill_skater_id) {
        $field['default_value'] = $prefill_skater_id;
    }
    return $field;
});

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Weekly Plan' : 'Create New Weekly Plan') . '</h1>';

acf_form([
    'post_id'      => $is_edit ? intval($post_id) : 'new_post',
    'post_title'   => false,
    'post_content' => false,
    'field_groups' => ['group_681c3d5d75241'], // Weekly Plan field group
    'new_post'     => [
        'post_type'   => 'weekly_plan',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Weekly Plan' : 'Create Weekly Plan',
    'return'       => $skater_slug
        ? site_url('/skater/' . $skater_slug . '/')
        : site_url('/coach-dashboard'),
]);

echo '</div>';
get_footer();
