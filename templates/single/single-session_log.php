<?php
/**
 * Template for displaying a single Session Log
 */

get_header();

$log_id = get_the_ID();
$skater = get_field('linked_skater', $log_id);
$session_date = get_field('session_date', $log_id);
$summary = get_field('session_summary', $log_id);
$program = get_field('program_work', $log_id);
$energy = get_field('energy_check', $log_id);
$mental = get_field('mental_check', $log_id);
$notes = get_field('additional_notes', $log_id);

// Wrapper
echo '<div class="coach-dashboard session-log">';

// Back button
if ($skater) {
    echo '<p><a class="button" href="' . site_url('/skater/' . $skater->post_name) . '">← Back to Skater</a></p>';
}

// Heading
echo '<h1>Session Log – ' . esc_html($session_date) . '</h1>';

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
    echo '<h2>Energy/Stamina</h2><p>' . nl2br(esc_html($energy)) . '</p>';
}

// Mental Check
if ($mental) {
    echo '<h2>Mental Training & Well-being</h2><p>' . nl2br(esc_html($mental)) . '</p>';
}

// Additional Notes
if ($notes) {
    echo '<h2>Additional Notes</h2><p>' . nl2br(esc_html($notes)) . '</p>';
}

// End wrapper
echo '</div>';

get_footer();
