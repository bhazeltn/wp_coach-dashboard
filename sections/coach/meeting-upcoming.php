<?php
// --- Coach Dashboard: Upcoming Meetings ---

$today = date('Ymd');

// Get visible skaters
$visible      = spd_get_visible_skaters();
$visible_ids  = wp_list_pluck($visible, 'ID');

// If no visible skaters, skip query
if (empty($visible_ids)) {
    echo '<p>No upcoming meetings found for assigned skaters.</p>';
    return;
}

// WP_Query with meta_query for date and visible skaters
$meetings = new WP_Query([
    'post_type'      => 'meeting_log',
    'posts_per_page' => -1,
    'meta_query'     => [
        'relation' => 'AND',
        [
            'key'     => 'meeting_date',
            'compare' => '>=',
            'value'   => $today,
        ],
        [
            'relation' => 'OR',
            ...array_map(function($id) {
                return [
                    'key'     => 'skater',
                    'value'   => '"' . $id . '"',
                    'compare' => 'LIKE',
                ];
            }, $visible_ids)
        ]
    ],
    'meta_key'   => 'meeting_date',
    'orderby'    => 'meta_value',
    'order'      => 'ASC',
]);

echo '<div class="dashboard-section">';
echo '<h2>Upcoming Meetings</h2>';
if ($meetings->have_posts()) {
    echo '<ul class="dashboard-list">';
    while ($meetings->have_posts()) {
        $meetings->the_post();

        $meeting_date_raw = get_field('meeting_date');
        $date_obj = DateTime::createFromFormat('d/m/Y', $meeting_date_raw);
        $formatted_date = $date_obj ? $date_obj->format('M j, Y') : esc_html($meeting_date_raw);

        $title = get_the_title();
        if (empty($title)) {
            $title = get_field('meeting_title');
        }

        $skaters = get_field('skater');
        $skater_names = [];
        if ($skaters) {
            foreach ($skaters as $skater) {
                $skater_names[] = esc_html(get_the_title($skater));
            }
        }

        echo '<li>';
        echo '<strong>' . esc_html($title) . '</strong>';
        echo ' — ' . esc_html($formatted_date);
        if (!empty($skater_names)) {
            echo ' <em>(' . implode(', ', $skater_names) . ')</em>';
        }
        echo ' <a class="button-small" href="' . esc_url(get_permalink()) . '">View</a>';
        echo '</li>';
    }
    echo '</ul></div>';
    wp_reset_postdata();
} else {
    echo '<p>No upcoming meetings found for assigned skaters.</p>';
}
