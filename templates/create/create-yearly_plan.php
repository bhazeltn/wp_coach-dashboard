<?php
/**
 * Template: Create or Edit Yearly Plan
 */

acf_form_head();
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

// Determine if we're editing
$post_id = get_query_var('edit_yearly_plan');
$is_edit = $post_id && is_numeric($post_id);

// For skater prefill or redirect
$skater = $is_edit ? get_field('skater', $post_id) : null;
$skater_slug = $skater ? $skater->post_name : null;

$prefill_skater_id = !$is_edit
    ? ($_GET['skater_id'] ?? ($GLOBALS['skater_id'] ?? null))
    : null;

// Prefill skater field on create
add_filter('acf/load_field/name=skater', function ($field) use ($prefill_skater_id) {
    if ($prefill_skater_id) {
        $field['default_value'] = $prefill_skater_id;
    }
    return $field;
});

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Yearly Plan' : 'Create New Yearly Plan') . '</h1>';

acf_form([
    'post_id'      => $is_edit ? intval($post_id) : 'new_post',
    'post_title'   => false,
    'post_content' => false,
    'field_groups' => ['group_681991c0d3817'],
    'new_post'     => [
        'post_type'   => 'yearly_plan',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Yearly Plan' : 'Create Yearly Plan',
    'return'       => $skater_slug
        ? site_url('/skater/' . $skater_slug . '/')
        : site_url('/coach-dashboard'),
]);

echo '</div>';
get_footer();
