<?php
/**
 * Register the 'gap_analysis' custom post type and override its single view.
 */

function coach_dashboard_register_gap_analysis_cpt() {
    $labels = array(
        'name'               => 'Gap Analyses',
        'singular_name'      => 'Gap Analysis',
        'add_new'            => 'Add New Gap Analysis',
        'add_new_item'       => 'Add New Gap Analysis',
        'edit_item'          => 'Edit Gap Analysis',
        'new_item'           => 'New Gap Analysis',
        'all_items'          => 'All Gap Analyses',
        'view_item'          => 'View Gap Analysis',
        'search_items'       => 'Search Gap Analyses',
        'not_found'          => 'No gap analyses found',
        'not_found_in_trash' => 'No gap analyses found in Trash',
        'menu_name'          => 'Gap Analysis'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'gap-analysis'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-chart-bar',
        'show_in_rest'       => true,
    );

    register_post_type('gap_analysis', $args);
}
add_action('init', 'coach_dashboard_register_gap_analysis_cpt');

function coach_dashboard_gap_analysis_template($template) {
    if (is_singular('gap_analysis')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-gap_analysis.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_gap_analysis_template');
