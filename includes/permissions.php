<?php
/**
 * Get skaters visible to the current user, considering roles and assignments.
 *
 * @param array $args Additional WP_Query args to merge in.
 * @return array Array of WP_Post skater objects.
 */
function spd_get_visible_skaters($args = []) {
    $current_user = wp_get_current_user();
    $user_roles   = $current_user->roles;

    $default_args = [
        'post_type'   => 'skater',
        'post_status' => 'publish',
        'numberposts' => -1,
    ];

    // Restrict to assigned coaches only if not admin or observer
    if (in_array('coach', $user_roles) && !in_array('administrator', $user_roles) && !in_array('observer', $user_roles)) {
        $default_args['meta_query'] = [[
            'key'     => 'assigned_coaches', // ACF relationship field
            'value'   => '"' . $current_user->ID . '"',
            'compare' => 'LIKE',
        ]];
    }

    return get_posts(array_merge($default_args, $args));
}

function spd_meta_query_for_visible_skaters($field_name, $skaters) {
    // Ensure this is an array of post IDs, not objects
    $skater_ids = array_map(function($s) {
        return is_object($s) ? $s->ID : $s;
    }, $skaters);

    return [[
        'key'     => $field_name,
        'value'   => $skater_ids,
        'compare' => 'IN',
    ]];
}