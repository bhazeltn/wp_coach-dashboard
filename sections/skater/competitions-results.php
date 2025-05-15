<?php
// --- Competition Results Section ---

$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Competition Results</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=competition_result') . '">Add Competition Result</a></p>';

// Define current ISU season
$season_year = (date('n') >= 7) ? date('Y') : date('Y') - 1;
$season_start = $season_year . '-07-01';
$season_end   = ($season_year + 1) . '-06-30';

// Get all results linked to this skater
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'linked_skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]],
]);

$filtered = [];

foreach ($results as $result) {
    $linked_comp = get_field('linked_competition', $result->ID);
    $competition = is_array($linked_comp) ? ($linked_comp[0] ?? null) : $linked_comp;

    if (!$competition || !is_object($competition)) continue;

    $comp_date = get_field('competition_date', $competition->ID);
    if (!$comp_date) continue;

    if ($comp_date >= $season_start && $comp_date <= $season_end) {
        $filtered[] = [
            'result'      => $result,
            'competition' => $competition,
        ];
    }
}

if ($filtered) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>Competition</th>
            <th>Date</th>
            <th>Level</th>
            <th>Discipline</th>
            <th>Placement</th>
            <th>TES</th>
            <th>PCS</th>
            <th>Deductions</th>
            <th>Total</th>
        </tr></thead><tbody>';

    foreach ($filtered as $entry) {
        $r = $entry['result'];
        $c = $entry['competition'];

        $placement  = get_field('placement', $r->ID) ?: '—';
        $level      = get_field('level', $r->ID) ?: '—';
        $discipline = get_field('discipline', $r->ID) ?: '—';

        $tes        = get_field('technical_element_scores', $r->ID);
        $pcs        = get_field('program_component_scores', $r->ID);
        $deductions = get_field('deduction_bonus', $r->ID);
        $total      = get_field('total_score', $r->ID);

        echo '<tr>';
        echo '<td>' . esc_html(get_the_title($c->ID)) . '</td>';
        echo '<td>' . esc_html(get_field('competition_date', $c->ID) ?: '—') . '</td>';
        echo '<td>' . esc_html($level) . '</td>';
        echo '<td>' . esc_html($discipline) . '</td>';
        echo '<td>' . esc_html($placement) . '</td>';
        echo '<td>' . esc_html($tes['total'] ?? '—') . '</td>';
        echo '<td>' . esc_html($pcs['total'] ?? '—') . '</td>';
        echo '<td>' . esc_html($deductions['total'] ?? '—') . '</td>';
        echo '<td>' . esc_html($total['total'] ?? '—') . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No competition results found for this season.</p>';
}
