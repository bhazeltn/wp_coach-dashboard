<?php
// --- Competition Results Section ---

$skater_id = $skater->ID;
$GLOBALS['skater_id'] = $skater_id;

echo '<h2>Competition Results</h2>';
if (!$is_skater) {
    echo '<p><a class="button" href="' . esc_url(site_url('/create-competition-result?skater_id=' . $skater_id)) . '">Add Competition Result</a></p>';
}


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

        $tes   = get_field('scores', $r->ID);       // renamed from 'technical_element_scores'
        $pcs   = get_field('fs_scores', $r->ID);    // renamed from 'program_component_scores'
        $sp_score = get_field('sp_score_place', $r->ID);
        $fs_score = get_field('fs_score', $r->ID);
        $comp_score = get_field('comp_score', $r->ID);

        // Placement
        $placement = $comp_score['placement'] ?? null;
        $place = $placement ?: '—';
        $medal = ($placement == 1) ? ' 🥇' : (($placement == 2) ? ' 🥈' : (($placement == 3) ? ' 🥉' : ''));

        // Segment scores
        $tes_sp = floatval($tes['tes_sp'] ?? 0);
        $tes_fs = floatval($pcs['tes_fs'] ?? 0);
        $pcs_sp = floatval($tes['pcs_sp'] ?? 0);
        $pcs_fp = floatval($pcs['pcs_fp'] ?? 0);

        // Manual total
        $total_tes = $tes_sp + $tes_fs;
        $total_pcs = $pcs_sp + $pcs_fp;
        $total_combined = $total_tes + $total_pcs;

        // Fallback to stored total_competition_score
        $stored_total = $comp_score['total_competition_score'] ?? 0;
        $show_manual_total = ($tes_sp || $tes_fs || $pcs_sp || $pcs_fp);
        $total_score_value = $show_manual_total ? $total_combined : floatval($stored_total);

        $level = get_field('level', $r->ID);
        $comp_name = esc_html(get_the_title($c->ID) . ($level ? ' – ' . $level : ''));
        $rows = [];

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
        if (!empty($tes['tes_sp']) || !empty($tes['pcs_sp'])) {
            $rows[] = [
                'segment'   => 'Short Program',
                'tes'       => number_format((float)($tes['tes_sp'] ?? 0), 2),
                'pcs'       => number_format((float)($tes['pcs_sp'] ?? 0), 2),
                'total'     => number_format((float)($sp_score['short_program_score'] ?? 0), 2),
                'placement' => '',
            ];
        }

        // Free Skate row
        if (!empty($pcs['tes_fs']) || !empty($pcs['pcs_fp'])) {
            $rows[] = [
                'segment'   => 'Free Skate',
                'tes'       => number_format((float)($pcs['tes_fs'] ?? 0), 2),
                'pcs'       => number_format((float)($pcs['pcs_fp'] ?? 0), 2),
                'total'     => number_format((float)($fs_score['free_program_score'] ?? 0), 2),
                'placement' => '',
            ];
        }

        if (empty($rows)) continue;

        foreach ($rows as $i => $row) {
            echo '<tr>';
            echo '<td>' . ($i === 0 ? $comp_name : '') . '</td>';
            echo '<td>' . $row['segment'] . '</td>';
            echo '<td>' . $row['tes'] . '</td>';
            echo '<td>' . $row['pcs'] . '</td>';
            echo '<td>' . $row['total'] . '</td>';
            echo '<td>' . $row['placement'] . '</td>';

            if ($i === 0) {
                if (!$is_skater) {
                    echo '<td><a href="' . esc_url(get_permalink($r->ID)) . '">View</a> | <a href="' . esc_url(site_url('/edit-competition-result/' . $r->ID . '/')) . '">Update</a></td>';
                }
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
