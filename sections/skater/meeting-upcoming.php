<?php
$today = date('Ymd'); // ACF date format for comparisons
$skater_id = $GLOBALS['skater_id'];

$meetings = new WP_Query([
    'post_type' => 'meeting_log',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => 'meeting_date',
            'compare' => '>=',
            'value' => $today,
        ],
        [
            'key' => 'skater',
            'value' => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ]
    ],
    'meta_key' => 'meeting_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
]);

if ($meetings->have_posts()) {
    echo '<div class="dashboard-section">';
    echo '<h2>Upcoming Meetings</h2>';
    if (!$is_skater) {
        echo '<p><a class="button" href="' . esc_url(site_url('/create-meeting-log?skater_id=' . $skater_id)) . '">Add Meeting</a></p>';
    }
    echo '<ul class="dashboard-list">';
    while ($meetings->have_posts()) {
        $meetings->the_post();
        $meeting_date_raw = get_field('meeting_date');
        $date_obj = DateTime::createFromFormat('d/m/Y', $meeting_date_raw);
        $formatted_date = $date_obj ? $date_obj->format('M j, Y') : $meeting_date_raw;

        echo '<li>';
        $title = get_the_title();
        if (empty($title)) {
            $title = get_field('meeting_title');
        }
        echo '<strong>' . esc_html($title) . '</strong>';
        echo ' — ' . esc_html($formatted_date);
        echo ' <a class="button-small" href="' . esc_url(get_permalink()) . '">View</a>';
        echo '</li>';
    }
    echo '</ul></div>';
    wp_reset_postdata();
}
