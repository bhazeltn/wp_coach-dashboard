<?php
// Section: Skater Programs

$skater_id = $GLOBALS['skater_id'] ?? null;
if (!$skater_id) return;

// Get current yearly plan to match season
$current_ytp = get_posts([
    'post_type'   => 'yearly_plan',
    'post_status' => 'publish',
    'numberposts' => 1,
    'meta_query'  => [
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ]
    ],
    'orderby'     => 'date',
    'order'       => 'DESC',
]);

$season = $current_ytp ? get_field('season', $current_ytp[0]->ID) : null;

$programs = get_posts([
    'post_type'   => 'program',
    'post_status' => 'publish',
    'numberposts' => -1,
    'meta_query'  => [
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ],
        $season ? [
            'key'     => 'season',
            'value'   => $season,
            'compare' => '=',
        ] : [],
    ],
]);

echo '<h2>Programs</h2>';
echo '<p><a class="button" href="' . esc_url(site_url('/create-program?skater_id=' . $skater_id)) . '">Add Program</a></p>';

if (empty($programs)) {
    echo '<p>No programs created for this season.</p>';
} else {
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Title</th><th>Category</th><th>Season</th><th>Actions</th></tr></thead><tbody>';
    foreach ($programs as $program) {
        $cat = get_field('program_category', $program->ID) ?? '—';
        $season_label = get_field('season', $program->ID) ?? '—';
        echo '<tr>';
        echo '<td>' . esc_html(get_the_title($program)) . '</td>';
        echo '<td>' . esc_html($cat) . '</td>';
        echo '<td>' . esc_html($season_label) . '</td>';
        echo '<td>';
        echo '<a class="button-small" href="' . esc_url(get_permalink($program)) . '">View</a> | ';
        echo '<a class="button-small" href="' . esc_url(site_url('/edit-program/' . $program->ID)) . '">Edit</a>';
        echo '</td></tr>';
    }
    echo '</tbody></table>';
}
