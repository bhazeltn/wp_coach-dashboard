<?php
// --- Session Logs (This Month) ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Session Logs (This Month)</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=session_log') . '">Add Session Log</a></p>';

// Current month range
$month_start = date('Y-m-01');
$month_end   = date('Y-m-t');

// Get session logs for this skater in the current month
$logs = get_posts([
    'post_type'   => 'session_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'date',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
    'meta_query'  => [
        [
            'key'     => 'linked_skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ],
        [
            'key'     => 'date',
            'value'   => [$month_start, $month_end],
            'compare' => 'BETWEEN',
            'type'    => 'DATE',
        ]
    ],
]);

if ($logs) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>Date</th>
            <th>Weekly Plan</th>
            <th>Notes</th>
        </tr></thead><tbody>';

    foreach ($logs as $log) {
        $date  = get_field('date', $log->ID) ?: '—';
        $plan  = get_field('linked_weekly_plan', $log->ID);
        $notes = get_field('session_notes', $log->ID);

        echo '<tr>
            <td>' . esc_html($date) . '</td>
            <td>' . esc_html(is_object($plan) ? get_the_title($plan->ID) : '—') . '</td>
            <td>' . esc_html(wp_trim_words($notes ?: '—', 20)) . '</td>
        </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No session logs found for this month.</p>';
}
