<?php
/**
 * Coach Dashboard Section: Upcoming Meetings
 * This template has been refactored for code style, UI consistency, and performance.
 */

// --- 1. PREPARE DATA ---
$visible_skater_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');
$meetings_data = [];

if (!empty($visible_skater_ids)) {
    // Build the meta query to find meetings for any of the visible skaters.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    // Fetch all upcoming meetings.
    $upcoming_meetings = new WP_Query([
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
            $skater_meta_query,
        ],
        'meta_key'   => 'meeting_date',
        'orderby'    => 'meta_value',
        'order'      => 'ASC',
    ]);

    if ($upcoming_meetings->have_posts()) {
        while ($upcoming_meetings->have_posts()) {
            $upcoming_meetings->the_post();
            $meeting_id = get_the_ID();

            $skater_post_array = get_field('skater', $meeting_id);
            $skater_names = [];
            if (!empty($skater_post_array)) {
                foreach ($skater_post_array as $skater_post) {
                    $skater_names[] = get_the_title($skater_post);
                }
            }

            $date_raw = get_field('meeting_date', $meeting_id);
            $date_obj = $date_raw ? DateTime::createFromFormat('d/m/Y', $date_raw) : null;
            
            $meeting_types = get_field('meeting_type', $meeting_id);

            $meetings_data[] = [
                'title' => get_the_title($meeting_id) ?: 'Meeting',
                'date' => $date_obj ? $date_obj->format('M j, Y') : '—',
                'skaters' => !empty($skater_names) ? implode(', ', $skater_names) : '—',
                'type' => is_array($meeting_types) ? implode(', ', $meeting_types) : ($meeting_types ?: '—'),
                'view_url' => get_permalink($meeting_id),
                'edit_url' => site_url('/edit-meeting-log/' . $meeting_id),
            ];
        }
    }
    wp_reset_postdata();
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Upcoming Meetings</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-meeting-log/')); ?>">Add Meeting</a>
</div>

<?php if (empty($meetings_data)) : ?>

    <p>No upcoming meetings found for assigned skaters.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Skater(s)</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($meetings_data as $meeting) : ?>
                <tr>
                    <td><?php echo esc_html($meeting['date']); ?></td>
                    <td><?php echo esc_html($meeting['title']); ?></td>
                    <td><?php echo esc_html($meeting['skaters']); ?></td>
                    <td><?php echo esc_html($meeting['type']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($meeting['view_url']); ?>">View</a> | 
                        <a href="<?php echo esc_url($meeting['edit_url']); ?>">Update</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
