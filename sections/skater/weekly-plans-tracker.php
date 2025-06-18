<?php
$skater_id = $GLOBALS['skater_id'];

$plans = new WP_Query([
    'post_type'      => 'weekly_plan',
    'posts_per_page' => 5,
    'meta_key'       => 'week_start',
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

echo '<div class="dashboard-section">';
echo '<h2>Weekly Plans</h2>';
if (!$is_skater) {
    echo '<p><a class="button" href="' . esc_url(site_url('/create-weekly-plan/?skater_id=' . $skater_id)) . '">+ Add Weekly Plan</a></p>';
}

echo '<table class="dashboard-table">';
echo '<thead><tr><th>Week Starting</th><th>Theme</th><th>Actions</th></tr></thead>';
echo '<tbody>';

if ($plans->have_posts()) {
    while ($plans->have_posts()) {
        $plans->the_post();

        $start_raw = get_field('week_start');
        $date_obj = DateTime::createFromFormat('d/m/Y', $start_raw);
        $formatted = $date_obj ? $date_obj->format('M j, Y') : esc_html($start_raw);

        $theme = get_field('theme');

        echo '<tr>';
        echo '<td>' . esc_html($formatted) . '</td>';
        echo '<td>' . esc_html($theme) . '</td>';
        echo '<td>';
        echo '<a class="button-small" href="' . esc_url(get_permalink()) . '">View</a>';
        if (!$is_skater) {
            echo '<a class="button-small" href="' . esc_url(site_url('/edit-weekly-plan/' . get_the_ID())) . '"> | Update</a>';
        }
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="3"><em>No weekly plans found.</em></td></tr>';
}

echo '</tbody></table>';
echo '</div>';

wp_reset_postdata();
