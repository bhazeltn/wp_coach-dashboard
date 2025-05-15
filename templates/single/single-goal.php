<?php
/**
 * Template for displaying a single Goal post
 */

get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $goal_id = get_the_ID();
        $target_date = get_field('target_date', $goal_id);
        $status      = get_field('goal_status', $goal_id);
        $timeframe   = get_field('goal_timeframe', $goal_id);
        $skater      = get_field('linked_skater', $goal_id);
        $edit_link   = get_edit_post_link($goal_id);

        echo '<div class="wrap coach-dashboard single-goal">';

        if ($skater && is_object($skater)) {
            $skater_link = site_url('/skater/' . $skater->post_name);
            echo '<p><a class="button" href="' . esc_url($skater_link) . '">← Back to Skater</a></p>';
        }

        echo '<h1>' . esc_html(get_the_title()) . '</h1>';

        echo '<table class="widefat fixed striped">';
        echo '<tbody>';
        echo '<tr><th>Timeframe</th><td>' . esc_html($timeframe ?: '—') . '</td></tr>';
        echo '<tr><th>Status</th><td>' . esc_html($status ?: '—') . '</td></tr>';
        echo '<tr><th>Target Date</th><td>' . esc_html($target_date ?: '—') . '</td></tr>';
        echo '</tbody>';
        echo '</table>';

        echo '<h2>Notes</h2>';
        the_content();

        if ($edit_link) {
            echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Goal</a></p>';
        }

        echo '</div>';

    endwhile;
endif;

get_footer();
