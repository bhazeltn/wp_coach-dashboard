<?php
/**
 * Template: View a Competition and its Results
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

the_post();
$comp_id = get_the_ID();

echo '<div class="wrap coach-dashboard">';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">&larr; Back to Dashboard</a></p>';

echo '<h1>' . esc_html(get_the_title()) . '</h1>';

$type = get_field('competition_type') ?: '—';
$start_date = get_field('competition_date') ?: '—';
$location = get_field('competition_location');

echo '<ul>';
echo '<li><strong>Type:</strong> ' . esc_html($type) . '</li>';
echo '<li><strong>Start Date:</strong> ' . esc_html($start_date) . '</li>';
if ($location) {
    echo '<li><strong>Location:</strong> ' . esc_html($location) . '</li>';
}
echo '</ul>';

// Get results linked to this competition
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        [
            'key'   => 'linked_competition',
            'value' => '"' . $comp_id . '"',
            'compare' => 'LIKE'
        ]
    ]
]);

echo '<h2>Skater Results</h2>';

if (empty($results)) {
    echo '<p>No results found.</p>';
} else {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr><th>Skater</th><th>Level</th><th>Discipline</th><th>Placement</th><th>Detail Sheet</th><th>Scores</th><th>Actions</th></tr></thead><tbody>';

    foreach ($results as $result) {
        $res_id = $result->ID;
    
        // Skater
        $skater = get_field('skater', $res_id);
        $skater_name = $skater ? get_the_title(is_array($skater) ? $skater[0] : $skater) : '—';
    
        // Level / Discipline
        $level = get_field('level', $res_id) ?: '—';
        $discipline = get_field('discipline', $res_id) ?: '—';
    
        // Placement
        $comp_score = get_field('comp_score', $res_id);
        $placement = $comp_score['placement'] ?? '—';
        $medal = ($placement == 1) ? ' 🥇' : (($placement == 2) ? ' 🥈' : (($placement == 3) ? ' 🥉' : ''));
        $placement_display = esc_html($placement . $medal);
    
        // Total Score
        $total_score = $comp_score['total_score'] ?? null;
        $score_display = is_numeric($total_score) ? number_format($total_score, 2) : '—';
    
        // Detail Sheet
        $sheets = get_field('detail_sheets', $res_id);
        $has_sheet = false;
        if ($sheets && is_array($sheets)) {
            foreach ($sheets as $sheet) {
                if (!empty($sheet['upload'])) {
                    $has_sheet = true;
                    break;
                }
            }
        }
    
        echo '<tr>';
        echo '<td>' . esc_html($skater_name) . '</td>';
        echo '<td>' . esc_html($level) . '</td>';
        echo '<td>' . esc_html($discipline) . '</td>';
        echo '<td>' . $placement_display . '</td>';
        echo '<td>' . ($has_sheet ? '✔️' : '—') . '</td>';
        echo '<td>' . $score_display . '</td>';
        echo '<td><a href="' . esc_url(get_permalink($res_id)) . '">View</a> | <a href="' . esc_url(site_url('/edit-competition-result/' . $res_id . '/')) . '">Update</a></td>';
        echo '</tr>';
    }
    

    echo '</tbody></table>';
}

echo '</div>';
get_footer();
