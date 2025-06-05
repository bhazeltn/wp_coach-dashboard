<?php
/**
 * Template: Create or Edit Gap Analysis
 */
acf_form_head();
get_header();

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

$gap_id = get_query_var('edit_gap_analysis');
$is_edit = $gap_id && get_post_type($gap_id) === 'gap_analysis';
$post_id = $is_edit ? intval($gap_id) : 'new_post';

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . ($is_edit ? 'Update Gap Analysis' : 'Create New Gap Analysis') . '</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'      => $post_id,
    'post_title'   => false, // You can disable and auto-generate from skater if needed
    'post_content' => false,
    'field_groups' => ['group_gap_analysis'], // ✅ Your imported ACF field group key
    'new_post'     => [
        'post_type'   => 'gap_analysis',
        'post_status' => 'publish',
    ],
    'submit_value' => $is_edit ? 'Update Gap Analysis' : 'Create Gap Analysis',
    'uploader'     => 'wp',
    'return'       => site_url('/coach-dashboard'),
]);

echo '</div>';
get_footer();
