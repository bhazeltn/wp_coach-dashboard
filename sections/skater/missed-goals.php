<?php
// --- Missed or Stalled Goals ---
echo '<h2>Missed or Stalled Goals</h2>';

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

$missed = [];
$today = date('Y-m-d');

foreach ($goals as $goal) {
    $status = get_field('goal_status', $goal->ID);
    $target_date = get_field('target_date', $goal->ID);

    if (in_array($status, ['On Hold', 'Abandoned']) || ($target_date && $target_date < $today && $status !== 'Achieved')) {
        $missed[] = $goal;
    }
}

if ($missed) {
    echo '<table class="widefat fixed striped">
        <thead><tr><th>Goal</th><th>Status</th><th>Target Date</th></tr></thead><tbody>';
    foreach ($missed as $goal) {
        echo '<tr>
            <td>' . esc_html(get_the_title($goal->ID)) . '</td>
            <td>' . esc_html(get_field('goal_status', $goal->ID) ?: '—') . '</td>
            <td>' . esc_html(get_field('target_date', $goal->ID) ?: '—') . '</td>
        </tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No missed or stalled goals found.</p>';
}
