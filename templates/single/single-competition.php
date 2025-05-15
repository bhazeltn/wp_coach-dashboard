<?php
/**
 * Template for displaying a single Competition
 */

get_header();

$comp_id   = get_the_ID();
$title     = get_the_title($comp_id);
$date      = get_field('date', $comp_id);
$location  = get_field('location', $comp_id);
$type      = get_field('type', $comp_id);
$notes     = get_field('notes', $comp_id);
$edit_link = get_edit_post_link($comp_id);

// Format date if helper exists
$formatted_date = $date && function_exists('coach_format_date') ? coach_format_date($date) : $date;

echo '<div class="wrap coach-dashboard single-competition">';

echo '<h1>' . esc_html($title) . '</h1>';

if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Competition</a></p>';
}

echo '<table class="widefat fixed striped"><tbody>';
echo '<tr><th>Date</th><td>' . esc_html($formatted_date ?: '—') . '</td></tr>';
echo '<tr><th>Location</th><td>' . esc_html($location ?: '—') . '</td></tr>';
echo '<tr><th>Type</th><td>' . esc_html($type ?: '—') . '</td></tr>';
echo '</tbody></table>';

if ($notes) {
    echo '<h2>Notes</h2><p>' . nl2br(esc_html($notes)) . '</p>';
}

echo '</div>';

get_footer();
