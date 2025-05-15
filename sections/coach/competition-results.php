<?php
// --- Coach Dashboard: Competition Results for Current Season ---

echo '<h2>Current Season Competition Results</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=competition') . '">Add Competition</a></p>';

// Define ISU season range
$today         = date('Y-m-d');
$season_year   = (date('n') >= 7) ? date('Y') : date('Y') - 1;
$season_start  = $season_year . '-07-01';
$season_end    = ($season_year + 1) . '-06-30';

// Get all competition results (filter manually by linked_competition date)
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'linked_competition',
        'compare' => 'EXISTS',
    ]]
]);

if (empty($results)) {
    echo '<p>No competition results found.</p>';
    return;
}

$filtered = [];

foreach ($results as $result) {
    $competition = get_field('linked_competition', $result->ID);
    $date        = $competition ? get_field('date', $competition->ID) : null;

    if ($competition && is_object($competition) && $date >= $season_start && $date <= $season_end) {
        $filtered[] = [
            'result'      => $result,
            'competition' => $competition,
        ];
    }
}

if (empty($filtered)) {
    echo '<p>No competition results in the current season.</p>';
    return;
}

// Output table
echo '<table class="widefat fixed striped">';
echo '<thead><tr>
        <th>Skater</th>
        <th>Competition</th>
        <th>Level</th>
        <th>Discipline</th>
        <th>Placement</th>
        <th>TES</th>
        <th>PCS</th>
        <th>Deductions</th>
        <th>Total</th>
    </tr></thead><tbody>';

foreach ($filtered as $entry) {
    $result = $entry['result'];
    $comp   = $entry['competition'];

    $skater     = get_field('linked_skater', $result->ID);
    $placement  = get_field('placement', $result->ID) ?: '—';
    $level      = get_field('level', $result->ID) ?: '—';
    $discipline = get_field('discipline', $result->ID) ?: '—';

    $tes        = get_field('technical_element_scores', $result->ID);
    $pcs        = get_field('program_component_scores', $result->ID);
    $deductions = get_field('deduction_bonus', $result->ID);
    $totals     = get_field('total_score', $result->ID);

    echo '<tr>';
    echo '<td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>';
    echo '<td>' . esc_html(get_the_title($comp->ID)) . '</td>';
    echo '<td>' . esc_html($level) . '</td>';
    echo '<td>' . esc_html($discipline) . '</td>';
    echo '<td>' . esc_html($placement) . '</td>';
    echo '<td>' . esc_html($tes['total'] ?? '—') . '</td>';
    echo '<td>' . esc_html($pcs['total'] ?? '—') . '</td>';
    echo '<td>' . esc_html($deductions['total'] ?? '—') . '</td>';
    echo '<td>' . esc_html($totals['total'] ?? '—') . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';
