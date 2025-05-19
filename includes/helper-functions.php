<?php
/**
 * General-purpose utility functions for the Coach Dashboard plugin.
 */

/**
 * Format a date string or timestamp as YYYY-MM-DD.
 */
function coach_format_date($date_string) {
    $dt = DateTime::createFromFormat('Y-m-d', $date_string);
    return $dt ? $dt->format('M j, Y') : $date_string;
}


/**
 * Return a readable label for a post type (e.g., 'weekly_plan' → 'Weekly Plan').
 */
function coach_get_post_type_label($post_type) {
    $post_type_object = get_post_type_object($post_type);
    return $post_type_object ? $post_type_object->labels->singular_name : ucfirst(str_replace('_', ' ', $post_type));
}

/**
 * Get the title of a related post by ID, or fallback to [Untitled].
 */
function coach_get_post_title($post_id) {
    $title = get_the_title($post_id);
    return $title ?: '[Untitled]';
}

/**
 * Simple debug output (only if user is admin).
 */
function coach_debug($data) {
    if (current_user_can('administrator')) {
        echo '<pre style="background: #f6f6f6; padding: 1em; border: 1px solid #ddd;">';
        print_r($data);
        echo '</pre>';
    }
}

add_action('acf/save_post', 'spd_autotitle_competition_result', 20);
function spd_autotitle_competition_result($post_id) {
    if (get_post_type($post_id) !== 'competition_result') {
        return;
    }

    // Only set if no title provided
    $existing_title = get_post_field('post_title', $post_id);
    if ($existing_title && $existing_title !== 'Auto Draft') {
        return;
    }

    $skater = get_field('linked_skater', $post_id);
    $comp   = get_field('linked_competition', $post_id);

    $skater_name = is_array($skater) ? get_the_title($skater[0]) : ($skater ? get_the_title($skater) : '');
    $comp_name   = is_array($comp)   ? get_the_title($comp[0])   : ($comp   ? get_the_title($comp)   : '');


    if ($skater_name && $comp_name) {
        $title = $comp_name . ' – ' . $skater_name;

        // Update post title and slug
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $title,
            'post_name'  => sanitize_title($title)
        ]);
    }

    add_action('acf/save_post', function ($post_id) {
        if (get_post_type($post_id) !== 'meeting_log') return;

        $title = get_field('meeting_title', $post_id);
        if ($title) {
            wp_update_post([
                'ID' => $post_id,
                'post_title' => sanitize_text_field($title),
            ]);
        }
    });

    add_action('acf/save_post', 'spd_set_yearly_plan_title', 20);
function spd_set_yearly_plan_title($post_id) {
    // Only for yearly_plan post type
    if (get_post_type($post_id) !== 'yearly_plan') {
        return;
    }

    // Avoid running on autosave or invalid ID
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!$post_id || is_numeric($post_id) === false) return;

    // Get skater and season fields
    $season = get_field('season', $post_id);
    $skaters = get_field('skater', $post_id);
    $skater_name = '';

    if (is_array($skaters) && !empty($skaters)) {
        $skater = $skaters[0]; // assuming 1 skater per plan
        $skater_name = get_the_title($skater);
    }

    if ($season && $skater_name) {
        $new_title = $season . ' – ' . $skater_name;

        // Update post title and slug
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $new_title,
            'post_name'  => sanitize_title($new_title),
        ]);
    }
}

}
