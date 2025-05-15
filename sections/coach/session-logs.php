<?php
// --- Coach Dashboard: Session Logs (This Month) ---
echo '<h2>Recent Session Logs (This Month)</h2>';

// Date range for current month
$month_start = date('Y-m-01');
$month_end   = date('Y-m-t');

// Get all session logs this month
$logs = get_posts([
    'post_type'   => 'session_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'date',
        'value'   => [$month_start, $month_end],
        'compare' => 'BETWEEN',
        'type'    => 'DATE',
    ]],
    'meta_key' => 'date',
    'orderby'  => 'meta_value',
    'order'    => 'DESC'
]);

if (empty($logs)) {
    echo '<p>No session logs found for this month.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead>
    <tr>
        <th>Date</th>
        <th>Skater</th>
        <th>Weekly Plan</th>
        <th>Notes</th>
        <th></th>
    </tr>
</thead><tbody>';

foreach ($logs as $log) {
    $date   = get_field('date', $log->ID);
    $skater = get_field('linked_skater', $log->ID);
    $plan   = get_field('linked_weekly_plan', $log->ID);
    $notes  = get_field('session_notes', $log->ID);

    $formatted_date = $date ? (function_exists('coach_format_date') ? coach_format_date($date) : $date) : '—';
    $skater_name    = $skater ? get_the_title($skater->ID) : '—';
    $plan_name      = $plan && is_object($plan) ? get_the_title($plan->ID) : '—';
    $note_preview   = $notes ? wp_trim_words(strip_tags($notes), 20) : '—';
    $view_link      = get_permalink($log->ID);

    echo '<tr class="skater-' . sanitize_title($skater_name) . '">';
    echo '<td>' . esc_html($formatted_date) . '</td>';
    echo '<td>' . esc_html($skater_name) . '</td>';
    echo '<td>' . esc_html($plan_name) . '</td>';
    echo '<td>' . esc_html($note_preview) . '</td>';
    echo '<td><a class="button small" href="' . esc_url($view_link) . '">View</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';
