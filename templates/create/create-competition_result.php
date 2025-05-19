<?php
/**
 * Create or Edit Competition Result
 */

acf_form_head();
get_header();

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . (get_query_var('edit_competition_result') ? 'Update' : 'Create') . ' Competition Result</h1>';

$result_id = get_query_var('result_id');
$is_edit = !empty($result_id);

$post_id = $is_edit ? intval($result_id) : 'new_post';

// Try to determine skater for redirect
$skater_id = null;
if ($is_edit && $post_id && get_post_type($post_id) === 'competition_result') {
    $skater = get_field('skater', $post_id);
    if ($skater instanceof WP_Post) {
        $skater_id = $skater->ID;
    }
} elseif (!$is_edit && isset($_GET['skater_id'])) {
    $skater_id = intval($_GET['skater_id']);
}

acf_form([
    'post_id'       => $post_id,
    'post_title'    => true,
    'post_content'  => false,
    'new_post'      => [
        'post_type'   => 'competition_result',
        'post_status' => 'publish',
    ],
    'submit_value'  => $is_edit ? 'Update Result' : 'Create Result',
    'return'        => $skater_id
        ? site_url('/skater/' . get_post_field('post_name', $skater_id) . '/')
        : site_url('/coach-dashboard'),
    'field_groups'  => ['group_681c30ea05053'],
]);

echo '</div>';

get_footer();
