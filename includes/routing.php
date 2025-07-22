<?php
/**
 * Handles custom routing for the Coach Operating System plugin.
 *
 * @package Coach_Operating_System
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Adds custom rewrite rules for dashboard pages.
 */
function spd_add_rewrite_rules() {
    // Dashboard and View Pages
    add_rewrite_rule( '^coach-dashboard/?$', 'index.php?coach_dashboard=1', 'top' );
    add_rewrite_rule( '^skater-dashboard/?$', 'index.php?skater_dashboard=1', 'top' );
    add_rewrite_rule( '^competitions/?$', 'index.php?view_all_competitions=1', 'top' );

    // Create Pages
    add_rewrite_rule( '^create-skater/?$', 'index.php?create_skater=1', 'top' );
    add_rewrite_rule( '^create-competition/?$', 'index.php?create_competition=1', 'top' );
    add_rewrite_rule( '^create-competition-result/?$', 'index.php?create_competition_result=1', 'top' );
    add_rewrite_rule( '^create-gap-analysis/?$', 'index.php?create_gap_analysis=1', 'top' );
    add_rewrite_rule( '^create-goal/?$', 'index.php?create_goal=1', 'top' );
    add_rewrite_rule( '^create-injury-log/?$', 'index.php?create_injury_log=1', 'top' );
    add_rewrite_rule( '^create-meeting-log/?$', 'index.php?create_meeting_log=1', 'top' );
    add_rewrite_rule( '^create-program/?$', 'index.php?create_program=1', 'top' );
    add_rewrite_rule( '^create-session-log/?$', 'index.php?create_session_log=1', 'top' );
    add_rewrite_rule( '^create-weekly-plan/?$', 'index.php?create_weekly_plan=1', 'top' );
    add_rewrite_rule( '^create-yearly-plan/?$', 'index.php?create_yearly_plan=1', 'top' );
    add_rewrite_rule( '^create-ctes/?$', 'index.php?create_ctes=1', 'top' ); // ✅ New Rule

    // Edit Pages (using post ID)
    add_rewrite_rule( '^edit-skater/(\d+)/?$', 'index.php?edit_skater=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-competition/(\d+)/?$', 'index.php?edit_competition=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-competition-result/(\d+)/?$', 'index.php?edit_competition_result=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-gap-analysis/(\d+)/?$', 'index.php?edit_gap_analysis=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-goal/(\d+)/?$', 'index.php?edit_goal=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-injury-log/(\d+)/?$', 'index.php?edit_injury_log=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-meeting-log/(\d+)/?$', 'index.php?edit_meeting_log=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-program/(\d+)/?$', 'index.php?edit_program=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-session-log/(\d+)/?$', 'index.php?edit_session_log=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-weekly-plan/(\d+)/?$', 'index.php?edit_weekly_plan=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-yearly-plan/(\d+)/?$', 'index.php?edit_yearly_plan=$matches[1]', 'top' );
    add_rewrite_rule( '^edit-ctes/(\d+)/?$', 'index.php?edit_ctes=$matches[1]', 'top' ); // ✅ New Rule
}
add_action( 'init', 'spd_add_rewrite_rules' );

/**
 * Adds custom query variables to WordPress.
 */
function spd_add_query_vars( $vars ) {
    // Dashboard and View Pages
    $vars[] = 'coach_dashboard';
    $vars[] = 'skater_dashboard';
    $vars[] = 'view_all_competitions';

    // Create Pages
    $vars[] = 'create_skater';
    $vars[] = 'create_competition';
    $vars[] = 'create_competition_result';
    $vars[] = 'create_gap_analysis';
    $vars[] = 'create_goal';
    $vars[] = 'create_injury_log';
    $vars[] = 'create_meeting_log';
    $vars[] = 'create_program';
    $vars[] = 'create_session_log';
    $vars[] = 'create_weekly_plan';
    $vars[] = 'create_yearly_plan';
    $vars[] = 'create_ctes'; // ✅ New Var

    // Edit Pages
    $vars[] = 'edit_skater';
    $vars[] = 'edit_competition';
    $vars[] = 'edit_competition_result';
    $vars[] = 'edit_gap_analysis';
    $vars[] = 'edit_goal';
    $vars[] = 'edit_injury_log';
    $vars[] = 'edit_meeting_log';
    $vars[] = 'edit_program';
    $vars[] = 'edit_session_log';
    $vars[] = 'edit_weekly_plan';
    $vars[] = 'edit_yearly_plan';
    $vars[] = 'edit_ctes'; // ✅ New Var

    return $vars;
}
add_filter( 'query_vars', 'spd_add_query_vars' );

/**
 * Loads the correct template file based on the query variable.
 */
function spd_template_include( $template ) {
    $plugin_path = plugin_dir_path( __DIR__ ); // Path to the plugin's root directory.

    // Dashboard and View Pages
    if ( get_query_var( 'coach_dashboard' ) ) {
        return $plugin_path . 'templates/coach-dashboard-view.php';
    }
    if ( get_query_var( 'skater_dashboard' ) ) {
        return $plugin_path . 'templates/skater-dashboard-view.php';
    }
    if ( get_query_var( 'view_all_competitions' ) ) {
        return $plugin_path . 'templates/views/competitions-all.php';
    }

    // Create/Edit Pages (they often share the same template)
    if ( get_query_var( 'create_skater' ) || get_query_var( 'edit_skater' ) ) {
        return $plugin_path . 'templates/create/create-skater.php';
    }
    if ( get_query_var( 'create_competition' ) || get_query_var( 'edit_competition' ) ) {
        return $plugin_path . 'templates/create/create-competition.php';
    }
    if ( get_query_var( 'create_competition_result' ) || get_query_var( 'edit_competition_result' ) ) {
        return $plugin_path . 'templates/create/create-competition_result.php';
    }
    if ( get_query_var( 'create_gap_analysis' ) || get_query_var( 'edit_gap_analysis' ) ) {
        return $plugin_path . 'templates/create/create-gap_analysis.php';
    }
    if ( get_query_var( 'create_goal' ) || get_query_var( 'edit_goal' ) ) {
        return $plugin_path . 'templates/create/create-goal.php';
    }
    if ( get_query_var( 'create_injury_log' ) || get_query_var( 'edit_injury_log' ) ) {
        return $plugin_path . 'templates/create/create-injury_log.php';
    }
    if ( get_query_var( 'create_meeting_log' ) || get_query_var( 'edit_meeting_log' ) ) {
        return $plugin_path . 'templates/create/create-meeting_log.php';
    }
    if ( get_query_var( 'create_program' ) || get_query_var( 'edit_program' ) ) {
        return $plugin_path . 'templates/create/create-program.php';
    }
    if ( get_query_var( 'create_session_log' ) || get_query_var( 'edit_session_log' ) ) {
        return $plugin_path . 'templates/create/create-session_log.php';
    }
    if ( get_query_var( 'create_weekly_plan' ) || get_query_var( 'edit_weekly_plan' ) ) {
        return $plugin_path . 'templates/create/create-weekly_plan.php';
    }
    if ( get_query_var( 'create_yearly_plan' ) || get_query_var( 'edit_yearly_plan' ) ) {
        return $plugin_path . 'templates/create/create-yearly_plan.php';
    }
    // ✅ New Template Include Logic
    if ( get_query_var( 'create_ctes' ) || get_query_var( 'edit_ctes' ) ) {
        return $plugin_path . 'templates/create/create-ctes.php';
    }

    return $template;
}
add_filter( 'template_include', 'spd_template_include' );
