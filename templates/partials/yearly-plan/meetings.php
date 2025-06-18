<?php
// Template: yearly-plan/meetings.php

$post_id = $post_id ?? get_the_ID();
$skater = get_field('skater', $post_id)[0] ?? null;
$season_dates = get_field('season_dates', $post_id);

$season_start = DateTime::createFromFormat('d/m/Y', $season_dates['start_date'] ?? '')->format('Ymd');
$season_end   = DateTime::createFromFormat('d/m/Y', $season_dates['end_date'] ?? '')->format('Ymd');

$meeting_query = new WP_Query([
    'post_type'      => 'meeting_log',
    'posts_per_page' => -1,
    'meta_key'       => 'meeting_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => 'meeting_date',
            'value'   => [$season_start, $season_end],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ],
        [
            'key'     => 'skater',
            'value'   => '"' . $skater->ID . '"',
            'compare' => 'LIKE',
        ]
    ],
]);

$current_user = wp_get_current_user();
$is_skater = in_array('skater', (array) $current_user->roles);
?>

<hr>
<div class="dashboard-box">
    <h3>📅 Meetings This Season</h3>

    <?php if (!$is_skater): ?>
        <p><a class="button" href="<?php echo esc_url(site_url('/create-meeting-log?skater_id=' . $skater->ID)); ?>">Add Meeting</a></p>
    <?php endif; ?>

    <?php if ($meeting_query->have_posts()) : ?>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Type(s)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($meeting_query->have_posts()) :
                    $meeting_query->the_post();

                    $title = get_the_title() ?: get_field('meeting_title');
                    $meeting_date = get_field('meeting_date');
                    $date_fmt = $meeting_date
                        ? DateTime::createFromFormat('d/m/Y', $meeting_date)->format('M j, Y')
                        : '—';

                    $types_raw = get_field('meeting_type');
                    $types = is_array($types_raw) ? implode(', ', $types_raw) : ($types_raw ?: '—');
                    ?>
                    <tr>
                        <td><?php echo esc_html($date_fmt); ?></td>
                        <td><?php echo esc_html($title); ?></td>
                        <td><?php echo esc_html($types); ?></td>
                        <td>
                            <a class="button-small" href="<?php echo esc_url(get_permalink()); ?>">View</a>
                            <?php if (!$is_skater): ?>
                                | <a class="button-small" href="<?php echo esc_url(site_url('/edit-meeting-log/' . get_the_ID())); ?>">Update</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No meetings recorded during this training season.</p>
    <?php endif; ?>
</div>

<?php wp_reset_postdata(); ?>
