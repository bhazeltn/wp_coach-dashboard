<?php
// --- Competition Highlights: PB, SB, CTES ---

$highlights = function_exists('get_skater_pb_sb_ctes') ? get_skater_pb_sb_ctes($skater_id) : null;

if (!$highlights) return;

$pb = $highlights['pb'] ?? [];
$sb = $highlights['sb'] ?? [];
$ctes = $highlights['ctes'] ?? [];

function ch_display_score_row($label, $entry) {
    if (!is_array($entry) || !isset($entry['score'])) return '';
    $score = is_numeric($entry['score']) ? number_format($entry['score'], 2) : '—';
    $comp = esc_html($entry['comp'] ?? '');
    $raw_date = $entry['date'] ?? '';
    $date = ($raw_date && strtotime($raw_date)) ? esc_html(date('M j, Y', strtotime($raw_date))) : '';
    $details = trim("{$comp}\n{$date}");
    return "<tr><td><strong>{$label}</strong></td><td>{$score}<br><small>" . nl2br(esc_html($details)) . "</small></td></tr>";
}

echo '<h2>Competition Highlights</h2>';
echo '<div class="competition-highlights-container" style="display: flex; gap: 40px; flex-wrap: wrap;">';

// --- Personal Bests ---
echo '<div class="highlight-box" style="flex: 1; min-width: 300px;">';
echo '<h3>Personal Bests</h3>';
echo '<table class="widefat fixed">';
echo ch_display_score_row('Total Score',       $pb['total_score'] ?? null);
echo ch_display_score_row('Short Program',     $pb['short_total'] ?? null);
echo ch_display_score_row('Free Skate',        $pb['free_total'] ?? null);
echo ch_display_score_row('SP TES',            $pb['sp_tes'] ?? null);
echo ch_display_score_row('SP PCS',            $pb['sp_pcs'] ?? null);
echo ch_display_score_row('FS TES',            $pb['fs_tes'] ?? null);
echo ch_display_score_row('FS PCS',            $pb['fs_pcs'] ?? null);
echo '</table>';
echo '</div>';

// --- Season Bests ---
echo '<div class="highlight-box" style="flex: 1; min-width: 300px;">';
echo '<h3>Season Bests</h3>';
echo '<table class="widefat fixed">';
echo ch_display_score_row('Total Score',       $sb['total_score'] ?? null);
echo ch_display_score_row('Short Program',     $sb['short_total'] ?? null);
echo ch_display_score_row('Free Skate',        $sb['free_total'] ?? null);
echo ch_display_score_row('SP TES',            $sb['sp_tes'] ?? null);
echo ch_display_score_row('SP PCS',            $sb['sp_pcs'] ?? null);
echo ch_display_score_row('FS TES',            $sb['fs_tes'] ?? null);
echo ch_display_score_row('FS PCS',            $sb['fs_pcs'] ?? null);
echo '</table>';
echo '</div>';

echo '</div>'; // end flex container

// --- CTES ---
if (!empty($ctes['combined']) && is_numeric($ctes['combined'])) {
    $short = $ctes['short_tes'] ?? null;
    $free  = $ctes['free_tes'] ?? null;

    echo '<div style="margin-top: 30px;">';
    echo '<h3>Combined TES (CTES)</h3>';
    echo '<table class="widefat fixed">';
    echo '<tr><td><strong>Total CTES</strong></td><td>' . number_format($ctes['combined'], 2) . '</td></tr>';

    if (is_array($short) && isset($short['score'])) {
        echo '<tr><td>SP TES</td><td>' . number_format($short['score'], 2) . '<br><small>' . esc_html($short['comp'] ?? '') . '</small></td></tr>';
    }

    if (is_array($free) && isset($free['score'])) {
        echo '<tr><td>FS TES</td><td>' . number_format($free['score'], 2) . '<br><small>' . esc_html($free['comp'] ?? '') . '</small></td></tr>';
    }

    echo '</table>';
    echo '</div>';
}
