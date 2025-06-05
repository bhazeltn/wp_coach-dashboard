<?php
/**
 * General-purpose utility functions for the Skater Planning Dashboard plugin.
 */

/**
 * Format a date string or timestamp as M j, Y.
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
 * Simple debug output (only visible to admins).
 */
function coach_debug($data) {
    if (current_user_can('administrator')) {
        echo '<pre style="background: #f6f6f6; padding: 1em; border: 1px solid #ddd;">';
        print_r($data);
        echo '</pre>';
    }
}

/**
 * Set post title for Skater from full_name field.
 */
add_action('acf/save_post', 'spd_set_skater_title', 20);
function spd_set_skater_title($post_id) {
    if (get_post_type($post_id) !== 'skater') return;

    $full_name = get_field('full_name', $post_id);
    if ($full_name) {
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => sanitize_text_field($full_name),
            'post_name'  => sanitize_title($full_name),
        ]);
    }
}

/**
 * Set post title for Yearly Plan as "Season – Skater".
 */
add_action('acf/save_post', 'spd_set_yearly_plan_title', 20);
function spd_set_yearly_plan_title($post_id) {
    if (get_post_type($post_id) !== 'yearly_plan') return;

    $season  = get_field('season', $post_id);
    $skaters = get_field('skater', $post_id);

    if ($season && is_array($skaters) && !empty($skaters)) {
        $skater_name = get_the_title($skaters[0]);
        $new_title = "{$season} – {$skater_name}";

        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $new_title,
            'post_name'  => sanitize_title($new_title),
        ]);
    }
}

/**
 * Set post title for Weekly Plan as "Week of [Date] – Skater".
 */
add_action('acf/save_post', 'spd_set_weekly_plan_title', 20);
function spd_set_weekly_plan_title($post_id) {
    if (get_post_type($post_id) !== 'weekly_plan') return;

    $week_start = get_field('week_start', $post_id);
    $skater     = get_field('skater', $post_id);

    if ($week_start && is_array($skater) && !empty($skater)) {
        $skater_name = get_the_title($skater[0]);
        $date = date('F j', strtotime($week_start));

        wp_update_post([
            'ID'         => $post_id,
            'post_title' => "Week of {$date} – {$skater_name}",
            'post_name'  => sanitize_title("week-of-{$date}-{$skater_name}"),
        ]);
    }
}

/**
 * Set post title for Competition Result as "Skater – Competition".
 */
add_action('acf/save_post', 'spd_set_competition_result_title', 20);
function spd_set_competition_result_title($post_id) {
    if (get_post_type($post_id) !== 'competition_result') return;

    $skater      = get_field('linked_skater', $post_id);
    $competition = get_field('competition', $post_id);

    if (is_array($skater) && !empty($skater) && $competition) {
        $skater_name = get_the_title($skater[0]);
        $comp_name   = get_the_title($competition);

        wp_update_post([
            'ID'         => $post_id,
            'post_title' => "{$skater_name} – {$comp_name}",
            'post_name'  => sanitize_title("{$skater_name}-{$comp_name}"),
        ]);
    }
}

/**
 * Set post title for Meeting Log using meeting_title field.
 */
add_action('acf/save_post', 'spd_set_meeting_title', 20);
function spd_set_meeting_title($post_id) {
    if (get_post_type($post_id) !== 'meeting_log') return;

    $title = get_field('meeting_title', $post_id);
    if ($title) {
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => sanitize_text_field($title),
        ]);
    }
}

function spd_auto_title_gap_analysis($post_id) {
    if (get_post_type($post_id) !== 'gap_analysis') return;

    // Prevent infinite loop
    remove_action('acf/save_post', 'spd_auto_title_gap_analysis', 20);

    $skater = get_field('skater', $post_id);
    if ($skater) {
        $skater_id   = is_array($skater) ? ($skater[0] ?? null) : $skater;
        $skater_name = get_the_title($skater_id);
        $title       = $skater_name . ' – Gap Analysis';

        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $title,
        ]);
    }

    add_action('acf/save_post', 'spd_auto_title_gap_analysis', 20);
}
add_action('acf/save_post', 'spd_auto_title_gap_analysis', 20);
