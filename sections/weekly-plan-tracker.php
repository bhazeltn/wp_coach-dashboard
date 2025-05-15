<?php
echo '<h2>Weekly Plan Tracker</h2>';

$today = date('Y-m-d');
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week = date('Y-m-d', strtotime('sunday this week'));

$weekly_plans = get_posts([
    'post_type' => 'weekly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [[
        'key' => 'week_start_date',
        'value' => [$start_of_week, $end_of_week],
        'compare' => 'BETWEEN',
        'type' => 'DATE'
    ]],
    'meta_key' => 'week_start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC'
]);

if (empty($weekly_plans)) {
    echo '<p>No relevant weekly plans found.</p>';
    return;
}

echo '<table class="widefat fixed striped">
    <thead>
        <tr>
            <th>Week Start</th>
            <th>Skater</th>
            <th>Theme</th>
            <th># Goals</th>
            <th># Session Logs</th>
        </tr>
    </thead>
    <tbody>';

foreach ($weekly_plans as $plan) {
    $start = get_field('week_start_date', $plan->ID);
    $theme = get_field('weekly_theme', $plan->ID) ?: '—';

    $skater = get_field('linked_skater', $plan->ID);
    $skater_name = $skater ? get_the_title($skater->ID) : '—';

    $goal_count = is_array(get_field('linked_goals', $plan->ID)) ? count(get_field('linked_goals', $plan->ID)) : 0;

    $logs = get_posts([
        'post_type' => 'session_log',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => [[
            'key' => 'linked_weekly_plan',
            'value' => $plan->ID,
            'compare' => '='
        ]]
    ]);
    $log_count = count($logs);

    echo '<tr>
        <td>' . esc_html($start ?: '—') . '</td>
        <td>' . esc_html($skater_name) . '</td>
        <td>' . esc_html($theme) . '</td>
        <td>' . $goal_count . '</td>
        <td>' . $log_count . '</td>
    </tr>';
}

echo '</tbody></table>';
