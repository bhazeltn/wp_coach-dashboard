<?php
// --- Coach Dashboard: Yearly Plan Summary ---

echo '<h2>Yearly Plan Summary</h2>';

$plans = get_posts([
    'post_type'   => 'yearly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

if (empty($plans)) {
    echo '<p>No yearly plans found.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead>
    <tr>
        <th>Season</th>
        <th>Skater(s)</th>
        <th>Start – End</th>
        <th>Peak Type</th>
        <th>Primary Goal</th>
    </tr>
</thead><tbody>';

foreach ($plans as $plan) {
    $plan_id     = $plan->ID;
    $title       = get_the_title($plan_id);
    $edit_link   = get_edit_post_link($plan_id);
    $season_link = '<a href="' . esc_url($edit_link) . '">' . esc_html($title) . '</a>';

    $skaters = get_field('linked_skaters', $plan_id);
    $skater_names = '—';
    if ($skaters && is_array($skaters)) {
        $names = array_map(fn($s) => get_the_title($s->ID), $skaters);
        $skater_names = implode(', ', array_map('esc_html', $names));
    }

    $dates = get_field('season_dates', $plan_id);
    $start = isset($dates['start_date']) ? (function_exists('coach_format_date') ? coach_format_date($dates['start_date']) : $dates['start_date']) : '—';
    $end   = isset($dates['end_date'])   ? (function_exists('coach_format_date') ? coach_format_date($dates['end_date'])   : $dates['end_date'])   : '—';

    $peak_type = get_field('peak_planning_peak_type', $plan_id) ?: '—';
    $goal_raw  = get_field('primary_goal', $plan_id);
    $primary_goal = $goal_raw ? wp_trim_words(strip_tags($goal_raw), 12) : '—';

    echo '<tr>';
    echo '<td>' . $season_link . '</td>';
    echo '<td>' . esc_html($skater_names) . '</td>';
    echo '<td>' . esc_html($start) . ' – ' . esc_html($end) . '</td>';
    echo '<td>' . esc_html($peak_type) . '</td>';
    echo '<td>' . esc_html($primary_goal) . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';
