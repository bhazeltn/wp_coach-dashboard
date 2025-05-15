<?php
/**
 * Template for displaying a single Injury Log
 */

get_header();
the_post();

$injury_id    = get_the_ID();
$date         = get_field('injury_date', $injury_id);
$area         = get_field('injured_area', $injury_id);
$details      = get_field('injury_details', $injury_id);
$return_date  = get_field('estimated_return', $injury_id);
$recovery     = get_field('recovery_notes', $injury_id);
$edit_link    = get_edit_post_link($injury_id);

// Format date
$injury_date_fmt = $date && function_exists('coach_format_date') ? coach_format_date($date) : $date;
$return_fmt      = $return_date && function_exists('coach_format_date') ? coach_format_date($return_date) : $return_date;

echo '<div class="wrap coach-dashboard single-injury-log">';

// Title & Edit
echo '<h1>' . esc_html(get_the_title()) . '</h1>';
if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Injury Log</a></p>';
}

// Date & Area
echo '<p><strong>Injury Date:</strong> ' . esc_html($injury_date_fmt ?: '—') . '</p>';
echo '<p><strong>Affected Area:</strong> ' . esc_html($area ?: '—') . '</p>';

// Details
if ($details) {
    echo '<h2>Injury Details</h2><p>' . nl2br(esc_html($details)) . '</p>';
}

// Estimated Return
if ($return_fmt) {
    echo '<p><strong>Estimated Return:</strong> ' . esc_html($return_fmt) . '</p>';
}

// Recovery Notes
if ($recovery) {
    echo '<h2>Recovery Plan / Notes</h2><p>' . nl2br(esc_html($recovery)) . '</p>';
}

echo '</div>';

get_footer();
