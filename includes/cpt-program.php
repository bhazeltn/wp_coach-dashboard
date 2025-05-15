<?php
/**
 * Register the 'program' custom post type and override its single view.
 */

// === Register CPT ===
function coach_dashboard_register_program_cpt() {
    $labels = array(
        'name'               => 'Programs',
        'singular_name'      => 'Program',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Program',
        'edit_item'          => 'Edit Program',
        'new_item'           => 'New Program',
        'all_items'          => 'All Programs',
        'view_item'          => 'View Program',
        'search_items'       => 'Search Programs',
        'not_found'          => 'No programs found',
        'not_found_in_trash' => 'No programs found in Trash',
        'menu_name'          => 'Programs'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'program'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-format-video',
        'show_in_rest'       => true,
    );

    register_post_type('program', $args);
}
add_action('init', 'coach_dashboard_register_program_cpt');

// === Override Single Template ===
function coach_dashboard_program_template($template) {
    if (is_singular('program')) {
        $custom = plugin_dir_path(__FILE__) . '/../templates/single/single-program.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}
add_filter('single_template', 'coach_dashboard_program_template');
