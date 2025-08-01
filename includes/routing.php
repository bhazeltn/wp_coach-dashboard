<?php
/**
 * Custom rewrite rules, query vars, and template overrides for special pages.
 */

add_action('template_redirect', function () {
    // Get the current URL path without any query strings
    $current_path = strtok($_SERVER["REQUEST_URI"], '?');

    // Check if the current path is one of our plugin's internal pages
    $is_app_page = preg_match('#^/(coach-dashboard|skater/|edit-|create-|view-all-)#', $current_path);

    // If the user is on one of our app's pages, do nothing here.
    if ($is_app_page) {
        return;
    }

    // --- The logic below only runs on pages that are NOT part of our app (like the homepage) ---

    // For non-logged-in users on the front page, show the landing page template.
    if (!is_user_logged_in() && is_front_page()) {
        include plugin_dir_path(__DIR__) . '/templates/front-page.php';
        exit;
    }

    // For logged-in users on the front page, redirect them to their correct dashboard.
    if (is_user_logged_in() && is_front_page()) {
        $user = wp_get_current_user();
        $roles = (array) $user->roles;

        if (in_array('administrator', $roles) || in_array('coach', $roles)) {
            wp_redirect(home_url('/coach-dashboard/'));
            exit;
        }

        if (in_array('skater', $roles)) {
            $skater = get_posts([
                'post_type'   => 'skater',
                'numberposts' => 1,
                'meta_key'    => 'skater_account',
                'meta_value'  => $user->ID,
            ]);
            if ($skater) {
                wp_redirect(site_url('/skater/' . $skater[0]->post_name));
                exit;
            }
        }
        // Fallback for any other logged-in role if needed
        wp_redirect(home_url('/coach-dashboard/'));
        exit;
    }

    // Finally, if a non-logged-in user tries to access any other random page, protect it.
    if (!is_user_logged_in()) {
        auth_redirect();
    }
});


add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (!($user instanceof WP_User)) return $redirect_to;

    $roles = (array) $user->roles;

    if (in_array('administrator', $roles) || in_array('coach', $roles)) {
        return site_url('/coach-dashboard/');
    }

    if (in_array('skater', $roles)) {
        $skater = get_posts([
            'post_type'      => 'skater',
            'numberposts'    => 1,
            'meta_key'       => 'skater_account',
            'meta_value'     => $user->ID,
            'meta_compare'   => '=',
            'post_status'    => 'publish',
        ]);

        if ($skater) {
            $slug = get_post_field('post_name', $skater[0]->ID);
            $redirect_url = site_url('/skater/' . $slug);
            return $redirect_url;
        }

        return home_url('/');
    }

    return $redirect_to;
}, 10, 3);


// === Rewrite Rules ===

