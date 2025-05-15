<?php
// --- Goals Section ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Goals</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=goal') . '">Add Goal</a></p>';

// Fetch all goals linked to this skater
$goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'linked_skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]]
]);

if ($goals) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>Goal</th>
            <th>Timeframe</th>
            <th>Status</th>
            <th>Target Date</th>
        </tr></thead><tbody>';

    foreach ($goals as $goal) {
        $title      = get_the_title($goal->ID) ?: '[Untitled]';
        $timeframe  = get_field('goal_timeframe', $goal->ID) ?: '—';
        $status     = get_field('goal_status', $goal->ID) ?: '—';
        $target     = get_field('target_date', $goal->ID) ?: '—';

        echo '<tr>
            <td>' . esc_html($title) . '</td>
            <td>' . esc_html($timeframe) . '</td>
            <td>' . esc_html($status) . '</td>
            <td>' . esc_html($target) . '</td>
        </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No goals found.</p>';
}
