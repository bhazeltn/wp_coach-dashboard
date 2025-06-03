<?php
/**
 * Template: Create or Edit Goal
 */
 
acf_form_head();
get_header();

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';
echo '<div class="wrap coach-dashboard">';

// === Determine if we're editing an existing goal ===
$goal_id = isset($_GET['goal_id']) ? intval($_GET['goal_id']) : 0;
$is_edit = ($goal_id && get_post_type($goal_id) === 'goal');

// === Determine skater context ===
$skater_raw = $is_edit
    ? get_field('skater', $goal_id)
    : ($_GET['skater_id'] ?? null);

$skater_id = null;

if (is_array($skater_raw)) {
    $skater_id = $skater_raw[0]->ID ?? null;
} elseif (is_numeric($skater_raw)) {
    $skater_id = intval($skater_raw);
}

// === Page Title ===
echo '<h1>' . esc_html($is_edit ? 'Update Goal' : 'Create New Goal') . '</h1>';


$skaters = get_posts([
    'post_type' => 'skater',
    'post_status' => 'publish',
    'numberposts' => -1,
]);

// === ACF Form ===
acf_form([
    'post_id'       => $is_edit ? $goal_id : 'new_post',
    'post_title'    => true,
    'post_content'  => false,
    'new_post'      => $is_edit ? false : [
        'post_type'   => 'goal',
        'post_status' => 'publish'
    ],
    'submit_value'  => $is_edit ? 'Update Goal' : 'Create Goal',
    'return'        => $skater_id
        ? site_url('/skater/' . get_post_field('post_name', $skater_id) . '/')
        : site_url('/coach-dashboard'),
    'field_groups'  => ['group_681c4115a026f'], // ACF Group: Goal
]);

get_footer();