function spd_add_custom_rewrite_rules() {
    // Skater profile: /skater/{slug}
    add_rewrite_rule('^skater/([^/]+)/?$', 'index.php?skater_view=$matches[1]', 'top');

    // Coach Dashboard: /coach-dashboard
    add_rewrite_rule('^coach-dashboard/?$', 'index.php?coach_dashboard=1', 'top');

    // View All Views
    add_rewrite_rule('^view-all-competitions/?$', 'index.php?view_all_competitions=1', 'top');

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
    add_rewrite_rule('^create-skater/?$', 'index.php?create_skater=1', 'top');
    add_rewrite_rule('^create-gap-analysis/?$', 'index.php?create_gap_analysis=1', 'top');
    add_rewrite_rule('^create-ctes/?$', 'index.php?create_ctes=1', 'top'); // ✅ New Rule

    // Edit forms
    add_rewrite_rule('^edit-goal/([0-9]+)/?$', 'index.php?edit_goal=$matches[1]', 'top');
    add_rewrite_rule('^edit-injury-log/([0-9]+)/?$', 'index.php?edit_injury_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-competition/([0-9]+)/?$', 'index.php?edit_competition=$matches[1]', 'top');
    add_rewrite_rule('^edit-competition-result/([0-9]+)/?$', 'index.php?edit_competition_result=$matches[1]', 'top');
    add_rewrite_rule('^edit-meeting-log/([0-9]+)/?$', 'index.php?edit_meeting_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-session-log/([0-9]+)/?$', 'index.php?edit_session_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-weekly-plan/([0-9]+)/?$', 'index.php?edit_weekly_plan=$matches[1]', 'top');
    add_rewrite_rule('^edit-yearly-plan/([0-9]+)/?$', 'index.php?edit_yearly_plan=$matches[1]', 'top');
    add_rewrite_rule('^edit-program/([0-9]+)/?$', 'index.php?edit_program=$matches[1]', 'top');
    add_rewrite_rule('^edit-skater/([0-9]+)/?$', 'index.php?edit_skater=$matches[1]', 'top');
    add_rewrite_rule('^edit-gap-analysis/([0-9]+)/?$', 'index.php?edit_gap_analysis=$matches[1]', 'top');
    add_rewrite_rule('^edit-ctes/([0-9]+)/?$', 'index.php?edit_ctes=$matches[1]', 'top'); // ✅ New Rule
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
    $vars[] = 'create_competition';
    $vars[] = 'edit_competition';
    $vars[] = 'create_competition_result';
    $vars[] = 'edit_competition_result';
    $vars[] = 'edit_meeting_log';
    $vars[] = 'create_session_log';
    $vars[] = 'edit_session_log';
    $vars[] = 'create_weekly_plan';
    $vars[] = 'edit_weekly_plan';
    $vars[] = 'create_yearly_plan';
    $vars[] = 'edit_yearly_plan';
    $vars[] = 'create_program';
    $vars[] = 'edit_program';
    $vars[] = 'create_skater';
    $vars[] = 'edit_skater';
    $vars[] = 'create_gap_analysis';
    $vars[] = 'edit_gap_analysis';
    $vars[] = 'view_all_competitions';
    $vars[] = 'create_ctes'; // ✅ New Var
    $vars[] = 'edit_ctes';   // ✅ New Var

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

    if ($goal_id = get_query_var('edit_goal')) {
        global $post;
        $post = get_post($goal_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-goal.php';
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
    
    if ($meeting_log_id = get_query_var('edit_meeting_log')) {
        global $post;
        $post = get_post($meeting_log_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-meeting_log.php';
        exit;
    }

    if (get_query_var('create_injury_log')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-injury_log.php';
        exit;
    }

    if ($injury_log_id = get_query_var('edit_injury_log')) {
        global $post;
        $post = get_post($injury_log_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-injury_log.php';
        exit;
    }
    if (get_query_var('create_competition')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-competition.php';
        exit;
    }

    if (get_query_var('create_competition_result')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-competition_result.php';
        exit;
    }
    
    if ($result_id = get_query_var('edit_competition_result')) {
        global $post;
        $post = get_post($result_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-competition_result.php';
        exit;
    }

    if (get_query_var('create_session_log')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-session_log.php';
        exit;
    }
    
    if ($session_log_id = get_query_var('edit_session_log')) {
        global $post;
        $post = get_post($session_log_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-session_log.php';
        exit;
    }
    
    if (get_query_var('create_weekly_plan')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-weekly_plan.php';
        exit;
    }
    
    if ($weekly_plan_id = get_query_var('edit_weekly_plan')) {
        global $post;
        $post = get_post($weekly_plan_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-weekly_plan.php';
        exit;
    }

    if (get_query_var('create_yearly_plan')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-yearly_plan.php';
        exit;
    }
    
    if ($yearly_plan_id = get_query_var('edit_yearly_plan')) {
        global $post;
        $post = get_post($yearly_plan_id);
        setup_postdata($post);
        include plugin_dir_path(__DIR__) . '/templates/create/create-yearly_plan.php';
        exit;
    }

    if (get_query_var('create_program')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-program.php';
        exit;
    }

    if (get_query_var('create_skater')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-skater.php';
        exit;
    }
    
    if ($skater_id = get_query_var('edit_skater')) {
        global $post;
        $post = get_post($skater_id);
        setup_postdata($post);
    
        include plugin_dir_path(__DIR__) . '/templates/create/create-skater.php';
        exit;
    }
    
    
    if ($program_id = get_query_var('edit_program')) {
        global $post;
        $post = get_post($program_id);
        setup_postdata($post);
    
        include plugin_dir_path(__DIR__) . '/templates/create/create-program.php';
        exit;
    }
    
    
    if ($comp_id = get_query_var('edit_competition')) {
        global $post;
        $post = get_post($comp_id);
        setup_postdata($post);

        include plugin_dir_path(__DIR__) . '/templates/create/create-competition.php';
        exit;
    }

    if (get_query_var('create_gap_analysis')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-gap_analysis.php';
        exit;
    }

    if ($gap_id = get_query_var('edit_gap_analysis')) {
        global $post;
        $post = get_post($gap_id);
        setup_postdata($post);

        include plugin_dir_path(__DIR__) . '/templates/create/create-gap_analysis.php';
        exit;
    }
    
    // ✅ New Template Include Logic
    if (get_query_var('create_ctes')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-ctes.php';
        exit;
    }
    
    // ✅ New Template Include Logic
    if ($ctes_id = get_query_var('edit_ctes')) {
        global $post;
        $post = get_post($ctes_id);
        setup_postdata($post);
    
        include plugin_dir_path(__DIR__) . '/templates/create/create-ctes.php';
        exit;
    }

    if (get_query_var('view_all_competitions')) {
        include plugin_dir_path(__DIR__) . '/templates/views/competitions-all.php';
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
