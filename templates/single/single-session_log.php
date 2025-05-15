<?php
/**
 * Template for displaying a single Session Log
 */

get_header();

$log_id        = get_the_ID();
$skater        = get_field('linked_skater', $log_id);
$edit_link     = get_edit_post_link($log_id);
$session_date  = get_field('session_date', $log_id);
$summary       = get_field('session_summary', $log_id);
$program       = get_field('program_work', $log_id);
$energy        = get_field('energy_check', $log_id);
$mental        = get_field('mental_check', $log_id);
$notes         = get_field('additional_notes', $log_id);

// Format the date
$formatted_date = $session_date && function_exists('coach_format_date')
    ? coach_format_date($session_date)
    : $session_date;

echo '<div class="wrap coach-dashboard single-session-log">';

// Back button
if ($skater && is_object($skater)) {
    $skater_link = site_url('/skater/' . $skater->post_name);
    echo '<p><a class="button" href="' . esc_url($skater_link) . '">← Back to Skater</a></p>';
}

// Heading
echo '<h1>Session Log – ' . esc_html($formatted_date ?: '—') . '</h1>';

// Optional edit button
if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Log</a></p>';
}

// Summary
if ($summary) {
    echo '<h2>Session Summary</h2><p>' . nl2br(esc_html($summary)) . '</p>';
}

// Program Work
if ($program) {
    echo '<h2>Program Work</h2><p>' . nl2br(esc_html($program)) . '</p>';
}

// Energy
if ($energy) {
    echo '<h2>Energy / Stamina</h2><p>' . nl2br(esc_html($energy)) . '</p>';
}

// Mental Check
if ($mental) {
    echo '<h2>Mental Training & Well-being</h2><p>' . nl2br(esc_html($mental)) . '</p>';
}

// Additional Notes
if ($notes) {
    echo '<h2>Additional Notes</h2><p>' . nl2br(esc_html($notes)) . '</p>';
}

echo '</div>';

get_footer();
