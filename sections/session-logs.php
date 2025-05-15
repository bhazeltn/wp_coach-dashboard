<?php
echo '<h2>Recent Session Logs (This Month)</h2>';

$month_start = date('Y-m-01');
$month_end = date('Y-m-t');

$logs = get_posts([
    'post_type' => 'session_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [[
        'key' => 'date',
        'value' => [$month_start, $month_end],
        'compare' => 'BETWEEN',
        'type' => 'DATE'
    ]],
    'meta_key' => 'date',
    'orderby' => 'meta_value',
    'order' => 'DESC'
]);

if (empty($logs)) {
    echo '<p>No session logs found for this month.</p>';
    return;
}

echo '<table class="widefat fixed striped">
    <thead>
        <tr><th>Date</th><th>Skater</th><th>Weekly Plan</th><th>Notes</th></tr>
    </thead><tbody>';

foreach ($logs as $log) {
    $date = get_field('date', $log->ID);
    $skater = get_field('linked_skater', $log->ID);
    $plan = get_field('linked_weekly_plan', $log->ID);
    $notes = wp_trim_words(strip_tags(get_field('session_notes', $log->ID)), 20);

    echo '<tr>
        <td>' . esc_html($date ?: '—') . '</td>
        <td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>
        <td>' . esc_html($plan ? get_the_title($plan->ID) : '—') . '</td>
        <td>' . esc_html($notes ?: '—') . '</td>
    </tr>';
}

echo '</tbody></table>';
