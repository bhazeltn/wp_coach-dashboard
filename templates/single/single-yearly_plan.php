<?php
/**
 * Template: View Single Yearly Plan (Formatted)
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

global $post;
setup_postdata($post);

// === Yearly Plan Fields ===
$season = get_field('season');
$skaters = get_field('skater');
$season_dates = get_field('season_dates');
$goal = get_field('primary_season_goal');
$macrocycles = get_field('macrocycles');
$peak = get_field('peak_planning');
$eval = get_field('evaluation_strategy');
$notes = get_field('coach_notes');

// === Season Dates ===
$start_raw = $season_dates['start_date'] ?? '';
$end_raw = $season_dates['end_date'] ?? '';
$season_start = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw)->format('Ymd') : '';
$season_end = $end_raw ? DateTime::createFromFormat('d/m/Y', $end_raw)->format('Ymd') : '';
$start_fmt = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw)->format('F j, Y') : '';
$end_fmt = $end_raw ? DateTime::createFromFormat('d/m/Y', $end_raw)->format('F j, Y') : '';

// === Skater Setup ===
$skater = null;
$skater_name = '';
$skater_slug = '';

if ($skaters && is_array($skaters)) {
    $skater = $skaters[0];
    if ($skater) {
        $skater_name = get_the_title($skater);
        $skater_slug = $skater->post_name;
    }
}

// Heading
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/header.php';

// === Primary Goal ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/goal.php';

// === Macrocycles ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/macrocycles.php';

// === Peak Planning ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/peak.php';

// === Weekly Plans Preview ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/weekly-plans.php';

// === Competitions ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/competitions.php';


// === Goals Section ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/goals.php';

// === Meetings This Season ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/meetings.php';

// === Injury Log This Season ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/injuries.php';

// === Action Block ===
include plugin_dir_path(__FILE__) . '../partials/yearly-plan/actions.php';

get_footer();