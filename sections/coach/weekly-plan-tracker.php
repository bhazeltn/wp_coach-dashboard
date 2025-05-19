<?php
$plans = new WP_Query([
    'post_type'      => 'weekly_plan',
    'posts_per_page' => 10,
    'meta_key'       => 'week_start',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
]);

if ($plans->have_posts()) {
    echo '<div class="dashboard-section">';
    echo '<h2>Recent Weekly Plans</h2>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Week</th><th>Skater</th><th>Theme</th><th>Actions</th></tr></thead>';
    echo '<tbody>';

    while ($plans->have_posts()) {
        $plans->the_post();

        $start_raw = get_field('week_start');
        $date_obj = DateTime::createFromFormat('d/m/Y', $start_raw);
        $formatted = $date_obj ? $date_obj->format('M j, Y') : esc_html($start_raw);

        $skaters = get_field('skater');
        $names = [];
        if ($skaters) {
            foreach ($skaters as $skater) {
                $names[] = esc_html(get_the_title($skater));
            }
        }

        $theme = get_field('theme');

        echo '<tr>';
        echo '<td>' . esc_html($formatted) . '</td>';
        echo '<td>' . implode(', ', $names) . '</td>';
        echo '<td>' . esc_html($theme) . '</td>';
        echo '<td>';
        echo '<a class="button-small" href="' . esc_url(get_permalink()) . '">View</a> | ';
        echo '<a class="button-small" href="' . esc_url(site_url('/edit-weekly-plan/' . get_the_ID())) . '">Update</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
    wp_reset_postdata();
}
