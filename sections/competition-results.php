<?php
$skater_id = $GLOBALS['skater_id'] ?? null;
echo '<p>Skater ID: ' . esc_html($skater_id ?? 'NOT SET') . '</p>';

echo '<h2>Current Season Competition Results</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=competition') . '">Add Competition</a></p>';


// Define current season range (adjust as needed)
$today = date('Y-m-d');
$year = (date('n') >= 7) ? date('Y') : date('Y') - 1; // Start in July
$season_start = $year . '-07-01';
$season_end = ($year + 1) . '-06-30';

$results = get_posts([
    'post_type' => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'linked_competition',
            'compare' => 'EXISTS' // We’ll validate actual date below
        ]
    ]
]);



if (empty($results)) {
    echo '<p>No competition results found.</p>';
    return;
}

// Filter by competition date in season range
$filtered = [];

echo '<p>Found ' . count($results) . ' competition results for this skater.</p>';


echo '<pre>';
foreach ($results as $result) {
    $competition = get_field('linked_competition', $result->ID);

    // Show debug info even if competition is missing
    $comp_title = $competition ? get_the_title($competition->ID) : 'No linked competition';
    $date = $competition ? get_field('date', $competition->ID) : 'No date';

    echo get_the_title($result->ID) . ' → ' . $comp_title . ' on ' . $date . PHP_EOL;

    if (!$competition || !is_object($competition)) continue;

    if ($date >= $season_start && $date <= $season_end) {
        $filtered[] = [
            'result' => $result,
            'competition' => $competition
        ];
    }
}
echo '</pre>';

if (empty($filtered)) {
    echo '<p>No competition results in the current season.</p>';
    return;
}

echo '<table class="widefat fixed striped">
    <thead>
        <tr>
            <th>Skater</th>
            <th>Competition</th>
            <th>Level</th>
            <th>Discipline</th>
            <th>Placement</th>
            <th>TES</th>
            <th>PCS</th>
            <th>Deductions</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

foreach ($filtered as $entry) {
    $result = $entry['result'];
    $comp = $entry['competition'];

    $skater = get_field('linked_skater', $result->ID);
    $placement = get_field('placement', $result->ID);
    $level = get_field('level', $result->ID);
    $discipline = get_field('discipline', $result->ID);

    $tes = get_field('technical_element_scores', $result->ID);
    $pcs = get_field('program_component_scores', $result->ID);
    $deductions = get_field('deduction_bonus', $result->ID);
    $totals = get_field('total_score', $result->ID);

    $tes_score = $tes['total'] ?? '—';
    $pcs_score = $pcs['total'] ?? '—';
    $deduction = $deductions['total'] ?? '—';
    $total = $totals['total'] ?? '—';

    echo '<tr>
        <td>' . esc_html($skater ? get_the_title($skater->ID) : '—') . '</td>
        <td>' . esc_html($comp ? get_the_title($comp->ID) : '—') . '</td>
        <td>' . esc_html($level ?: '—') . '</td>
        <td>' . esc_html($discipline ?: '—') . '</td>
        <td>' . esc_html($placement ?: '—') . '</td>
        <td>' . esc_html($tes_score) . '</td>
        <td>' . esc_html($pcs_score) . '</td>
        <td>' . esc_html($deduction) . '</td>
        <td>' . esc_html($total) . '</td>
    </tr>';
}

echo '</tbody></table>';
