<?php
/**
 * Template for displaying a single Competition Result
 */

get_header();

$result_id   = get_the_ID();
$skater      = get_field('linked_skater', $result_id);
$competition = get_field('linked_competition', $result_id);
$level       = get_field('level', $result_id);
$placement   = get_field('placement', $result_id);
$tes         = get_field('tes_total', $result_id);
$pcs         = get_field('pcs_total', $result_id);
$total       = get_field('total_score', $result_id);
$segment     = get_field('result_segment', $result_id);
$notes       = get_field('result_notes', $result_id);
$edit_link   = get_edit_post_link($result_id);

echo '<div class="wrap coach-dashboard single-competition-result">';

// Back to skater
if ($skater && is_object($skater)) {
    echo '<p><a class="button" href="' . esc_url(site_url('/skater/' . $skater->post_name)) . '">← Back to Skater</a></p>';
}

// Heading
echo '<h1>Competition Result</h1>';

// Optional edit link
if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Result</a></p>';
}

// Linked Competition
if ($competition && is_object($competition)) {
    $comp_title = get_the_title($competition->ID);
    $comp_date = get_field('date', $competition->ID);
    $comp_date = $comp_date && function_exists('coach_format_date') ? coach_format_date($comp_date) : $comp_date;

    echo '<p><strong>Competition:</strong> ' . esc_html($comp_title);
    if ($comp_date) {
        echo ' <em>(' . esc_html($comp_date) . ')</em>';
    }
    echo '</p>';
}

// Segment
if ($segment) {
    echo '<p><strong>Segment:</strong> ' . esc_html($segment) . '</p>';
}

// Level and Placement
echo '<p><strong>Level:</strong> ' . esc_html($level ?: '—') . ' | <strong>Placement:</strong> ' . esc_html($placement ?: '—') . '</p>';

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

echo '</div>';

get_footer();
