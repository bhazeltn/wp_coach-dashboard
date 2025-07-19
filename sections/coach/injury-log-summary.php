<?php
/**
 * Coach Dashboard Section: Current Injury & Health Log Summary
 * This template has been refactored to show all non-resolved injuries.
 */

// --- 1. PREPARE DATA ---
$visible_skater_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');
$logs_data = [];

if (!empty($visible_skater_ids)) {
    // Build the meta query to find logs for any of the visible skaters.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'injured_skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    // Fetch ALL injury logs that are NOT marked as 'Resolved'.
    $injury_logs = get_posts([
        'post_type'   => 'injury_log',
        'numberposts' => -1, // Show all current injuries
        'post_status' => 'publish',
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => 'recovery_status',
                'value'   => 'Resolved',
                'compare' => '!=', // Exclude injuries that are fully resolved
            ],
            $skater_meta_query, // Filter by visible skaters
        ],
        'meta_key'    => 'date_of_onset',
        'orderby'     => 'meta_value',
        'order'       => 'DESC',
    ]);

    // Color mapping for the status dots.
    $status_colors = [
        'Cleared'     => '#3c763d', // green
        'Limited'     => '#e67e22', // orange
        'Modified'    => '#3498db', // blue
        'Resting'     => '#c0392b', // red
        'Rehab'       => '#9b59b6', // purple
        'Resolved'    => '#777777', // grey for resolved (won't be shown)
        'default'     => '#999',    // default grey
    ];

    foreach ($injury_logs as $log) {
        $log_id = $log->ID;

        $skater_post = get_field('injured_skater', $log_id);
        $skater_name = is_array($skater_post) ? get_the_title($skater_post[0]) : ($skater_post ? get_the_title($skater_post) : '—');

        $onset_raw = get_field('date_of_onset', $log_id);
        $onset_obj = $onset_raw ? DateTime::createFromFormat('d/m/Y', $onset_raw) : null;
        $onset_display = $onset_obj ? $onset_obj->format('M j, Y') : '—';

        $status = get_field('recovery_status', $log_id);
        $status_value = is_array($status) ? ($status['value'] ?? '') : sanitize_title($status);
        $status_label = is_array($status) ? ($status['label'] ?? '—') : ($status ?: '—');
        
        $severity = get_field('severity', $log_id);
        $severity_display = is_array($severity) ? ($severity['label'] ?? '—') : ($severity ?: '—');
        
        $body_area = get_field('body_area', $log_id);
        $body_area_display = is_array($body_area) ? implode(', ', $body_area) : ($body_area ?: '—');

        $logs_data[] = [
            'skater_name' => $skater_name,
            'onset_date' => $onset_display,
            'severity' => $severity_display,
            'body_area' => $body_area_display,
            'status_label' => $status_label,
            'dot_color' => $status_colors[$status_value] ?? $status_colors['default'],
            'view_url' => get_permalink($log_id),
            'edit_url' => site_url('/edit-injury-log/' . $log_id),
        ];
    }
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Current Injury & Health Log</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-injury-log/')); ?>">Add Injury Log</a>
</div>

<?php if (empty($logs_data)) : ?>

    <p>No current (non-resolved) injuries or health concerns have been logged for assigned skaters.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Skater</th>
                <th>Date of Onset</th>
                <th>Severity</th>
                <th>Body Area</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs_data as $log) : ?>
                <tr>
                    <td>
                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:<?php echo esc_attr($log['dot_color']); ?>; margin-right:8px;"></span>
                        <?php echo esc_html($log['status_label']); ?>
                    </td>
                    <td><?php echo esc_html($log['skater_name']); ?></td>
                    <td><?php echo esc_html($log['onset_date']); ?></td>
                    <td><?php echo esc_html($log['severity']); ?></td>
                    <td><?php echo esc_html($log['body_area']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($log['view_url']); ?>">View</a> | 
                        <a href="<?php echo esc_url($log['edit_url']); ?>">Update</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
