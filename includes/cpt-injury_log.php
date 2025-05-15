<?php
/**
 * Register the 'injury_log' custom post type and override its single view.
 */

function coach_dashboard_register_injury_log_cpt() {
    $labels = array(
        'name'               => 'Injury Logs',
        'singular_name'      => 'Injury Log',
        'add_new'            => 'Add New Injury Log',
        'add_new_item'       => 'Add Injury Log',
        'edit_item'          => 'Edit Injury Log',
        'new_item'           => 'New Injury Log',
        'all_items'          => 'All Injury Logs',
        'view_item'          => 'View Injury Log',
        'search_items'       => 'Search Injury Logs',
        'not_found'          => 'No injury logs found',
        'not_found_in_trash' => 'No injury logs in Trash',
        'menu_name'          => 'Injury Logs'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'injury-log'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-first-aid',
        'show_in_rest'       => true,
    );

    register_post_type('injury_log', $args);
}
add_action('init', 'coach_dashboard_register_injury_log_cpt');

function coach_dashboard_injury_log_template($template) {
    if (is_singular('injury_log')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-injury_log.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_injury_log_template');
