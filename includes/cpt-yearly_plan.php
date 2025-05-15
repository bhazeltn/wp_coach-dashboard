<?php
/**
 * Register the 'yearly_plan' custom post type and override its single view.
 */

function coach_dashboard_register_yearly_plan_cpt() {
    $labels = array(
        'name'               => 'Yearly Plans',
        'singular_name'      => 'Yearly Plan',
        'add_new'            => 'Add New Yearly Plan',
        'add_new_item'       => 'Add Yearly Plan',
        'edit_item'          => 'Edit Yearly Plan',
        'new_item'           => 'New Yearly Plan',
        'all_items'          => 'All Yearly Plans',
        'view_item'          => 'View Yearly Plan',
        'search_items'       => 'Search Yearly Plans',
        'not_found'          => 'No yearly plans found',
        'not_found_in_trash' => 'No yearly plans in Trash',
        'menu_name'          => 'Yearly Plans'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'yearly-plan'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-chart-line',
        'show_in_rest'       => true,
    );

    register_post_type('yearly_plan', $args);
}
add_action('init', 'coach_dashboard_register_yearly_plan_cpt');

function coach_dashboard_yearly_plan_template($template) {
    if (is_singular('yearly_plan')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-yearly_plan.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_yearly_plan_template');
