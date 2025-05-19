<?php
/**
 * Template: Coach Dashboard View (Frontend Routed)
 */

if (!is_user_logged_in()) {
    auth_redirect();
}

$current_user = wp_get_current_user();
if (!in_array('coach', (array) $current_user->roles) && !in_array('administrator', (array) $current_user->roles)) {
    wp_die('You do not have permission to view this dashboard.');
}

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';



get_header();

echo '<div class="wrap coach-dashboard">';
echo '<h1>Coach Dashboard</h1>';

// === Dashboard Sections (modular coach view) ===
coach_dashboard_section('skater_overview');
coach_dashboard_section('injury_log_summary');
coach_dashboard_section('yearly_plan_summary');
coach_dashboard_section('weekly_plan_tracker');
coach_dashboard_section('competitions_upcoming');
coach_dashboard_section('goal_tracker');
coach_dashboard_section('competition_results');
include plugin_dir_path(__FILE__) . '../sections/coach/meeting-upcoming.php';
coach_dashboard_section('missed_goals');
coach_dashboard_section('session_logs');

echo '</div>';

get_footer();