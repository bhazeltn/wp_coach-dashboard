<?php
/**
 * Template: View a Competition Result
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!have_posts()) {
    echo '<p>Result not found.</p>';
    get_footer();
    return;
}

the_post();
$result_id = get_the_ID();

// Linked skater and competition
$skater = get_field('linked_skater', $result_id);
$comp   = get_field('linked_competition', $result_id);
$skater_name = is_array($skater) ? get_the_title($skater[0]) : ($skater ? get_the_title($skater) : '—');
$comp_name   = is_array($comp) ? get_the_title($comp[0]) : ($comp ? get_the_title($comp) : '—');

// ACF Groups
$tes_group = get_field('technical_element_scores', $result_id);
$pcs_group = get_field('program_component_scores', $result_id);
$ded_group = get_field('deduction_bonus', $result_id);
$total_group = get_field('total_score', $result_id);
$detail_sheets = get_field('detail_sheets', $result_id);

// Segment definitions
$segments = [
    'short'     => ['label' => 'Short', 'tes' => 'tes_sp', 'pcs' => 'pcs_sp', 'ded' => 'short_program_deductions', 'bonus' => 'short_program_bonus', 'total' => 'short_program_score'],
    'freeskate' => ['label' => 'Free Skate', 'tes' => 'tes_fs', 'pcs' => 'pcs_fp', 'ded' => 'free_program_deductions', 'bonus' => 'free_program_bonus', 'total' => 'free_program_score'],
];

echo '<div class="wrap coach-dashboard">';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">&larr; Back to Dashboard</a></p>';
echo '<h1>Competition Result</h1>';

echo '<ul>';
echo '<li><strong>Skater:</strong> ' . esc_html($skater_name) . '</li>';
echo '<li><strong>Competition:</strong> ' . esc_html($comp_name) . '</li>';
echo '<li><strong>Level:</strong> ' . esc_html(get_field('level', $result_id) ?: '—') . '</li>';
echo '<li><strong>Discipline:</strong> ' . esc_html(get_field('discipline', $result_id) ?: '—') . '</li>';
$placement = get_field('placement', $result_id);
$placement_display = '—';
if ($placement) {
    $medal = '';
    if ((int)$placement === 1) $medal = ' 🥇';
    elseif ((int)$placement === 2) $medal = ' 🥈';
    elseif ((int)$placement === 3) $medal = ' 🥉';
    $placement_display = $placement . $medal;
}
echo '<li><strong>Placement:</strong> ' . esc_html($placement_display) . '</li>';

echo '</ul>';

// Scores table
echo '<h2>Scores</h2>';
echo '<table class="widefat fixed striped">';
echo '<thead><tr><th>Segment</th><th>TES</th><th>PCS</th><th>Deductions</th><th>Bonus</th><th>Total</th></tr></thead><tbody>';

$has_scores = false;

foreach ($segments as $key => $seg) {
    $tes = isset($tes_group[$seg['tes']]) ? floatval($tes_group[$seg['tes']]) : 0;
    $pcs = isset($pcs_group[$seg['pcs']]) ? floatval($pcs_group[$seg['pcs']]) : 0;
    $ded = isset($ded_group[$seg['ded']]) ? floatval($ded_group[$seg['ded']]) : 0;
    $bonus = isset($ded_group[$seg['bonus']]) ? floatval($ded_group[$seg['bonus']]) : 0;
    $total = isset($total_group[$seg['total']]) ? floatval($total_group[$seg['total']]) : ($tes + $pcs - $ded + $bonus);

    if ($tes || $pcs || $ded || $bonus || $total) {
        $has_scores = true;
        echo '<tr>';
        echo '<td>' . esc_html($seg['label']) . '</td>';
        echo '<td>' . number_format($tes, 2) . '</td>';
        echo '<td>' . number_format($pcs, 2) . '</td>';
        echo '<td>' . number_format($ded, 2) . '</td>';
        echo '<td>' . number_format($bonus, 2) . '</td>';
        echo '<td>' . number_format($total, 2) . '</td>';
        echo '</tr>';
    }
}

if (!$has_scores) {
    echo '<tr><td colspan="6"><em>No segment scores recorded.</em></td></tr>';
}

echo '</tbody></table>';

// Detail Sheets
if ($detail_sheets) {
    echo '<h2>Detail Sheets</h2><ul>';
    foreach ($detail_sheets as $sheet) {
        $segment = $sheet['segment'] ?? '—';
        $file = $sheet['upload'];
        if ($file) {
            echo '<li><strong>' . esc_html($segment) . ':</strong> <a href="' . esc_url($file['url']) . '" target="_blank">View Sheet</a></li>';
        }
    }
    echo '</ul>';
} else {
    echo '<p><em>No detail sheets uploaded.</em></p>';
}

echo '<p><a class="button" href="' . esc_url(site_url('/edit-competition-result/' . $result_id . '/')) . '">Update Result</a></p>';
echo '</div>';

get_footer();
