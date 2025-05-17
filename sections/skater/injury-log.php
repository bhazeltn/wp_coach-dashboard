<?php
// --- Skater-Specific: Injury Log ---

$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Injury & Health Log</h2>';
echo '<p><a class="button" href="' . esc_url(site_url('/create-injury-log')) . '">Add Injury Log</a></p>';

if (!$skater_id) {
    echo '<p>No skater context available.</p>';
    return;
}

// Fetch injury logs
$logs = get_posts([
    'post_type'   => 'injury_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'injured_skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]],
    'meta_key' => 'date_of_onset',
    'orderby'  => 'meta_value',
    'order'    => 'DESC',
]);

if (empty($logs)) {
    echo '<p>No injuries or health concerns recorded for this skater.</p>';
    return;
}

// Table layout
echo '<table class="widefat fixed striped">';
echo '<thead><tr>
    <th>Status</th>
    <th>Date of Onset</th>
    <th>Severity</th>
    <th>Body Area</th>
    <th>Actions</th>
</tr></thead><tbody>';

foreach ($logs as $log) {
    $log_id = $log->ID;

    // Date of Onset
    $onset_raw = get_field('date_of_onset', $log_id);
    $onset = DateTime::createFromFormat('d/m/Y', $onset_raw);
    $onset_display = $onset ? date_i18n('M j, Y', $onset->getTimestamp()) : '—';

    // Severity
    $severity = get_field('severity', $log_id);
    $severity_display = is_array($severity) ? ($severity['label'] ?? '—') : ($severity ?: '—');

    // Body Area
    $body_area = get_field('body_area', $log_id);
    $body_area_display = is_array($body_area) ? implode(', ', $body_area) : ($body_area ?: '—');

    // Recovery Status (now first column)
    $status = get_field('recovery_status', $log_id);
    $status_value = is_array($status) ? ($status['value'] ?? '') : sanitize_title($status);
    $status_label = is_array($status) ? ($status['label'] ?? '—') : ($status ?: '—');

    // Flag styling
    $colors = [
        'cleared'     => '#3c763d', // green
        'limited'     => '#e67e22', // orange
        'modified'    => '#3498db', // blue
        'resting'     => '#c0392b', // red
        'rehab_only'  => '#9b59b6', // purple
    ];
    $dot_color = $colors[$status_value] ?? '#999';

    // Action links
    $view_link = get_permalink($log_id);
    $edit_link = site_url('/edit-injury-log/' . $log_id);

    echo '<tr>';
    echo '<td><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:' . esc_attr($dot_color) . ';margin-right:6px;"></span>' . esc_html($status_label) . '</td>';
    echo '<td>' . esc_html($onset_display) . '</td>';
    echo '<td>' . esc_html($severity_display) . '</td>';
    echo '<td>' . esc_html($body_area_display) . '</td>';
    echo '<td><a href="' . esc_url($view_link) . '">View</a> | <a href="' . esc_url($edit_link) . '">Update</a></td>';
    echo '</tr>';
}


echo '</tbody></table>';
