<?php
// --- Weekly Plans ---
echo '<h2>Weekly Plans</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=weekly_plan') . '">Add Weekly Plan</a></p>';


$weekly_plans = get_posts([
    'post_type' => 'weekly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key' => 'week_start_date',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'meta_query' => [[
        'key' => 'linked_skater',
        'value' => $skater_id,
        'compare' => '='
    ]]
]);

if ($weekly_plans) {
    echo '<table class="widefat fixed striped">
        <thead><tr><th>Week Start</th><th>Theme</th><th>Notes</th><th># Goals</th><th># Logs</th></tr></thead><tbody>';
    foreach ($weekly_plans as $wp) {
        $date = get_field('week_start_date', $wp->ID);
        $theme = get_field('weekly_theme', $wp->ID);
        $notes = wp_trim_words(get_field('notes', $wp->ID), 12);
        $goal_count = count(get_field('linked_goals', $wp->ID) ?: []);

        $log_count = count(get_posts([
            'post_type' => 'session_log',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => [[
                'key' => 'linked_weekly_plan',
                'value' => $wp->ID,
                'compare' => '='
            ]]
        ]));

        echo '<tr>
            <td>' . esc_html($date ?: '—') . '</td>
            <td>' . esc_html($theme ?: '—') . '</td>
            <td>' . esc_html($notes ?: '—') . '</td>
            <td>' . $goal_count . '</td>
            <td>' . $log_count . '</td>
        </tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No weekly plans found.</p>';
}
