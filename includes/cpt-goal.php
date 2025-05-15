<?php
/**
 * Register the 'goal' custom post type and override its single view.
 */

function coach_dashboard_register_goal_cpt() {
    $labels = array(
        'name'               => 'Goals',
        'singular_name'      => 'Goal',
        'add_new'            => 'Add New Goal',
        'add_new_item'       => 'Add New Goal',
        'edit_item'          => 'Edit Goal',
        'new_item'           => 'New Goal',
        'all_items'          => 'All Goals',
        'view_item'          => 'View Goal',
        'search_items'       => 'Search Goals',
        'not_found'          => 'No goals found',
        'not_found_in_trash' => 'No goals found in Trash',
        'menu_name'          => 'Goals'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'goal'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-flag',
        'show_in_rest'       => true,
    );

    register_post_type('goal', $args);
}
add_action('init', 'coach_dashboard_register_goal_cpt');

function coach_dashboard_goal_template($template) {
    if (is_singular('goal')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-goal.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_goal_template');
