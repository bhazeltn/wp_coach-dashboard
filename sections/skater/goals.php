<?php
// --- Goals ---
echo '<h2>Goals</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=goal') . '">Add Goal</a></p>';


$goals = get_posts([
    'post_type' => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key' => 'target_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => [[
        'key' => 'linked_skater',
        'value' => $skater_id,
        'compare' => '='
    ]]
]);

if ($goals) {
    echo '<table class="widefat fixed striped">
        <thead><tr><th>Goal</th><th>Timeframe</th><th>Status</th><th>Target Date</th></tr></thead><tbody>';
    foreach ($goals as $goal) {
        echo '<tr>
            <td>' . esc_html(get_the_title($goal->ID)) . '</td>
            <td>' . esc_html(get_field('goal_timeframe', $goal->ID) ?: '—') . '</td>
            <td>' . esc_html(get_field('goal_status', $goal->ID) ?: '—') . '</td>
            <td>' . esc_html(get_field('target_date', $goal->ID) ?: '—') . '</td>
        </tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No goals found.</p>';
}
