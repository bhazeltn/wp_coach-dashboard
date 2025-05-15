<?php
/**
 * Template for displaying a single Competition Result
 */

get_header();

$result_id = get_the_ID();
$skater = get_field('linked_skater', $result_id);
$competition = get_field('linked_competition', $result_id);
$level = get_field('level', $result_id);
$placement = get_field('placement', $result_id);
$tes = get_field('tes_total', $result_id);
$pcs = get_field('pcs_total', $result_id);
$total = get_field('total_score', $result_id);
$segment = get_field('result_segment', $result_id);
$notes = get_field('result_notes', $result_id);

// Wrapper
echo '<div class="coach-dashboard competition-result">';

// Back button
if ($skater) {
    echo '<p><a class="button" href="' . site_url('/skater/' . $skater->post_name) . '">← Back to Skater</a></p>';
}

// Heading
echo '<h1>Competition Result</h1>';

// Competition Title
if ($competition) {
    echo '<p><strong>Competition:</strong> ' . esc_html(get_the_title($competition->ID)) . '</p>';
}

// Segment
if ($segment) {
    echo '<p><strong>Segment:</strong> ' . esc_html($segment) . '</p>';
}

// Level and Placement
if ($level || $placement) {
    echo '<p><strong>Level:</strong> ' . esc_html($level ?: '—') . ' | <strong>Placement:</strong> ' . esc_html($placement ?: '—') . '</p>';
}

// Scores
echo '<h2>Scores</h2>';
echo '<ul>';
    echo '<li><strong>TES:</strong> ' . esc_html($tes ?: '—') . '</li>';
    echo '<li><strong>PCS:</strong> ' . esc_html($pcs ?: '—') . '</li>';
    echo '<li><strong>Total Score:</strong> ' . esc_html($total ?: '—') . '</li>';
echo '</ul>';

// Notes
if ($notes) {
    echo '<h2>Notes</h2><p>' . nl2br(esc_html($notes)) . '</p>';
}

// End wrapper
echo '</div>';

get_footer();
