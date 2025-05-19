<?php
// --- Competition Results Section ---

$skater_id = $skater->ID;
$GLOBALS['skater_id'] = $skater_id;

echo '<h2>Competition Results</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=competition_result') . '">Add Competition Result</a></p>';

// Define current ISU season
$season_year = (date('n') >= 7) ? date('Y') : date('Y') - 1;
$season_start = $season_year . '-07-01';
$season_end   = ($season_year + 1) . '-06-30';

$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]],
]);

$filtered = [];

foreach ($results as $result) {
    $competition = get_field('linked_competition', $result->ID);
    $comp_obj = is_array($competition) ? ($competition[0] ?? null) : $competition;
    if (!$comp_obj || !is_object($comp_obj)) continue;

    $comp_date = get_field('competition_date', $comp_obj->ID);
    if ($comp_date >= $season_start && $comp_date <= $season_end) {
        $filtered[] = [
            'result' => $result,
            'competition' => $comp_obj
        ];
    }
}

if ($filtered) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
        <th>Competition</th>
        <th>Segment</th>
        <th>TES</th>
        <th>PCS</th>
        <th>Total</th>
        <th>Placement</th>
        <th>Actions</th>
    </tr></thead><tbody>';

    foreach ($filtered as $entry) {
        $r = $entry['result'];
        $c = $entry['competition'];
    
        $tes   = get_field('technical_element_scores', $r->ID);
        $pcs   = get_field('program_component_scores', $r->ID);
        $total = get_field('total_score', $r->ID);
        $place = get_field('placement', $r->ID) ?: '—';
        $medal = ($place == 1) ? ' 🥇' : (($place == 2) ? ' 🥈' : (($place == 3) ? ' 🥉' : ''));
        $comp_name = esc_html(get_the_title($c->ID));
    
        $rows = [];
    
        // Calculate segment values
$tes_sp = isset($tes['tes_sp']) ? floatval($tes['tes_sp']) : 0;
$tes_fs = isset($tes['tes_fs']) ? floatval($tes['tes_fs']) : 0;
$pcs_sp = isset($pcs['pcs_sp']) ? floatval($pcs['pcs_sp']) : 0;
$pcs_fp = isset($pcs['pcs_fp']) ? floatval($pcs['pcs_fp']) : 0;

// Compute totals
$total_tes = $tes_sp + $tes_fs;
$total_pcs = $pcs_sp + $pcs_fp;
$total_combined = $total_tes + $total_pcs;

// Use manual total if segments exist, otherwise use ACF stored total
$show_manual_total = ($tes_sp || $tes_fs || $pcs_sp || $pcs_fp);
$total_score_value = $show_manual_total ? $total_combined : floatval($total['total_competition_score'] ?? 0);

// Add Total Row if any component exists
if ($total_score_value > 0) {
    $rows[] = [
        'segment'   => '<strong>Total</strong>',
        'tes'       => number_format($total_tes, 2),
        'pcs'       => number_format($total_pcs, 2),
        'total'     => number_format($total_score_value, 2),
        'placement' => esc_html($place . $medal),
    ];
}
    
        // Short Program row
        if (!empty($tes['tes_sp']) || !empty($pcs['pcs_sp'])) {
            $rows[] = [
                'segment'   => 'Short Program',
                'tes'       => number_format((float)($tes['tes_sp'] ?? 0), 2),
                'pcs'       => number_format((float)($pcs['pcs_sp'] ?? 0), 2),
                'total'     => number_format((float)($total['short_program_score'] ?? 0), 2),
                'placement' => '',
            ];
        }
    
        // Free Skate row
        if (!empty($tes['tes_fs']) || !empty($pcs['pcs_fp'])) {
            $rows[] = [
                'segment'   => 'Free Skate',
                'tes'       => number_format((float)($tes['tes_fs'] ?? 0), 2),
                'pcs'       => number_format((float)($pcs['pcs_fp'] ?? 0), 2),
                'total'     => number_format((float)($total['free_program_score'] ?? 0), 2),
                'placement' => '',
            ];
        }
    
        if (empty($rows)) continue; // Skip if no rows to show
    
        foreach ($rows as $i => $row) {
            echo '<tr>';
            echo '<td>' . ($i === 0 ? $comp_name : '') . '</td>';
            echo '<td>' . $row['segment'] . '</td>';
            echo '<td>' . $row['tes'] . '</td>';
            echo '<td>' . $row['pcs'] . '</td>';
            echo '<td>' . $row['total'] . '</td>';
            echo '<td>' . $row['placement'] . '</td>';
    
            if ($i === 0) {
                echo '<td><a href="' . esc_url(get_permalink($r->ID)) . '">View</a> | <a href="' . esc_url(site_url('/edit-competition-result/' . $r->ID . '/')) . '">Update</a></td>';
            } else {
                echo '<td></td>';
            }
    
            echo '</tr>';
        }
    }

    echo '</tbody></table>';
} else {
    echo '<p>No competition results found for this season.</p>';
}
