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
