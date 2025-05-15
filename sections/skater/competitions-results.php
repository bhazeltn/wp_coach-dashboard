<?php
// --- Competition Results ---

$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Competition Results</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=competition_result') . '">Add Competition Result</a></p>';


$today = date('Y-m-d');
$season_year = (date('n') >= 7) ? date('Y') : date('Y') - 1;
$season_start = $season_year . '-07-01';
$season_end = ($season_year + 1) . '-06-30';

$results = get_posts([
    'post_type' => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [[
        'key' => 'linked_skater',
        'value' => '"' . $skater_id . '"',
        'compare' => 'LIKE'
    ]]
]);

$filtered = [];
foreach ($results as $result) {
    $linked = get_field('linked_competition', $result->ID);
    $competition = is_array($linked) ? $linked[0] ?? null : $linked;

    if (!$competition || !is_object($competition)) continue;

    $date = get_field('competition_date', $competition->ID);
    if ($date >= $season_start && $date <= $season_end) {
        $filtered[] = [
            'result' => $result,
            'competition' => $competition
        ];
    }
}

if ($filtered) {
    echo '<table class="widefat fixed striped">
        <thead><tr>
            <th>Competition</th><th>Date</th><th>Level</th><th>Discipline</th>
            <th>Placement</th><th>TES</th><th>PCS</th><th>Deductions</th><th>Total</th>
        </tr></thead><tbody>';

    foreach ($filtered as $entry) {
        $result = $entry['result'];
        $comp = $entry['competition'];

        $placement = get_field('placement', $result->ID);
        $level = get_field('level', $result->ID);
        $discipline = get_field('discipline', $result->ID);

        $tes = get_field('technical_element_scores', $result->ID);
        $pcs = get_field('program_component_scores', $result->ID);
        $deductions = get_field('deduction_bonus', $result->ID);
        $total = get_field('total_score', $result->ID);

        $tes_score = $tes['total'] ?? '—';
        $pcs_score = $pcs['total'] ?? '—';
        $deduction = $deductions['total'] ?? '—';
        $total_score = $total['total'] ?? '—';

        echo '<tr>
            <td>' . esc_html(get_the_title($comp->ID)) . '</td>
            <td>' . esc_html(get_field('date', $comp->ID) ?: '—') . '</td>
            <td>' . esc_html($level ?: '—') . '</td>
            <td>' . esc_html($discipline ?: '—') . '</td>
            <td>' . esc_html($placement ?: '—') . '</td>
            <td>' . esc_html($tes_score) . '</td>
            <td>' . esc_html($pcs_score) . '</td>
            <td>' . esc_html($deduction) . '</td>
            <td>' . esc_html($total_score) . '</td>
        </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No competition results found for this season.</p>';
}
