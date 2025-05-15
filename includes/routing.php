<?php
/**
 * Custom rewrite rules, query vars, and template overrides for special pages.
 */

// === Rewrite Rules & Query Vars ===

function coach_dashboard_add_custom_rewrite_rules() {
    // Skater profile: /skater/{slug}
    add_rewrite_rule('^skater/([^/]+)/?$', 'index.php?skater_view=$matches[1]', 'top');

    // Create forms
    add_rewrite_rule('^create-goal/?$', 'index.php?create_goal=1', 'top');
    add_rewrite_rule('^create-program/?$', 'index.php?create_program=1', 'top');
    add_rewrite_rule('^create-meeting-log/?$', 'index.php?create_meeting_log=1', 'top');
    add_rewrite_rule('^create-injury-log/?$', 'index.php?create_injury_log=1', 'top');
}
add_action('init', 'coach_dashboard_add_custom_rewrite_rules');

function coach_dashboard_add_query_vars($vars) {
    $vars[] = 'skater_view';
    $vars[] = 'create_goal';
    $vars[] = 'create_program';
    $vars[] = 'create_meeting_log';
    $vars[] = 'create_injury_log';
    return $vars;
}
add_filter('query_vars', 'coach_dashboard_add_query_vars');

// === Template Redirects for Custom Routes ===

function coach_dashboard_template_redirects() {
    if (get_query_var('skater_view')) {
        include plugin_dir_path(__FILE__) . '/../templates/skater-view.php';
        exit;
    }

    if (get_query_var('create_goal')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-goal.php';
        exit;
    }

    if (get_query_var('create_program')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-program.php';
        exit;
    }

    if (get_query_var('create_meeting_log')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-meeting_log.php';
        exit;
    }

    if (get_query_var('create_injury_log')) {
        include plugin_dir_path(__FILE__) . '/../templates/create/create-injury_log.php';
        exit;
    }
}
add_action('template_redirect', 'coach_dashboard_template_redirects');

// === Optional: Enforce trailing slash on skater pages ===

add_action('template_redirect', function () {
    $slug = get_query_var('skater_view');
    if ($slug && substr($_SERVER['REQUEST_URI'], -1) !== '/') {
        wp_safe_redirect(home_url('/skater/' . $slug . '/'), 301);
        exit;
    }
});
