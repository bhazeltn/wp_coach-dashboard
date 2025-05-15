<?php
/**
 * Register the 'weekly_plan' custom post type and override its single view.
 */

function coach_dashboard_register_weekly_plan_cpt() {
    $labels = array(
        'name'               => 'Weekly Plans',
        'singular_name'      => 'Weekly Plan',
        'add_new'            => 'Add New Weekly Plan',
        'add_new_item'       => 'Add Weekly Plan',
        'edit_item'          => 'Edit Weekly Plan',
        'new_item'           => 'New Weekly Plan',
        'all_items'          => 'All Weekly Plans',
        'view_item'          => 'View Weekly Plan',
        'search_items'       => 'Search Weekly Plans',
        'not_found'          => 'No weekly plans found',
        'not_found_in_trash' => 'No weekly plans in Trash',
        'menu_name'          => 'Weekly Plans'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'weekly-plan'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-calendar-alt',
        'show_in_rest'       => true,
    );

    register_post_type('weekly_plan', $args);
}
add_action('init', 'coach_dashboard_register_weekly_plan_cpt');

function coach_dashboard_weekly_plan_template($template) {
    if (is_singular('weekly_plan')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-weekly_plan.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_weekly_plan_template');
