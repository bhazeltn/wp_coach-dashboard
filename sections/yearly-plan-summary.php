<?php
echo '<h2>Yearly Plan Summary</h2>';

$plans = get_posts([
    'post_type' => 'yearly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

if (empty($plans)) {
    echo '<p>No yearly plans found.</p>';
    return;
}

echo '<table class="widefat fixed striped">
    <thead>
        <tr>
            <th>Season</th>
            <th>Skater(s)</th>
            <th>Start - End</th>
            <th>Peak Type</th>
            <th>Primary Goal</th>
        </tr>
    </thead>
    <tbody>';

foreach ($plans as $plan) {
    $season_link = '<a href="' . get_edit_post_link($plan->ID) . '">' . esc_html(get_the_title($plan->ID)) . '</a>';

    $skaters = get_field('linked_skaters', $plan->ID);
    $skater_names = $skaters ? implode(', ', array_map(fn($s) => get_the_title($s->ID), $skaters)) : '—';

    $dates = get_field('season_dates', $plan->ID);
    $start = isset($dates['start_date']) ? esc_html($dates['start_date']) : '—';
    $end = isset($dates['end_date']) ? esc_html($dates['end_date']) : '—';

    $peak_type = get_field('peak_planning_peak_type', $plan->ID) ?: '—';
    $primary_goal = get_field('primary_goal', $plan->ID) ?: '—';

    echo '<tr>
        <td>' . $season_link . '</td>
        <td>' . esc_html($skater_names) . '</td>
        <td>' . $start . ' – ' . $end . '</td>
        <td>' . esc_html($peak_type) . '</td>
        <td>' . esc_html(wp_trim_words(strip_tags($primary_goal), 10)) . '</td>
    </tr>';
}

echo '</tbody></table>';
