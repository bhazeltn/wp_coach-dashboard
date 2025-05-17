<?php
// --- Coach Dashboard: Injury & Health Logs ---

echo '<h2>Injury & Health Status</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=injury_log') . '">Add Injury Log</a></p>';

// Fetch all injury logs
$logs = get_posts([
    'post_type'   => 'injury_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'date_reported',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
]);

if (empty($logs)) {
    echo '<p>No injury or health logs recorded.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead><tr>
    <th>Date</th>
    <th>Skater</th>
    <th>Injury / Condition</th>
    <th>Status</th>
    <th>Recovery Notes</th>
    <th></th>
</tr></thead><tbody>';

foreach ($logs as $log) {
    $skater   = get_field('linked_skater', $log->ID);
    $date     = get_field('date_reported', $log->ID);
    $injury   = get_field('injury_description', $log->ID);
    $status   = get_field('recovery_status', $log->ID) ?: '—';
    $notes    = get_field('recovery_notes', $log->ID);
    $edit_url = get_edit_post_link($log->ID);

    echo '<tr>';
    echo '<td>' . esc_html($date ?: '—') . '</td>';
    echo '<td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>';
    echo '<td>' . esc_html($injury ?: '—') . '</td>';
    echo '<td>' . esc_html($status) . '</td>';
    echo '<td>' . esc_html(wp_trim_words(strip_tags($notes), 15)) . '</td>';
    echo '<td><a class="button small" href="' . esc_url($edit_url) . '">Edit</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';
