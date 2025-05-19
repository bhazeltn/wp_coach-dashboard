<?php
/**
 * Template: View Single Session Log
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

global $post;
setup_postdata($post);

// Fields
$skaters = get_field('skater');
$skater = is_array($skaters) && count($skaters) > 0 ? $skaters[0] : null;
$weekly_plan = get_field('related_weekly_plan');
$date_raw = get_field('session_date');
$date_obj = DateTime::createFromFormat('d/m/Y', $date_raw);
$formatted_date = $date_obj ? $date_obj->format('F j, Y') : $date_raw;

$energy = get_field('energy__stamina');
$wellbeing = get_field('wellbeing__focus_check-in');
if (is_array($wellbeing)) {
    $wellbeing = implode(', ', $wellbeing);
}
$mental_notes = get_field('wellbeing__mental_focus_notes');
$coach_notes = get_field('coach_notes');

// Start layout
echo '<div class="wrap coach-dashboard">';
echo '<h1>Session Log</h1>';

echo '<div class="dashboard-box">';
echo '<p><strong>Date:</strong> ' . esc_html($formatted_date) . '</p>';

if ($skater) {
    echo '<p><strong>Skater:</strong> <a href="' . esc_url(site_url('/skater/' . $skater->post_name)) . '">' . esc_html(get_the_title($skater)) . '</a></p>';
}

if ($weekly_plan) {
    echo '<p><strong>Weekly Plan:</strong> ' . esc_html(get_the_title($weekly_plan)) . '</p>';
}
echo '</div>';

function display_focus_block($label, $field_name) {
    $entries = get_field($field_name);
    if ($entries && is_array($entries)) {
        echo '<div class="dashboard-box">';
        echo '<h3>' . esc_html($label) . '</h3>';
        echo '<ul>';
        foreach ($entries as $entry) {
            echo '<li>';
            echo '<strong>' . esc_html($entry['element'] ?? $entry['program'] ?? '(unnamed)') . '</strong>';
            if (!empty($entry['outcome_notes'] ?? $entry['feedback'])) {
                echo '<br><em>' . esc_html($entry['outcome_notes'] ?? $entry['feedback']) . '</em>';
            }
            if (!empty($entry['consistency'] ?? $entry['type_of_work'])) {
                echo '<br>Consistency/Type: ' . esc_html($entry['consistency'] ?? $entry['type_of_work']);
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}

display_focus_block('Jump Focus', 'jump_focus');
display_focus_block('Spin Focus', 'spin_focus');
display_focus_block('Program Work', 'program_work');

echo '<div class="dashboard-box">';
if (!empty($energy)) {
    echo '<p><strong>Energy / Stamina:</strong> ' . esc_html($energy) . '</p>';
}
if (!empty($wellbeing)) {
    echo '<p><strong>Wellbeing Check-In:</strong> ' . esc_html($wellbeing) . '</p>';
}
if (!empty($mental_notes)) {
    echo '<p><strong>Mental Focus Notes:</strong><br>' . nl2br(esc_html($mental_notes)) . '</p>';
}
if (!empty($coach_notes)) {
    echo '<p><strong>Coach Notes:</strong><br>' . nl2br(esc_html($coach_notes)) . '</p>';
}
echo '</div>';

echo '<p><a class="button" href="' . esc_url(site_url('/edit-session-log/' . get_the_ID())) . '">Update This Log</a></p>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Back to Dashboard</a></p>';
echo '</div>';

get_footer();
