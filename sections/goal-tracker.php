<?php
echo '<h2>Mid/Long-Term Goals</h2>';

$long_goals = get_posts([
    'post_type' => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'goal_timeframe',
            'value' => ['Long Term', 'Medium Term', 'Season'],
            'compare' => 'IN'
        ],
        [
            'key' => 'goal_status',
            'value' => ['Not Started', 'In Progress'],
            'compare' => 'IN'
        ]
    ],
    'orderby' => 'meta_value',
    'meta_key' => 'target_date',
    'order' => 'ASC'
]);

if (empty($long_goals)) {
    echo '<p>No mid/long-term goals found.</p>';
} else {
    echo '<table class="widefat fixed striped">
        <thead>
            <tr><th>Skater</th><th>Goal</th><th>Timeframe</th><th>Status</th><th>Target Date</th></tr>
        </thead><tbody>';
    foreach ($long_goals as $goal) {
        $skater = get_field('linked_skater', $goal->ID);
        echo '<tr>
            <td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>
            <td>' . esc_html(get_the_title($goal->ID)) . '</td>
            <td>' . esc_html(get_field('goal_timeframe', $goal->ID)) . '</td>
            <td>' . esc_html(get_field('goal_status', $goal->ID)) . '</td>
            <td>' . esc_html(get_field('target_date', $goal->ID)) . '</td>
        </tr>';
    }
    echo '</tbody></table>';
}

echo '<h2>Active Weekly Goals</h2>';

$weekly_goals = get_posts([
    'post_type' => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'goal_timeframe',
            'value' => ['Weekly', 'Microcycle'],
            'compare' => 'IN'
        ],
        [
            'key' => 'target_date',
            'value' => [date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday next week'))],
            'compare' => 'BETWEEN',
            'type' => 'DATE'
        ]
    ],
    'orderby' => 'meta_value',
    'meta_key' => 'target_date',
    'order' => 'ASC'
]);

if (empty($weekly_goals)) {
    echo '<p>No active weekly goals found.</p>';
} else {
    echo '<table class="widefat fixed striped">
        <thead>
            <tr><th>Skater</th><th>Goal</th><th>Status</th><th>Target Date</th></tr>
        </thead><tbody>';
    foreach ($weekly_goals as $goal) {
        $skater = get_field('linked_skater', $goal->ID);
        echo '<tr>
            <td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>
            <td>' . esc_html(get_the_title($goal->ID)) . '</td>
            <td>' . esc_html(get_field('goal_status', $goal->ID)) . '</td>
            <td>' . esc_html(get_field('target_date', $goal->ID)) . '</td>
        </tr>';
    }
    echo '</tbody></table>';
}

echo '<h2>Stalled or Missed Goals</h2>';

$missed_goals = get_posts([
    'post_type' => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        'relation' => 'OR',
        [
            'key' => 'goal_status',
            'value' => ['Abandoned', 'On Hold'],
            'compare' => 'IN'
        ],
        [
            'relation' => 'AND',
            [
                'key' => 'target_date',
                'value' => date('Y-m-d'),
                'compare' => '<',
                'type' => 'DATE'
            ],
            [
                'key' => 'goal_status',
                'value' => 'Achieved',
                'compare' => '!='
            ]
        ]
    ],
    'orderby' => 'meta_value',
    'meta_key' => 'target_date',
    'order' => 'ASC'
]);

if (empty($missed_goals)) {
    echo '<p>No stalled or missed goals found.</p>';
} else {
    echo '<table class="widefat fixed striped">
        <thead>
            <tr><th>Skater</th><th>Goal</th><th>Status</th><th>Target Date</th></tr>
        </thead><tbody>';
    foreach ($missed_goals as $goal) {
        $skater = get_field('linked_skater', $goal->ID);
        echo '<tr>
            <td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>
            <td>' . esc_html(get_the_title($goal->ID)) . '</td>
            <td>' . esc_html(get_field('goal_status', $goal->ID)) . '</td>
            <td>' . esc_html(get_field('target_date', $goal->ID)) . '</td>
        </tr>';
    }
    echo '</tbody></table>';
}

