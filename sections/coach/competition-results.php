<?php
// --- Coach Dashboard: Competition Results Summary ---

echo '<h2>Competition Results</h2>';

$today = date('Y-m-d');

// Fetch all competition results
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

if (empty($results)) {
    echo '<p>No competition results found.</p>';
    return;
}

// Group results by past competitions
$grouped = [];

foreach ($results as $result) {
    $comp = get_field('linked_competition', $result->ID);
    $comp = is_array($comp) ? ($comp[0] ?? null) : $comp;
    if (!$comp || !is_object($comp)) continue;

    $comp_date = get_field('competition_date', $comp->ID);
    if (!$comp_date || $comp_date > $today) continue; // Skip upcoming events

    $grouped[$comp->ID]['competition'] = $comp;
    $grouped[$comp->ID]['results'][] = $result;
}

// Sort by competition date
uasort($grouped, function ($a, $b) {
    $dateA = get_field('competition_date', $a['competition']->ID);
    $dateB = get_field('competition_date', $b['competition']->ID);
    return strtotime($dateA) - strtotime($dateB);
});

// Display each group
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
        $skater = get_field('skater', $result->ID);
        $skater = is_array($skater) ? ($skater[0] ?? null) : $skater;

        $placement = get_field('placement', $result->ID) ?: '—';
        $level     = get_field('level', $result->ID) ?: '—';
        $discipline = get_field('discipline', $result->ID) ?: '—';

        // Total score logic
        $total_field = get_field('total_score', $result->ID);
        $total = $total_field['total_competition_score'] ?? null;

        if (!$total) {
            // Fallback to single segment if available
            $segments = ['short_program_score', 'free_program_score', 'artistic_score'];
            foreach ($segments as $key) {
                if (!empty($total_field[$key])) {
                    $total = $total_field[$key];
                    break;
                }
            }
        }

        // Medal emoji
        $medal = ($placement == 1) ? ' 🥇' : (($placement == 2) ? ' 🥈' : (($placement == 3) ? ' 🥉' : ''));

        echo '<tr>';
        echo '<td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>';
        echo '<td>' . esc_html($level) . '</td>';
        echo '<td>' . esc_html($discipline) . '</td>';
        echo '<td>' . esc_html($placement . $medal) . '</td>';
        echo '<td>' . esc_html(is_numeric($total) ? number_format($total, 2) : '—') . '</td>';
        echo '<td><a href="' . esc_url(get_permalink($result->ID)) . '">View</a> | <a href="' . esc_url(site_url('/edit-competition-result/' . $result->ID . '/')) . '">Update</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
