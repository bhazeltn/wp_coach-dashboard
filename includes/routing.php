<?php
/**
 * Custom rewrite rules, query vars, and template overrides for special pages.
 */

 add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (!($user instanceof WP_User)) return $redirect_to;

    error_log('🔁 login_redirect fired for: ' . $user->user_login);
    error_log('🔁 Roles: ' . implode(', ', (array) $user->roles));

    $roles = (array) $user->roles;

    if (in_array('administrator', $roles) || in_array('coach', $roles)) {
        error_log('✅ Redirecting to coach-dashboard');
        return site_url('/coach-dashboard/');
    }

    if (in_array('skater', $roles)) {
        error_log('🛼 Looking for skater CPT linked to user ID ' . $user->ID);
        $skater = get_posts([
            'post_type'     => 'skater',
            'numberposts'   => 1,
            'meta_key'      => 'skater_account',
            'meta_value'    => $user->ID,
            'meta_compare'  => '=',
            'post_status'   => 'publish',
        ]);

        if ($skater) {
            $slug = get_post_field('post_name', $skater[0]->ID);
            $redirect_url = site_url('/skater/' . $slug);
            error_log('✅ Skater found: ' . $slug . ' → redirecting to: ' . $redirect_url);
            return $redirect_url;
        } else {
            error_log('❌ No skater post linked to this user.');
        }

        return home_url('/');
    }

    return $redirect_to;
}, 10, 3);

 add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (!($user instanceof WP_User)) return $redirect_to;

    $roles = (array) $user->roles;

    if (in_array('administrator', $roles) || in_array('coach', $roles)) {
        return site_url('/coach-dashboard/');
    }

    if (in_array('skater', $roles)) {
        $skater = get_posts([
            'post_type'     => 'skater',
            'numberposts'   => 1,
            'meta_key'      => 'skater_account',
            'meta_value'    => $user->ID,
            'meta_compare'  => '=',
            'post_status'   => 'publish',
        ]);

        if ($skater) {
            $slug = get_post_field('post_name', $skater[0]->ID);
            return site_url('/skater/' . $slug);
        }

        return home_url('/');
    }

    return $redirect_to;
}, 10, 3);

add_action('template_redirect', function () {
    if ((is_front_page() || is_home()) && !is_admin()) {
        if (!is_user_logged_in()) {
            // Not logged in → send to login
            wp_redirect(wp_login_url(home_url()));
            exit;
        }

        $user = wp_get_current_user();
        $roles = (array) $user->roles;

        $current_url = $_SERVER['REQUEST_URI'];

        // Allow coaches/admins to view skater pages too
        if ((in_array('coach', $roles) || in_array('administrator', $roles)) &&
            !preg_match('#^/coach-dashboard|/skater/#', $current_url)) {
            wp_redirect(home_url('/coach-dashboard/'));
            exit;
        }

        if (in_array('skater', $roles) && strpos($current_url, '/skater/') === false) {
            $skater = get_posts([
                'post_type'     => 'skater',
                'numberposts'   => 1,
                'meta_key'      => 'skater_account',
                'meta_value'    => $user->ID,
                'meta_compare'  => '=',
                'post_status'   => 'publish',
            ]);

            if ($skater) {
                $slug = get_post_field('post_name', $skater[0]->ID);
                wp_redirect(site_url('/skater/' . $slug));
                exit;
            }
        }
    }
});




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
    add_rewrite_rule('^edit-program/([0-9]+)/?$', 'index.php?edit_program=$matches[1]', 'top');
    add_rewrite_rule('^create-skater/?$', 'index.php?create_skater=1', 'top');
    add_rewrite_rule('^create-gap-analysis/?$', 'index.php?create_gap_analysis=1', 'top');

    // Edit forms
    add_rewrite_rule('^edit-goal/?$', 'index.php?edit_goal=1', 'top');
    add_rewrite_rule('^edit-injury-log/([0-9]+)/?$', 'index.php?edit_injury_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-competition/([0-9]+)/?$', 'index.php?edit_competition=$matches[1]', 'top');
    add_rewrite_rule('^edit-competition-result/([0-9]+)/?$', 'index.php?edit_competition_result=1&result_id=$matches[1]', 'top');
    add_rewrite_rule('^edit-meeting-log/([0-9]+)/?$', 'index.php?edit_meeting_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-session-log/([0-9]+)/?$', 'index.php?edit_session_log=$matches[1]', 'top');
    add_rewrite_rule('^edit-weekly-plan/([0-9]+)/?$', 'index.php?edit_weekly_plan=$matches[1]', 'top');
    add_rewrite_rule('^edit-yearly-plan/([0-9]+)/?$', 'index.php?edit_yearly_plan=$matches[1]', 'top');
    add_rewrite_rule('^edit-skater/([0-9]+)/?$', 'index.php?edit_skater=$matches[1]', 'top');
    add_rewrite_rule('^edit-gap-analysis/([0-9]+)/?$', 'index.php?edit_gap_analysis=$matches[1]', 'top');
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
    $vars[] = 'create_skater';
    $vars[] = 'edit_skater';
    $vars[] = 'create_gap_analysis';
    $vars[] = 'edit_gap_analysis';
    $vars[] = 'gap_analysis_id';
    $vars[] = 'view_all_competitions';


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

    if (get_query_var('create_skater')) {
        include plugin_dir_path(__DIR__) . '/templates/create/create-skater.php';
        exit;
    }
    
    if ($skater_id = get_query_var('edit_skater')) {
        global $post;
        $post = get_post($skater_id);
        setup_postdata($post);
    
        include plugin_dir_path(__FILE__) . '/../templates/create/create-skater.php';
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

if (get_query_var('create_gap_analysis')) {
    include plugin_dir_path(__FILE__) . '/../templates/create/create-gap_analysis.php';
    exit;
}

if ($gap_id = get_query_var('edit_gap_analysis')) {
    global $post;
    $post = get_post($gap_id);
    setup_postdata($post);

    include plugin_dir_path(__FILE__) . '/../templates/create/create-gap_analysis.php';
    exit;
}

if (get_query_var('view_all_competitions')) {
    include plugin_dir_path(__FILE__) . '/../templates/views/competitions-all.php';
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