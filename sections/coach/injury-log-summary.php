<?php
// --- Coach Dashboard: Injury Log Summary ---

echo '<h2>Coach Summary: Injury & Health Logs</h2>';

// Get visible skater IDs for the logged-in user
$visible      = spd_get_visible_skaters();
$visible_ids  = wp_list_pluck($visible, 'ID');

// Build meta query
$meta_clauses = array_map(function($id) {
    return [
        'key'     => 'injured_skater',
        'value'   => '"' . $id . '"',
        'compare' => 'LIKE',
    ];
}, $visible_ids);

// Fetch injury logs for visible skaters
$logs = get_posts([
    'post_type'   => 'injury_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        'relation' => 'OR',
        ...$meta_clauses,
    ],
    'meta_key'    => 'date_of_onset',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
]);

if (empty($logs)) {
    echo '<p>No injuries or health concerns logged for assigned skaters.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead><tr>
    <th>Status</th>
    <th>Skater</th>
    <th>Date of Onset</th>
    <th>Severity</th>
    <th>Body Area</th>
    <th>Actions</th>
</tr></thead><tbody>';

foreach ($logs as $log) {
    $log_id = $log->ID;

    // Skater
    $skater = get_field('injured_skater', $log_id);
    $skater_name = is_array($skater) ? get_the_title($skater[0]) : ($skater ? get_the_title($skater) : '—');

    // Dates
    $onset_raw = get_field('date_of_onset', $log_id);
    $onset = DateTime::createFromFormat('d/m/Y', $onset_raw);
    $onset_display = $onset ? date_i18n('M j, Y', $onset->getTimestamp()) : '—';

    // Severity
    $severity = get_field('severity', $log_id);
    $severity_display = is_array($severity) ? ($severity['label'] ?? '—') : ($severity ?: '—');

    // Body Area
    $body_area = get_field('body_area', $log_id);
    $body_area_display = is_array($body_area) ? implode(', ', $body_area) : ($body_area ?: '—');

    // Recovery Status
    $status = get_field('recovery_status', $log_id);
    $status_value = strtolower(is_array($status) ? ($status['value'] ?? '') : sanitize_title($status));
    $status_label = is_array($status) ? ($status['label'] ?? '—') : ($status ?: '—');

    $colors = [
        'cleared'     => '#3c763d',
        'limited'     => '#e67e22',
        'modified'    => '#3498db',
        'resting'     => '#c0392b',
        'rehab_only'  => '#9b59b6',
    ];
    $dot_color = $colors[$status_value] ?? '#999';

    // Actions
    $view_link = get_permalink($log_id);
    $edit_link = site_url('/edit-injury-log/' . $log_id);

    echo '<tr>';
    echo '<td><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:' . esc_attr($dot_color) . ';margin-right:6px;"></span>' . esc_html($status_label) . '</td>';
    echo '<td>' . esc_html($skater_name) . '</td>';
    echo '<td>' . esc_html($onset_display) . '</td>';
    echo '<td>' . esc_html($severity_display) . '</td>';
    echo '<td>' . esc_html($body_area_display) . '</td>';
    echo '<td><a href="' . esc_url($view_link) . '">View</a> | <a href="' . esc_url($edit_link) . '">Update</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';
