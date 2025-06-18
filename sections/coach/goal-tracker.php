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
    $skater_raw = get_field('skater', $goal_id);
    $skater = is_array($skater_raw) ? ($skater_raw[0] ?? null) : $skater_raw;
    return $skater ? get_the_title($skater) : '—';
}

function coach_format_date_safe($date) {
    return function_exists('coach_format_date') ? coach_format_date($date) : $date;
}

// --- Determine visible skaters
$visible      = spd_get_visible_skaters();
$visible_ids  = wp_list_pluck($visible, 'ID');

if (empty($visible_ids)) {
    echo '<p>No skaters assigned to your account.</p>';
    return;
}

// Meta query clause for filtering goals by skater
$skater_clause = [
    'relation' => 'OR',
    ...array_map(function($id) {
        return [
            'key'     => 'skater',
            'value'   => '"' . $id . '"',
            'compare' => 'LIKE'
        ];
    }, $visible_ids)
];

//
// --- Mid/Long-Term Goals
//
echo '<h2>Mid/Long-Term Goals</h2>';

$long_goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        'relation' => 'AND',
        $skater_clause,
        [
            'key'     => 'goal_timeframe',
            'value'   => ['long', 'medium', 'season'],
            'compare' => 'IN'
        ],
        [
            'key'     => 'current_status',
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
        $title = get_the_title($goal->ID) ?: '[Untitled]';
        $view_url = get_permalink($goal->ID);
        $edit_url = site_url('/edit-goal?goal_id=' . $goal->ID);
        $title_links = esc_html($title) . ' <span style="font-size: 0.9em;">(' .
            '<a href="' . esc_url($view_url) . '">View</a> | ' .
            '<a href="' . esc_url($edit_url) . '">Update</a>' .
            ')</span>';

        $timeframe = get_field('goal_timeframe', $goal->ID) ?: '—';
        $status = get_field('current_status', $goal->ID) ?: '—';

        $target = get_field('target_date', $goal->ID);
        if ($target) {
            $dt = DateTime::createFromFormat('d/m/Y', $target);
            $target = $dt ? date_i18n('F j, Y', $dt->getTimestamp()) : $target;
        } else {
            $target = '—';
        }

        echo '<td>' . esc_html(coach_get_goal_skater_name($goal->ID)) . '</td>';
        echo '<td>' . $title_links . '</td>';
        echo '<td>' . esc_html($timeframe) . '</td>';
        echo '<td>' . esc_html($status) . '</td>';
        echo '<td>' . esc_html($target) . '</td>';
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
        'relation' => 'AND',
        $skater_clause,
        [
            'key'     => 'goal_timeframe',
            'value'   => ['week', 'micro'],
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
        $title = get_the_title($goal->ID) ?: '[Untitled]';
        $view_url = get_permalink($goal->ID);
        $edit_url = site_url('/edit-goal?goal_id=' . $goal->ID);
        $title_links = esc_html($title) . ' <span style="font-size: 0.9em;">(' .
            '<a href="' . esc_url($view_url) . '">View</a> | ' .
            '<a href="' . esc_url($edit_url) . '">Update</a>' .
            ')</span>';

        $status = get_field('current_status', $goal->ID) ?: '—';

        $target = get_field('target_date', $goal->ID);
        if ($target) {
            $dt = DateTime::createFromFormat('d/m/Y', $target);
            $target = $dt ? date_i18n('F j, Y', $dt->getTimestamp()) : $target;
        } else {
            $target = '—';
        }

        echo '<td>' . esc_html(coach_get_goal_skater_name($goal->ID)) . '</td>';
        echo '<td>' . $title_links . '</td>';
        echo '<td>' . esc_html($status) . '</td>';
        echo '<td>' . esc_html($target) . '</td>';
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
        'relation' => 'AND',
        $skater_clause,
        [
            'relation' => 'OR',
            [
                'key'     => 'current_status',
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
                    'key'     => 'current_status',
                    'value'   => 'Achieved',
                    'compare' => '!=',
                ]
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
        $title = get_the_title($goal->ID) ?: '[Untitled]';
        $view_url = get_permalink($goal->ID);
        $edit_url = site_url('/edit-goal?goal_id=' . $goal->ID);
        $title_links = esc_html($title) . ' <span style="font-size: 0.9em;">(' .
            '<a href="' . esc_url($view_url) . '">View</a> | ' .
            '<a href="' . esc_url($edit_url) . '">Update</a>' .
            ')</span>';

        $status = get_field('current_status', $goal->ID) ?: '—';

        $target_raw = get_field('target_date', $goal->ID);
        $target = '—';
        $overdue = '';

        if ($target_raw) {
            $dt = DateTime::createFromFormat('d/m/Y', $target_raw);
            if ($dt) {
                $target = date_i18n('F j, Y', $dt->getTimestamp());
                $interval = $dt->diff(new DateTime());
                $overdue = ' (' . $interval->days . ' day' . ($interval->days !== 1 ? 's' : '') . ' overdue)';
            }
        }

        echo '<td>' . esc_html(coach_get_goal_skater_name($goal->ID)) . '</td>';
        echo '<td>' . $title_links . '</td>';
        echo '<td>' . esc_html($status) . '</td>';
        echo '<td>' . esc_html($target . $overdue) . '</td>';
    });
}
