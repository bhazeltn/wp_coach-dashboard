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
$skater = get_field('skater', $result_id);
$comp   = get_field('linked_competition', $result_id);
$skater_name = is_array($skater) ? get_the_title($skater[0]) : ($skater ? get_the_title($skater) : '—');
$comp_name   = is_array($comp) ? get_the_title($comp[0]) : ($comp ? get_the_title($comp) : '—');

// Score groups
$tes_group     = get_field('scores', $result_id);
$pcs_group     = get_field('fs_scores', $result_id);
$ded_group     = get_field('sp_deduction_bonus', $result_id);
$fs_ded_group  = get_field('fs_deduction_bonus', $result_id);
$total_group   = get_field('sp_score_place', $result_id);
$fs_total_group= get_field('fs_score', $result_id);
$comp_score    = get_field('comp_score', $result_id);
$detail_sheets = get_field('detail_sheets', $result_id);

// Segment definitions
$segments = [
    'short' => [
        'label' => 'Short',
        'tes'   => $tes_group['tes_sp'] ?? null,
        'pcs'   => $tes_group['pcs_sp'] ?? null,
        'ded'   => $ded_group['short_program_deductions'] ?? null,
        'bonus' => $ded_group['short_program_bonus'] ?? null,
        'total' => $total_group['short_program_score'] ?? null,
    ],
    'freeskate' => [
        'label' => 'Free Skate',
        'tes'   => $pcs_group['tes_fs'] ?? null,
        'pcs'   => $pcs_group['pcs_fp'] ?? null,
        'ded'   => $fs_ded_group['free_program_deductions'] ?? null,
        'bonus' => $fs_ded_group['free_program_bonus'] ?? null,
        'total' => $fs_total_group['free_program_score'] ?? null,
    ],
];

echo '<div class="wrap coach-dashboard">';

// Navigation
$skater_obj = is_array($skater) ? $skater[0] ?? null : $skater;
if ($skater_obj && is_object($skater_obj)) {
    $skater_slug = $skater_obj->post_name;
    echo '<p>';
    echo '<a class="button" href="' . esc_url(site_url('/skater/' . $skater_slug)) . '">← Back to Skater</a> ';
    echo '<a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">← Back to Dashboard</a>';
    echo '</p>';
} else {
    echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">← Back to Dashboard</a></p>';
}

echo '<h1>Competition Result</h1>';

echo '<ul>';
echo '<li><strong>Skater:</strong> ' . esc_html($skater_name) . '</li>';
echo '<li><strong>Competition:</strong> ' . esc_html($comp_name) . '</li>';
echo '<li><strong>Level:</strong> ' . esc_html(get_field('level', $result_id) ?: '—') . '</li>';
echo '<li><strong>Discipline:</strong> ' . esc_html(get_field('discipline', $result_id) ?: '—') . '</li>';

$placement = $comp_score['placement'] ?? null;
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
foreach ($segments as $seg) {
    $tes   = floatval($seg['tes'] ?? 0);
    $pcs   = floatval($seg['pcs'] ?? 0);
    $ded   = floatval($seg['ded'] ?? 0);
    $bonus = floatval($seg['bonus'] ?? 0);
    $total = isset($seg['total']) ? floatval($seg['total']) : ($tes + $pcs - $ded + $bonus);

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
