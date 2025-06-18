<?php
// --- Coach Dashboard: Competition Results Summary ---

echo '<h2>Competition Results</h2>';

$today = date('Y-m-d');
$visible_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');

// Fetch all competition results visible to the current coach
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => spd_meta_query_for_visible_skaters('linked_skater', $visible_ids),
]);

if (empty($results)) {
    echo '<p>No competition results found.</p>';
    return;
}

// Group results by competition
$grouped = [];

foreach ($results as $result) {
    $comp = get_field('linked_competition', $result->ID);
    $comp = is_array($comp) ? ($comp[0] ?? null) : $comp;
    if (!$comp || !is_object($comp)) continue;

    $comp_date = get_field('competition_date', $comp->ID);
    if (!$comp_date || $comp_date > $today) continue;

    $grouped[$comp->ID]['competition'] = $comp;
    $grouped[$comp->ID]['results'][] = $result;
}

// Sort competitions by date
uasort($grouped, function ($a, $b) {
    $dateA = get_field('competition_date', $a['competition']->ID);
    $dateB = get_field('competition_date', $b['competition']->ID);
    return strtotime($dateA) - strtotime($dateB);
});

// Display each competition and its results
foreach ($grouped as $comp_data) {
    $comp = $comp_data['competition'];
    $comp_id = $comp->ID;
    $comp_name = get_the_title($comp_id);
    $comp_date = get_field('competition_date', $comp_id);
    $comp_date_fmt = function_exists('coach_format_date') ? coach_format_date($comp_date) : $comp_date;

    echo '<h3>' . esc_html($comp_name) . ' – ' . esc_html($comp_date_fmt) . '</h3>';

    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>Skater</th>
            <th>Level</th>
            <th>Discipline</th>
            <th>Placement</th>
            <th>Total Score</th>
            <th>Actions</th>
        </tr></thead><tbody>';

    foreach ($comp_data['results'] as $result) {
        $skater = get_field('linked_skater', $result->ID);
        $skater = is_array($skater) ? ($skater[0] ?? null) : $skater;

        $level      = get_field('level', $result->ID) ?: '—';
        $discipline = get_field('discipline', $result->ID) ?: '—';

        // Placement and score
        $placement_display = '—';
        $medal = '';
        $total = null;

        $comp_score = get_field('comp_score', $result->ID);
        if (is_array($comp_score)) {
            $placement = $comp_score['placement'] ?? null;
            if ($placement) {
                $placement_display = $placement;
                $medal = match ((int)$placement) {
                    1 => ' 🥇',
                    2 => ' 🥈',
                    3 => ' 🥉',
                    default => '',
                };
            }

            $total = $comp_score['total_competition_score'] ?? null;
        }

        // Fallback to segment total if needed
        if (!$total) {
            $sp_score = get_field('sp_score_place', $result->ID);
            $fs_score = get_field('fs_score', $result->ID);
            if (!empty($sp_score['short_program_score'])) {
                $total = $sp_score['short_program_score'];
            } elseif (!empty($fs_score['free_program_score'])) {
                $total = $fs_score['free_program_score'];
            }
        }

        echo '<tr>';
        echo '<td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>';
        echo '<td>' . esc_html($level) . '</td>';
        echo '<td>' . esc_html($discipline) . '</td>';
        echo '<td>' . esc_html($placement_display . $medal) . '</td>';
        echo '<td>' . esc_html(is_numeric($total) ? number_format($total, 2) : '—') . '</td>';
        echo '<td><a href="' . esc_url(get_permalink($result->ID)) . '">View</a> | <a href="' . esc_url(site_url('/edit-competition-result/' . $result->ID . '/')) . '">Update</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
