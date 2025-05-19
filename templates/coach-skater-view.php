<?php
/**
 * Template: Coach View of a Specific Skater
 */

 if (!is_user_logged_in()) {
    auth_redirect();
}

$current_user = wp_get_current_user();
if (!in_array('coach', (array) $current_user->roles) && !in_array('administrator', (array) $current_user->roles)) {
    wp_die('You do not have permission to view this skater dashboard.');
}

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

get_header(); // ← This loads the <head> tag and enqueued styles/scripts

$skater_slug = get_query_var('skater_view');
$skater = get_page_by_path($skater_slug, OBJECT, 'skater');

if (!$skater) {
    wp_die('Skater not found.');
}

$skater_id = $skater->ID;
$GLOBALS['skater_id'] = $skater_id;

$edit_link  = get_edit_post_link($skater_id);
$level      = get_field('current_level', $skater_id);
$federation = get_field('federation', $skater_id);
$club       = get_field('home_club', $skater_id); // optional future field

echo '<div class="wrap coach-dashboard">';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">&larr; Back to Coach Dashboard</a></p>';
echo '<h1>' . esc_html(get_the_title($skater)) . '</h1>';

echo '<ul>';
if ($level)      echo '<li><strong>Level:</strong> ' . esc_html($level) . '</li>';
if ($federation) echo '<li><strong>Federation:</strong> ' . esc_html($federation) . '</li>';
if ($club)       echo '<li><strong>Home Club:</strong> ' . esc_html($club) . '</li>';
if ($edit_link)  echo '<li><a class="button small" href="' . esc_url($edit_link) . '">Edit Skater Info</a></li>';
echo '</ul>';

// SECTION 1: Injury Log
include plugin_dir_path(__FILE__) . '../sections/skater/injury-log.php';

// SECTION 2: Yearly Plans
include plugin_dir_path(__FILE__) . '../sections/skater/yearly-plans-summary.php';

// SECTION 3: Weekly Plans
include plugin_dir_path(__FILE__) . '../sections/skater/weekly-plans-tracker.php';

// SECTION 4: Goals
include plugin_dir_path(__FILE__) . '../sections/skater/goals.php';

// SECTION 5: Upcoming Competitions
include plugin_dir_path(__FILE__) . '../sections/skater/competitions-results.php';

// SECTION 6: Competition Results
include plugin_dir_path(__FILE__) . '../sections/skater/competitions-upcoming.php';

// SECTION 7: Session Logs
include plugin_dir_path(__FILE__) . '../sections/skater/session-logs.php';

// Section  : Meetings 
include plugin_dir_path(__FILE__) . '../sections/skater/meeting-upcoming.php';


// SECTION 8: Missed or Stalled Goals
include plugin_dir_path(__FILE__) . '../sections/skater/missed-goals.php';

echo '</div>';

get_footer(); // ← Closes the page, loads enqueued scripts if needed
