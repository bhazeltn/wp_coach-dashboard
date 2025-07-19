<?php
/**
 * Coach Dashboard Section: Recent Session Logs
 * This template has been refactored for code style, UI consistency, and performance.
 */

// --- 1. PREPARE DATA ---
$visible_skater_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');
$session_logs_data = [];

if (!empty($visible_skater_ids)) {
    // Build the meta query to find logs for any of the visible skaters.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    // Fetch the 10 most recent session logs.
    $recent_sessions = new WP_Query([
        'post_type'      => 'session_log',
        'posts_per_page' => 10,
        'meta_key'       => 'session_date',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
        'meta_query'     => $skater_meta_query,
    ]);

    if ($recent_sessions->have_posts()) {
        while ($recent_sessions->have_posts()) {
            $recent_sessions->the_post();
            $log_id = get_the_ID();

            $skater_post_array = get_field('skater', $log_id);
            $skater_name = !empty($skater_post_array[0]) ? get_the_title($skater_post_array[0]) : '—';

            $date_raw = get_field('session_date', $log_id);
            $date_obj = $date_raw ? DateTime::createFromFormat('d/m/Y', $date_raw) : null;
            $formatted_date = $date_obj ? $date_obj->format('M j, Y') : '—';

            $wellbeing = get_field('wellbeing_focus_check-in', $log_id);

            $session_logs_data[] = [
                'date' => $formatted_date,
                'skater_name' => $skater_name,
                'energy' => get_field('energy_stamina', $log_id) ?: '—',
                'wellbeing' => is_array($wellbeing) ? implode(', ', $wellbeing) : ($wellbeing ?: '—'),
                'view_url' => get_permalink($log_id),
                'edit_url' => site_url('/edit-session-log/' . $log_id),
            ];
        }
    }
    wp_reset_postdata();
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Recent Session Logs</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-session-log/')); ?>">Add Session Log</a>
</div>

<?php if (empty($session_logs_data)) : ?>

    <p>No recent session logs found for assigned skaters.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Skater</th>
                <th>Energy/Stamina</th>
                <th>Wellbeing/Focus</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($session_logs_data as $log) : ?>
                <tr>
                    <td><?php echo esc_html($log['date']); ?></td>
                    <td><?php echo esc_html($log['skater_name']); ?></td>
                    <td><?php echo esc_html($log['energy']); ?></td>
                    <td><?php echo esc_html($log['wellbeing']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($log['view_url']); ?>">View</a> | 
                        <a href="<?php echo esc_url($log['edit_url']); ?>">Update</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
