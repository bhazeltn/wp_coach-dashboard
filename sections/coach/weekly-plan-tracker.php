<?php
// --- Coach Dashboard: Weekly Plan Tracker ---

echo '<h2>Weekly Plan Tracker</h2>';

$today         = date('Y-m-d');
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week   = date('Y-m-d', strtotime('sunday this week'));

// Fetch weekly plans for the current week
$weekly_plans = get_posts([
    'post_type'   => 'weekly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'week_start_date',
        'value'   => [$start_of_week, $end_of_week],
        'compare' => 'BETWEEN',
        'type'    => 'DATE'
    ]],
    'meta_key' => 'week_start_date',
    'orderby'  => 'meta_value',
    'order'    => 'ASC'
]);

if (empty($weekly_plans)) {
    echo '<p>No relevant weekly plans found.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead>
    <tr>
        <th>Week Start</th>
        <th>Skater</th>
        <th>Theme</th>
        <th># Goals</th>
        <th># Session Logs</th>
        <th></th>
    </tr>
</thead><tbody>';

foreach ($weekly_plans as $plan) {
    $start_raw     = get_field('week_start_date', $plan->ID);
    $start         = $start_raw ? (function_exists('coach_format_date') ? coach_format_date($start_raw) : $start_raw) : '—';
    $theme         = get_field('weekly_theme', $plan->ID) ?: '—';
    $skater        = get_field('linked_skater', $plan->ID);
    $skater_name   = $skater ? get_the_title($skater->ID) : '—';
    $edit_url      = get_edit_post_link($plan->ID);

    $goals = get_field('linked_goals', $plan->ID);
    $goal_count = is_array($goals) ? count($goals) : 0;

    $logs = get_posts([
        'post_type'   => 'session_log',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query'  => [[
            'key'     => 'linked_weekly_plan',
            'value'   => $plan->ID,
            'compare' => '='
        ]]
    ]);
    $log_count = count($logs);

    echo '<tr>';
    echo '<td>' . esc_html($start) . '</td>';
    echo '<td>' . esc_html($skater_name) . '</td>';
    echo '<td>' . esc_html($theme) . '</td>';
    echo '<td>' . esc_html($goal_count) . '</td>';
    echo '<td>' . esc_html($log_count) . '</td>';
    echo '<td><a class="button small" href="' . esc_url($edit_url) . '">Edit</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';
