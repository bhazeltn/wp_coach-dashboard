<?php
/**
 * Register the 'session_log' custom post type and override its single view.
 */

function coach_dashboard_register_session_log_cpt() {
    $labels = array(
        'name'               => 'Session Logs',
        'singular_name'      => 'Session Log',
        'add_new'            => 'Add New Session Log',
        'add_new_item'       => 'Add Session Log',
        'edit_item'          => 'Edit Session Log',
        'new_item'           => 'New Session Log',
        'all_items'          => 'All Session Logs',
        'view_item'          => 'View Session Log',
        'search_items'       => 'Search Session Logs',
        'not_found'          => 'No session logs found',
        'not_found_in_trash' => 'No session logs in Trash',
        'menu_name'          => 'Session Logs'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'session-log'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-welcome-write-blog',
        'show_in_rest'       => true,
    );

    register_post_type('session_log', $args);
}
add_action('init', 'coach_dashboard_register_session_log_cpt');

function coach_dashboard_session_log_template($template) {
    if (is_singular('session_log')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-session_log.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_session_log_template');
