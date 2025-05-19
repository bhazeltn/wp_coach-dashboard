<?php
/**
 * Custom rewrite rules, query vars, and template overrides for special pages.
 */

// === Rewrite Rules ===

function spd_add_custom_rewrite_rules() {
    // Skater profile: /skater/{slug}
    add_rewrite_rule('^skater/([^/]+)/?$', 'index.php?skater_view=$matches[1]', 'top');

    // Coach Dashboard: /coach-dashboard
    add_rewrite_rule('^coach-dashboard/?$', 'index.php?coach_dashboard=1', 'top');

    // Create forms
    add_rewrite_rule('^create-goal/?$', 'index.php?create_goal=1', 'top');
    add_rewrite_rule('^create-injury-log/?$', 'index.php?create_injury_log=1', 'top');
    add_rewrite_rule('^create-competition/?$', 'index.php?create_competition=1', 'top');
    add_rewrite_rule('^create-competition-result/?$', 'index.php?create_competition_result=1', 'top');
    add_rewrite_rule('^create-meeting-log/?$', 'index.php?create_meeting_log=1', 'top');
    add_rewrite_rule('^create-session-log/?$', 'index.php?create_session_log=1', 'top');
    add_rewrite_rule('^create-weekly-plan/?$', 'index.php?create_weekly_plan=1', 'top');
    add_rewrite_rule('^create-yearly-plan/?$', 'index.php?create_yearly_plan=1', 'top');
    add_rewrite_rule('^create-program/?$', 'index.php?create_program=1', 'top');
    add_rewrite_rule('^edit-program/([0-9]+)/?$', 'index.php?edit_program=$matches[1]', 'top');

    // Edit forms
    add_rewrite_rule('^edit-goal/?$', 'index.php?edit_goal=1', 'top');
    add_rewrite_rule('^edit-injury-log/([0-9]+)/?$', 'index.php?edit_injury_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-competition/([0-9]+)/?$', 'index.php?edit_competition=$matches[1]', 'top');
    add_rewrite_rule('^edit-competition-result/([0-9]+)/?$', 'index.php?edit_competition_result=1&result_id=$matches[1]', 'top');
    add_rewrite_rule('^edit-meeting-log/([0-9]+)/?$', 'index.php?edit_meeting_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-session-log/([0-9]+)/?$', 'index.php?edit_session_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-weekly-plan/([0-9]+)/?$', 'index.php?edit_weekly_plan=$matches[1]', 'top');
    add_rewrite_rule('^edit-yearly-plan/([0-9]+)/?$', 'index.php?edit_yearly_plan=$matches[1]', 'top');


}
add_action('init', 'spd_add_custom_rewrite_rules');

// === Register Custom Query Vars ===

function spd_add_query_vars($vars) {
    $vars[] = 'skater_view';
    $vars[] = 'coach_dashboard';
    $vars[] = 'create_program';
    $vars[] = 'create_meeting_log';
    $vars[] = 'create_injury_log';
    $vars[] = 'edit_injury_log';
    $vars[] = 'create_goal';
    $vars[] = 'edit_goal';
    $vars[] = 'goal_id';
    $vars[] = 'injury_id';
    $vars[] = 'create_competition';
    $vars[] = 'edit_competition';
    $vars[] = 'create_competition_result';
    $vars[] = 'edit_competition_result';
    $vars[] = 'result_id';
    $vars[] = 'edit_meeting_log';
    $vars[] = 'meeting_log_id';
    $vars[] = 'create_session_log';
    $vars[] = 'edit_session_log';
    $vars[] = 'session_log_id';
    $vars[] = 'create_weekly_plan';
    $vars[] = 'edit_weekly_plan';
    $vars[] = 'weekly_plan_id';
    $vars[] = 'create_yearly_plan';
    $vars[] = 'edit_yearly_plan';
    $vars[] = 'yearly_plan_id';
    $vars[] = 'create_program';
    $vars[] = 'edit_program';
    $vars[] = 'program_view';
    $vars[] = 'program_id';

    return $vars;
}
add_filter('query_vars', 'spd_add_query_vars');

// === Template Redirects ===

function spd_template_redirects() {
    if (get_query_var('skater_view')) {
        include plugin_dir_path(__DIR__) . '/templates/coach-skater-view.php';
        exit;
    }

    if (get_query_var('coach_dashboard')) {
        include plugin_dir_path(__DIR__) . '/templates/coach-dashboard-view.php';
        exit;
    }

    if (get_query_var('create_goal')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-goal.php';
        exit;
    }

    if (get_query_var('edit_goal')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-goal.php';
        exit;
    }

    if (get_query_var('create_program')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-program.php';
        exit;
    }

    if (get_query_var('create_meeting_log')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-meeting_log.php';
        exit;
    }
    
    if (get_query_var('edit_meeting_log')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-meeting_log.php';
        exit;
    }

    if (get_query_var('create_injury_log')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-injury_log.php';
        exit;
    }

    if (get_query_var('edit_injury_log')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-injury_log.php';
        exit;
    }
    if (get_query_var('create_competition')) {
    include plugin_dir_path(__FILE__) . '/../templates/create/create-competition.php';
    exit;
    }

    if (get_query_var('create_competition_result')) {
    include plugin_dir_path(__FILE__) . '/../templates/create/create-competition_result.php';
    exit;
    }
    
    if (get_query_var('edit_competition_result')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-competition_result.php';
        exit;
    }

    if (get_query_var('create_session_log')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-session_log.php';
        exit;
    }
    
    if (get_query_var('edit_session_log')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-session_log.php';
        exit;
    }
    
    if (get_query_var('create_weekly_plan')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-weekly_plan.php';
        exit;
    }
    
    if (get_query_var('edit_weekly_plan')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-weekly_plan.php';
        exit;
    }

    if (get_query_var('create_yearly_plan')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-yearly_plan.php';
        exit;
    }
    
    if (get_query_var('edit_yearly_plan')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-yearly_plan.php';
        exit;
    }

    if (get_query_var('create_program')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-program.php';
        exit;
    }
    
    if ($program_id = get_query_var('edit_program')) {
        global $post;
        $post = get_post($program_id);
        setup_postdata($post);
    
        include plugin_dir_path(__FILE__) . '/../templates/create/create-program.php';
        exit;
    }
    
    
   if ($comp_id = get_query_var('edit_competition')) {
    global $post;
    $post = get_post($comp_id);
    setup_postdata($post);

    include plugin_dir_path(__FILE__) . '/../templates/create/create-competition.php';
    exit;
}

}
add_action('template_redirect', 'spd_template_redirects');

// === Optional: Enforce trailing slash on skater URLs ===

add_action('template_redirect', function () {
    $slug = get_query_var('skater_view');
    if ($slug && substr($_SERVER['REQUEST_URI'], -1) !== '/') {
        wp_safe_redirect(home_url('/skater/' . $slug . '/'), 301);
        exit;
    }
});