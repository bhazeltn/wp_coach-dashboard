<?php
// --- Session Logs ---
echo '<h2>Session Logs (This Month)</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=session_log') . '">Add Session Log</a></p>';


$month_start = date('Y-m-01');
$month_end = date('Y-m-t');

$logs = get_posts([
    'post_type' => 'session_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'linked_skater',
            'value' => $skater_id,
            'compare' => '='
        ],
        [
            'key' => 'date',
            'value' => [$month_start, $month_end],
            'compare' => 'BETWEEN',
            'type' => 'DATE'
        ]
    ],
    'meta_key' => 'date',
    'orderby' => 'meta_value',
    'order' => 'DESC'
]);

if ($logs) {
    echo '<table class="widefat fixed striped">
        <thead><tr><th>Date</th><th>Weekly Plan</th><th>Notes</th></tr></thead><tbody>';
    foreach ($logs as $log) {
        $date = get_field('date', $log->ID);
        $plan = get_field('linked_weekly_plan', $log->ID);
        $notes = wp_trim_words(get_field('session_notes', $log->ID), 20);

        echo '<tr>
            <td>' . esc_html($date ?: '—') . '</td>
            <td>' . esc_html($plan ? get_the_title($plan->ID) : '—') . '</td>
            <td>' . esc_html($notes ?: '—') . '</td>
        </tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No session logs found for this month.</p>';
}
