<?php
/**
 * Plugin Name: Skater Planning Dashboard
 * Description: Modular frontend dashboard for coaches to manage skaters, training plans, and performance data.
 */

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
include_once plugin_dir_path(__FILE__) . 'includes/cpt-gap_analysis.php';
include_once plugin_dir_path(__FILE__) . 'includes/cpt-ctes-requirements.php';


// === Routing, Redirects, Helpers ===
include_once plugin_dir_path(__FILE__) . 'includes/routing.php';
include_once plugin_dir_path(__FILE__) . 'includes/acf-redirects.php';
include_once plugin_dir_path(__FILE__) . 'includes/helper-functions.php';
include_once plugin_dir_path(__FILE__) . 'includes/permissions.php';
include_once plugin_dir_path(__FILE__) . 'includes/coach-sections.php';

// === Conditional Style Loader (based on route context) ===
add_action('template_redirect', function () {
    if (get_query_var('skater_view')) {
        $GLOBALS['is_skater_view'] = true;
    }

    if (intval(get_query_var('coach_dashboard')) === 1) {
        $GLOBALS['is_coach_dashboard'] = true;
    }

    $is_skater = $GLOBALS['is_skater_view'] ?? false;
    $is_coach  = $GLOBALS['is_coach_dashboard'] ?? false;

    if ($is_skater || $is_coach) {
        wp_enqueue_style(
            'skater-planning-dashboard-style',
            '/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css',
            [],
            '1.0'
        );
    }
}, 20);


// === Conditional Style Loader (based on route context) ===
add_action('wp_enqueue_scripts', function () {
    // We check if either of our main query variables are set.
    if (get_query_var('skater_view') || get_query_var('coach_dashboard')) {
        wp_enqueue_style(
            'skater-planning-dashboard-style',
            plugin_dir_url(__FILE__) . 'css/dashboard-style.css',
            [],
            '1.4' // I've bumped the version number to help break browser cache
        );
    }
});