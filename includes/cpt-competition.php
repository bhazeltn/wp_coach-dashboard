<?php
/**
 * Register the 'competition' custom post type and override its single view.
 */

function coach_dashboard_register_competition_cpt() {
    $labels = array(
        'name'               => 'Competitions',
        'singular_name'      => 'Competition',
        'add_new'            => 'Add New Competition',
        'add_new_item'       => 'Add Competition',
        'edit_item'          => 'Edit Competition',
        'new_item'           => 'New Competition',
        'all_items'          => 'All Competitions',
        'view_item'          => 'View Competition',
        'search_items'       => 'Search Competitions',
        'not_found'          => 'No competitions found',
        'not_found_in_trash' => 'No competitions in Trash',
        'menu_name'          => 'Competitions'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'competition'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-awards',
        'show_in_rest'       => true,
    );

    register_post_type('competition', $args);
}
add_action('init', 'coach_dashboard_register_competition_cpt');

function coach_dashboard_competition_template($template) {
    if (is_singular('competition')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-competition.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_competition_template');
