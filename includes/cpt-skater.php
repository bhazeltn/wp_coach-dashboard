<?php
/**
 * Register the 'skater' custom post type.
 */

function coach_dashboard_register_skater_cpt() {
    $labels = array(
        'name'               => 'Skaters',
        'singular_name'      => 'Skater',
        'add_new'            => 'Add New Skater',
        'add_new_item'       => 'Add New Skater',
        'edit_item'          => 'Edit Skater',
        'new_item'           => 'New Skater',
        'all_items'          => 'All Skaters',
        'view_item'          => 'View Skater',
        'search_items'       => 'Search Skaters',
        'not_found'          => 'No skaters found',
        'not_found_in_trash' => 'No skaters found in Trash',
        'menu_name'          => 'Skaters'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'skater'),
        'supports'           => array('title', 'custom-fields'),
        'menu_icon'          => 'dashicons-groups',
        'show_in_rest'       => true,
    );

    register_post_type('skater', $args);
}
add_action('init', 'coach_dashboard_register_skater_cpt');
