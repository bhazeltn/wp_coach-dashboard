<?php
/**
 * Skater Dashboard Section: Upcoming Meetings
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$meetings_data = [];

// Fetch all upcoming meetings for this skater.
$meetings_query = new WP_Query([
    'post_type'      => 'meeting_log',
    'posts_per_page' => -1,
    'meta_query'     => [
        'relation' => 'AND',
        [
            'key'     => 'meeting_date',
            'compare' => '>=',
            'value'   => date('Ymd'), // Use Ymd for comparison
            'type'    => 'DATE',
        ],
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ]
    ],
    'meta_key'   => 'meeting_date',
    'orderby'    => 'meta_value',
    'order'      => 'ASC',
]);

if ($meetings_query->have_posts()) {
    while ($meetings_query->have_posts()) {
        $meetings_query->the_post();
        $meeting_id = get_the_ID();

        $date_raw = get_field('meeting_date', $meeting_id);
        $date_obj = $date_raw ? DateTime::createFromFormat('d/m/Y', $date_raw) : null;
        
        $meeting_types = get_field('meeting_type', $meeting_id);

        $meetings_data[] = [
            'title'    => get_the_title($meeting_id) ?: 'Meeting',
            'date'     => $date_obj ? $date_obj->format('M j, Y') : '—',
            'type'     => is_array($meeting_types) ? implode(', ', $meeting_types) : ($meeting_types ?: '—'),
            'view_url' => get_permalink($meeting_id),
            'edit_url' => site_url('/edit-meeting-log/' . $meeting_id),
        ];
    }
}
wp_reset_postdata();

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Upcoming Meetings</h2>
    <?php if (!$is_skater) : ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-meeting-log/?skater_id=' . $skater_id)); ?>">Add Meeting</a>
    <?php endif; ?>
</div>

<?php if (empty($meetings_data)) : ?>

    <p>No upcoming meetings have been scheduled for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($meetings_data as $meeting) : ?>
                <tr>
                    <td><?php echo esc_html($meeting['date']); ?></td>
                    <td><?php echo esc_html($meeting['title']); ?></td>
                    <td><?php echo esc_html($meeting['type']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($meeting['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($meeting['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
