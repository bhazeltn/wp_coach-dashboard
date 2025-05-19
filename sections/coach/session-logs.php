<?php
$sessions = new WP_Query([
    'post_type'      => 'session_log',
    'posts_per_page' => 10,
    'meta_key'       => 'session_date',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
]);

if ($sessions->have_posts()) {
    echo '<div class="dashboard-section">';
    echo '<h2>Recent Session Logs</h2>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr>';
    echo '<th>Date</th>';
    echo '<th>Skater</th>';
    echo '<th>Energy</th>';
    echo '<th>Wellbeing</th>';
    echo '<th>Actions</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    while ($sessions->have_posts()) {
        $sessions->the_post();

        // Date
        $date_raw = get_field('session_date');
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_raw);
        $formatted_date = $date_obj ? $date_obj->format('M j, Y') : esc_html($date_raw);

        // Skaters
        $skaters = get_field('skater');
        $skater_names = [];
        if ($skaters) {
            foreach ($skaters as $skater) {
                $skater_names[] = esc_html(get_the_title($skater));
            }
        }

        // Energy & Wellbeing
        $energy = get_field('energy_stamina');
        $wellbeing = get_field('wellbeing_focus_check-in');
        if (is_array($wellbeing)) {
            $wellbeing = implode(', ', $wellbeing);
        }

        echo '<tr>';
        echo '<td>' . esc_html($formatted_date) . '</td>';
        echo '<td>' . implode(', ', $skater_names) . '</td>';
        echo '<td>' . esc_html($energy) . '</td>';
        echo '<td>' . esc_html($wellbeing) . '</td>';
        echo '<td>';
        echo '<a class="button-small" href="' . esc_url(get_permalink()) . '">View</a> | ';
        echo '<a class="button-small" href="' . esc_url(site_url('/edit-session-log/' . get_the_ID())) . '">Update</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
    wp_reset_postdata();
}
