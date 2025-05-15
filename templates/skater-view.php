<?php
/**
 * Template for Skater View Page
 */

$skater_slug = get_query_var('skater_view');
$skater = get_page_by_path($skater_slug, OBJECT, 'skater');

if (!$skater) {
    wp_die('Skater not found.');
}

$skater_id = $skater->ID;
$GLOBALS['skater_id'] = $skater_id;
echo '<div class="coach-dashboard skater-view">';
echo '<p><a class="button" href="' . site_url('/coach-dashboard') . '">← Back to Coach Dashboard</a></p>';


echo '<h1>' . esc_html(get_the_title($skater_id)) . '</h1>';

// Load stylesheet
echo '<link rel="stylesheet" href="' . plugin_dir_url(__DIR__) . 'css/dashboard-style.css" type="text/css" media="all">';



// --- Modular Sections ---
include plugin_dir_path(__DIR__) . 'sections/skater/yearly-plans.php';
include plugin_dir_path(__DIR__) . 'sections/skater/weekly-plans.php';
include plugin_dir_path(__DIR__) . 'sections/skater/goals.php';
include plugin_dir_path(__DIR__) . 'sections/skater/competitions-upcoming.php';
include plugin_dir_path(__DIR__) . 'sections/skater/competitions-results.php';
include plugin_dir_path(__DIR__) . 'sections/skater/session-logs.php';
include plugin_dir_path(__DIR__) . 'sections/skater/missed-goals.php';

echo '</div>';
