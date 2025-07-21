<?php
/**
 * Skater Dashboard Section: Recent Session Logs
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$logs_data = [];

// Fetch the 10 most recent session logs for this skater.
$sessions_query = new WP_Query([
    'post_type'      => 'session_log',
    'posts_per_page' => 10,
    'meta_key'       => 'session_date',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => [
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ],
    ],
]);

if ($sessions_query->have_posts()) {
    while ($sessions_query->have_posts()) {
        $sessions_query->the_post();
        $log_id = get_the_ID();

        $date_raw = get_field('session_date', $log_id);
        $date_obj = $date_raw ? DateTime::createFromFormat('d/m/Y', $date_raw) : null;
        
        $wellbeing = get_field('wellbeing_focus_check-in', $log_id);

        $logs_data[] = [
            'date'       => $date_obj ? $date_obj->format('M j, Y') : '—',
            'energy'     => get_field('energy_stamina', $log_id) ?: '—',
            'wellbeing'  => is_array($wellbeing) ? implode(', ', $wellbeing) : ($wellbeing ?: '—'),
            'view_url'   => get_permalink($log_id),
            'edit_url'   => site_url('/edit-session-log/' . $log_id),
        ];
    }
}
wp_reset_postdata();

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Recent Session Logs</h2>
    <?php if (!$is_skater) : ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-session-log/?skater_id=' . $skater_id)); ?>">Add Session Log</a>
    <?php endif; ?>
</div>

<?php if (empty($logs_data)) : ?>

    <p>No session logs have been recorded for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Energy/Stamina</th>
                <th>Wellbeing/Focus</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs_data as $log) : ?>
                <tr>
                    <td><?php echo esc_html($log['date']); ?></td>
                    <td><?php echo esc_html($log['energy']); ?></td>
                    <td><?php echo esc_html($log['wellbeing']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($log['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($log['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
