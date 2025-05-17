<?php
// --- Coach Dashboard: Missed or Stalled Goals ---

echo '<h2>Stalled or Missed Goals</h2>';

$today = date('Y-m-d');

$goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        'relation' => 'OR',
        [
            'key'     => 'goal_status',
            'value'   => ['On Hold', 'Abandoned'],
            'compare' => 'IN'
        ],
        [
            'relation' => 'AND',
            [
                'key'     => 'target_date',
                'value'   => $today,
                'compare' => '<',
                'type'    => 'DATE'
            ],
            [
                'key'     => 'goal_status',
                'value'   => 'Achieved',
                'compare' => '!=',
            ]
        ]
    ],
    'meta_key' => 'target_date',
    'orderby'  => 'meta_value',
    'order'    => 'ASC'
]);

if (empty($goals)) {
    echo '<p>No missed or stalled goals found.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead><tr>
    <th>Skater</th>
    <th>Goal</th>
    <th>Status</th>
    <th>Target Date</th>
    <th>Overdue</th>
</tr></thead><tbody>';

foreach ($goals as $goal) {
    $skater  = get_field('linked_skater', $goal->ID);
    $status  = get_field('goal_status', $goal->ID) ?: '—';
    $target  = get_field('target_date', $goal->ID);
    $overdue = '—';

    if ($target && strtotime($target) < time()) {
        $interval = (new DateTime($target))->diff(new DateTime());
        $overdue = $interval->days . ' day' . ($interval->days !== 1 ? 's' : '');
    }

    echo '<tr>';
    echo '<td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>';
    echo '<td>' . esc_html(get_the_title($goal->ID)) . '</td>';
    echo '<td>' . esc_html($status) . '</td>';
    echo '<td>' . esc_html($target ?: '—') . '</td>';
    echo '<td>' . esc_html($overdue) . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';
