<?php
/**
 * Register the 'meeting_log' custom post type and override its single view.
 */

function coach_dashboard_register_meeting_log_cpt() {
    $labels = array(
        'name'               => 'Meeting Logs',
        'singular_name'      => 'Meeting Log',
        'add_new'            => 'Add New Meeting Log',
        'add_new_item'       => 'Add Meeting Log',
        'edit_item'          => 'Edit Meeting Log',
        'new_item'           => 'New Meeting Log',
        'all_items'          => 'All Meeting Logs',
        'view_item'          => 'View Meeting Log',
        'search_items'       => 'Search Meeting Logs',
        'not_found'          => 'No meeting logs found',
        'not_found_in_trash' => 'No meeting logs in Trash',
        'menu_name'          => 'Meeting Logs'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'meeting-log'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-clipboard',
        'show_in_rest'       => true,
    );

    register_post_type('meeting_log', $args);
}
add_action('init', 'coach_dashboard_register_meeting_log_cpt');

function coach_dashboard_meeting_log_template($template) {
    if (is_singular('meeting_log')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-meeting_log.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_meeting_log_template');
