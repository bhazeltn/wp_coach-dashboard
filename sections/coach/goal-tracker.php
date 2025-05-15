<?php
// --- Coach Dashboard: Goal Tracker ---

function coach_display_goal_table($goals, $columns, $row_renderer) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>';
    foreach ($columns as $col) {
        echo '<th>' . esc_html($col) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($goals as $goal) {
        echo '<tr>';
        call_user_func($row_renderer, $goal);
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function coach_get_goal_skater_name($goal_id) {
    $skater = get_field('linked_skater', $goal_id);
    return $skater ? get_the_title($skater->ID) : '—';
}

function coach_format_date_safe($date) {
    return function_exists('coach_format_date') ? coach_format_date($date) : $date;
}

//
// --- Mid/Long-Term Goals
//
echo '<h2>Mid/Long-Term Goals</h2>';

$long_goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        [
            'key'     => 'goal_timeframe',
            'value'   => ['Long Term', 'Medium Term', 'Season'],
            'compare' => 'IN'
        ],
        [
            'key'     => 'goal_status',
            'value'   => ['Not Started', 'In Progress'],
            'compare' => 'IN'
        ]
    ],
    'meta_key' => 'target_date',
    'orderby'  => 'meta_value',
    'order'    => 'ASC'
]);

if (empty($long_goals)) {
    echo '<p>No mid/long-term goals found.</p>';
} else {
    coach_display_goal_table($long_goals, ['Skater', 'Goal', 'Timeframe', 'Status', 'Target Date'], function ($goal) {
        echo '<td>' . esc_html(coach_get_goal_skater_name($goal->ID)) . '</td>';
        echo '<td>' . esc_html(get_the_title($goal->ID)) . '</td>';
        echo '<td>' . esc_html(get_field('goal_timeframe', $goal->ID) ?: '—') . '</td>';
        echo '<td>' . esc_html(get_field('goal_status', $goal->ID) ?: '—') . '</td>';
        echo '<td>' . esc_html(coach_format_date_safe(get_field('target_date', $goal->ID) ?: '—')) . '</td>';
    });
}

//
// --- Weekly Goals
//
echo '<h2>Active Weekly Goals</h2>';

$weekly_goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        [
            'key'     => 'goal_timeframe',
            'value'   => ['Weekly', 'Microcycle'],
            'compare' => 'IN'
        ],
        [
            'key'     => 'target_date',
            'value'   => [
                date('Y-m-d', strtotime('monday this week')),
                date('Y-m-d', strtotime('sunday next week'))
            ],
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        ]
    ],
    'meta_key' => 'target_date',
    'orderby'  => 'meta_value',
    'order'    => 'ASC'
]);

if (empty($weekly_goals)) {
    echo '<p>No active weekly goals found.</p>';
} else {
    coach_display_goal_table($weekly_goals, ['Skater', 'Goal', 'Status', 'Target Date'], function ($goal) {
        echo '<td>' . esc_html(coach_get_goal_skater_name($goal->ID)) . '</td>';
        echo '<td>' . esc_html(get_the_title($goal->ID)) . '</td>';
        echo '<td>' . esc_html(get_field('goal_status', $goal->ID) ?: '—') . '</td>';
        echo '<td>' . esc_html(coach_format_date_safe(get_field('target_date', $goal->ID) ?: '—')) . '</td>';
    });
}

//
// --- Missed or Stalled Goals
//
echo '<h2>Stalled or Missed Goals</h2>';

$missed_goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        'relation' => 'OR',
        [
            'key'     => 'goal_status',
            'value'   => ['Abandoned', 'On Hold'],
            'compare' => 'IN'
        ],
        [
            'relation' => 'AND',
            [
                'key'     => 'target_date',
                'value'   => date('Y-m-d'),
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

if (empty($missed_goals)) {
    echo '<p>No stalled or missed goals found.</p>';
} else {
    coach_display_goal_table($missed_goals, ['Skater', 'Goal', 'Status', 'Target Date'], function ($goal) {
        $target = get_field('target_date', $goal->ID);
        $overdue = '—';
        if ($target && strtotime($target) < time()) {
            $interval = (new DateTime($target))->diff(new DateTime());
            $overdue = $interval->days . ' day' . ($interval->days !== 1 ? 's' : '');
        }

        echo '<td>' . esc_html(coach_get_goal_skater_name($goal->ID)) . '</td>';
        echo '<td>' . esc_html(get_the_title($goal->ID)) . '</td>';
        echo '<td>' . esc_html(get_field('goal_status', $goal->ID) ?: '—') . '</td>';
        echo '<td>' . esc_html(coach_format_date_safe($target) . ($overdue !== '—' ? " ({$overdue} overdue)" : '')) . '</td>';
    });
}
