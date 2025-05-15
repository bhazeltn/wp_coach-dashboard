<?php
/**
 * Register the 'competition_result' custom post type and override its single view.
 */

function coach_dashboard_register_competition_result_cpt() {
    $labels = array(
        'name'               => 'Competition Results',
        'singular_name'      => 'Competition Result',
        'add_new'            => 'Add New Result',
        'add_new_item'       => 'Add Competition Result',
        'edit_item'          => 'Edit Competition Result',
        'new_item'           => 'New Competition Result',
        'all_items'          => 'All Competition Results',
        'view_item'          => 'View Result',
        'search_items'       => 'Search Results',
        'not_found'          => 'No results found',
        'not_found_in_trash' => 'No results in Trash',
        'menu_name'          => 'Competition Results'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'competition-result'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-chart-bar',
        'show_in_rest'       => true,
    );

    register_post_type('competition_result', $args);
}
add_action('init', 'coach_dashboard_register_competition_result_cpt');

function coach_dashboard_competition_result_template($template) {
    if (is_singular('competition_result')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-competition_result.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_competition_result_template');
