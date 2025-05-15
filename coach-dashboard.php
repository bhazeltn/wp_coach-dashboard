<?php
/**
 * Plugin Name: Coach Dashboard
 * Description: Modular frontend dashboard for coaches to manage skaters and plans.
 */

 ChatGPT said:
 Here’s the full block of include_once lines to drop into your coach-dashboard.php file, ideally just after your functions.php line.
 
 🔧 coach-dashboard.php → Load All CPTs
 php
 Copy
 Edit
 // === Core Includes ===
 include_once plugin_dir_path(__FILE__) . 'functions.php';
 
 // === Custom Post Types ===
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-goal.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-skater.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-weekly_plan.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-yearly_plan.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-session_log.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-competition.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-competition_result.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-program.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-meeting_log.php';
 include_once plugin_dir_path(__FILE__) . 'includes/cpt-injury_log.php';
 
 // === Routing, Redirects, Helpers ===
 include_once plugin_dir_path(__FILE__) . 'includes/routing.php';
 include_once plugin_dir_path(__FILE__) . 'includes/acf-redirects.php';
 include_once plugin_dir_path(__FILE__) . 'includes/helper-functions.php';

// === Enqueue Styles ===
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'coach-dashboard-style',
        plugin_dir_url(__FILE__) . 'css/dashboard-style.css',
        [],
        '1.0'
    );
});

// === Shortcode Dashboard Loader ===
add_shortcode('coach_dashboard', 'render_coach_dashboard');

function render_coach_dashboard() {
    if (!is_user_logged_in()) {
        auth_redirect();
    }

    $current_user = wp_get_current_user();
    if (!in_array('coach', (array) $current_user->roles) && !in_array('administrator', (array) $current_user->roles)) {
        return '<p>You do not have permission to view this dashboard.</p>';
    }

    ob_start();
    echo '<div class="wrap coach-dashboard">';
    coach_dashboard_section('skater_overview');
    coach_dashboard_section('yearly_plan_summary');
    coach_dashboard_section('weekly_plan_tracker');
    coach_dashboard_section('goal_tracker');
    coach_dashboard_section('competition_results');
    coach_dashboard_section('session_logs');
    echo '</div>';

    return ob_get_clean();
}

function coach_dashboard_section($section) {
    $fn = 'coach_section_' . $section;
    if (function_exists($fn)) {
        call_user_func($fn);
    } else {
        echo '<h2>' . ucfirst(str_replace('_', ' ', $section)) . '</h2><p>Section not implemented yet.</p>';
    }
}

// === Skater Section Includes ===
function coach_section_skater_overview() {
    include plugin_dir_path(__FILE__) . 'sections/skater/skater-overview.php';
}
function coach_section_yearly_plan_summary() {
    include plugin_dir_path(__FILE__) . 'sections/skater/yearly-plan-summary.php';
}
function coach_section_weekly_plan_tracker() {
    include plugin_dir_path(__FILE__) . 'sections/skater/weekly-plan-tracker.php';
}
function coach_section_goal_tracker() {
    include plugin_dir_path(__FILE__) . 'sections/skater/goal-tracker.php';
}
function coach_section_competition_results() {
    include plugin_dir_path(__FILE__) . 'sections/skater/competition-results.php';
}
function coach_section_session_logs() {
    include plugin_dir_path(__FILE__) . 'sections/skater/session-logs.php';
}
