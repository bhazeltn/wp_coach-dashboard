<?php
/**
 * Register the 'CTES Requirements' Custom Post Type.
 *
 * This CPT will store global CTES (Combined Technical Elements Score) requirements
 * for various championships, updated per competitive season/year.
 */
function coachos_register_ctes_requirements_cpt() {
    $labels = array(
        'name'                  => _x( 'CTES Requirements', 'Post Type General Name', 'coachos' ),
        'singular_name'         => _x( 'CTES Requirement', 'Post Type Singular Name', 'coachos' ),
        'menu_name'             => __( 'CTES Requirements', 'coachos' ),
        'name_admin_bar'        => __( 'CTES Requirement', 'coachos' ),
        'archives'              => __( 'CTES Requirement Archives', 'coachos' ),
        'attributes'            => __( 'CTES Requirement Attributes', 'coachos' ),
        'parent_item_colon'     => __( 'Parent CTES Requirement:', 'coachos' ),
        'all_items'             => __( 'All CTES Requirements', 'coachos' ),
        'add_new_item'          => __( 'Add New CTES Requirement', 'coachos' ),
        'add_new'               => __( 'Add New', 'coachos' ),
        'new_item'              => __( 'New CTES Requirement', 'coachos' ),
        'edit_item'             => __( 'Edit CTES Requirement', 'coachos' ),
        'update_item'           => __( 'Update CTES Requirement', 'coachos' ),
        'view_item'             => __( 'View CTES Requirement', 'coachos' ),
        'view_items'            => __( 'View CTES Requirements', 'coachos' ),
        'search_items'          => __( 'Search CTES Requirement', 'coachos' ),
        'not_found'             => __( 'Not found', 'coachos' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'coachos' ),
        'featured_image'        => __( 'Featured Image', 'coachos' ),
        'set_featured_image'    => __( 'Set featured image', 'coachos' ),
        'remove_featured_image' => __( 'Remove featured image', 'coachos' ),
        'use_featured_image'    => __( 'Use as featured image', 'coachos' ),
        'insert_into_item'      => __( 'Insert into requirement', 'coachos' ),
        'uploaded_to_this_item' => __( 'Uploaded to this requirement', 'coachos' ),
        'items_list'            => __( 'CTES Requirements list', 'coachos' ),
        'items_list_navigation' => __( 'CTES Requirements list navigation', 'coachos' ),
        'filter_items_list'     => __( 'Filter CTES requirements list', 'coachos' ),
    );
    $args = array(
        'label'                 => __( 'CTES Requirement', 'coachos' ),
        'description'           => __( 'Custom Post Type for managing CTES qualification requirements per season/year.', 'coachos' ),
        'labels'                => $labels,
        'supports'              => array( 'title' ), // Only need a title for the season (e.g., "2025-2026 CTES Requirements")
        'hierarchical'          => false,
        'public'                => false, // This CPT is not publicly viewable on the frontend
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25, // Adjust as needed
        'menu_icon'             => 'dashicons-chart-bar', // Changed icon again for a more 'score' feel
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'rewrite'               => false, // No need for public rewrites
        'capability_type'       => 'post',
        'show_in_rest'          => false, // Disable REST API if not needed
    );
    register_post_type( 'ctes_requirement', $args ); // Updated CPT slug

}
add_action( 'init', 'coachos_register_ctes_requirements_cpt', 0 );

