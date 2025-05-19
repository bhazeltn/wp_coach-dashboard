<?php
$today = date('Ymd');

$meetings = new WP_Query([
    'post_type' => 'meeting_log',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => 'meeting_date',
            'compare' => '>=',
            'value' => $today,
        ]
    ],
    'meta_key' => 'meeting_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
]);

if ($meetings->have_posts()) {
    echo '<div class="dashboard-section">';
    echo '<h2>Upcoming Meetings</h2>';
    echo '<ul class="dashboard-list">';
    while ($meetings->have_posts()) {
        $meetings->the_post();

        $meeting_date_raw = get_field('meeting_date');
        $date_obj = DateTime::createFromFormat('d/m/Y', $meeting_date_raw);
        $formatted_date = $date_obj ? $date_obj->format('M j, Y') : esc_html($meeting_date_raw);

        // Use post title or fall back to ACF meeting_title
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
}
