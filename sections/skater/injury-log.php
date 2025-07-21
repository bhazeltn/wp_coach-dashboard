<?php
/**
 * Skater Dashboard Section: Injury & Health Log
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$injury_logs_data = [];

// Fetch all injury logs for the current skater, sorted by most recent.
$injury_logs = get_posts([
    'post_type'   => 'injury_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'injured_skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]],
    'meta_key'    => 'date_of_onset',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
]);

// Color mapping for the status dots.
$status_colors = [
    'Cleared'  => '#3c763d', // green
    'Limited'  => '#e67e22', // orange
    'Modified' => '#3498db', // blue
    'Resting'  => '#c0392b', // red
    'Rehab'    => '#9b59b6', // purple
    'Resolved' => '#777777', // grey
    'default'  => '#999',
];

foreach ($injury_logs as $log) {
    $log_id = $log->ID;

    $onset_raw = get_field('date_of_onset', $log_id);
    $onset_obj = $onset_raw ? DateTime::createFromFormat('d/m/Y', $onset_raw) : null;
    
    $status = get_field('recovery_status', $log_id);
    $status_value = is_array($status) ? ($status['value'] ?? '') : $status;
    $status_label = is_array($status) ? ($status['label'] ?? '—') : ($status ?: '—');
    
    $severity = get_field('severity', $log_id);
    $severity_display = is_array($severity) ? ($severity['label'] ?? '—') : ($severity ?: '—');
    
    $body_area = get_field('body_area', $log_id);
    $body_area_display = is_array($body_area) ? implode(', ', $body_area) : ($body_area ?: '—');

    $injury_logs_data[] = [
        'onset_date'   => $onset_obj ? $onset_obj->format('M j, Y') : '—',
        'severity'     => $severity_display,
        'body_area'    => $body_area_display,
        'status_label' => $status_label,
        'dot_color'    => $status_colors[$status_value] ?? $status_colors['default'],
        'view_url'     => get_permalink($log_id),
        'edit_url'     => site_url('/edit-injury-log/' . $log_id),
    ];
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Injury & Health Log</h2>
    <?php if (!$is_skater) : // Skaters cannot add their own injury logs ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-injury-log/?skater_id=' . $skater_id)); ?>">Add Injury Log</a>
    <?php endif; ?>
</div>

<?php if (empty($injury_logs_data)) : ?>

    <p>No injuries or health concerns have been recorded for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Date of Onset</th>
                <th>Severity</th>
                <th>Body Area</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($injury_logs_data as $log) : ?>
                <tr>
                    <td>
                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:<?php echo esc_attr($log['dot_color']); ?>; margin-right:8px;"></span>
                        <?php echo esc_html($log['status_label']); ?>
                    </td>
                    <td><?php echo esc_html($log['onset_date']); ?></td>
                    <td><?php echo esc_html($log['severity']); ?></td>
                    <td><?php echo esc_html($log['body_area']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($log['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : // Skaters cannot update logs ?>
                            | <a href="<?php echo esc_url($log['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
