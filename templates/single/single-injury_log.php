<?php
/**
 * Single Injury Log View Template
 */

if (!is_user_logged_in()) {
    auth_redirect();
}

get_header();

// Load dashboard styles
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

echo '<div class="wrap coach-dashboard">';

// Fetch injury log post
$injury_id = get_the_ID();
$skater_raw = get_field('injured_skater', $injury_id);
$skater = is_array($skater_raw) ? ($skater_raw[0] ?? null) : $skater_raw;

// Links
$skater_slug = $skater ? $skater->post_name : null;
$back_link = $skater_slug ? site_url('/skater/' . $skater_slug . '/') : site_url('/coach-dashboard');
$edit_link = site_url('/edit-injury-log/' . $injury_id);

// Header & Navigation
echo '<p><a class="button" href="' . esc_url($back_link) . '">&larr; Back to ' . ($skater ? esc_html($skater->post_title) : 'Coach Dashboard') . '</a></p>';
echo '<h1>Injury Log</h1>';

// Skater and date
echo '<p><strong>Skater:</strong> ' . ($skater ? esc_html(get_the_title($skater)) : '[Unknown]') . '</p>';

$onset_raw = get_field('date_of_onset', $injury_id);
$onset = DateTime::createFromFormat('d/m/Y', $onset_raw);
echo '<p><strong>Date of Onset:</strong> ' . ($onset ? esc_html(date_i18n('F j, Y', $onset->getTimestamp())) : '—') . '</p>';

// Fields
$severity = get_field('severity', $injury_id);
echo '<p><strong>Severity:</strong> ' . esc_html($severity['label'] ?? '—') . '</p>';

$areas = get_field('body_area', $injury_id);
if ($areas && is_array($areas)) {
    echo '<p><strong>Body Area:</strong> ' . esc_html(implode(', ', $areas)) . '</p>';
} else {
    echo '<p><strong>Body Area:</strong> —</p>';
}

$type = get_field('injury_type', $injury_id);
echo '<p><strong>Description:</strong> ' . esc_html($type ?: '—') . '</p>';

$status = get_field('recovery_status', $injury_id);
echo '<p><strong>Recovery Status:</strong> ' . esc_html($status['label'] ?? '—') . '</p>';


$notes = get_field('recovery_notes', $injury_id);
if ($notes) {
    echo '<h2>Recovery Notes</h2>';
    echo wp_kses_post(wpautop($notes));
}
$is_skater = in_array('skater', (array) $current_user->roles);
if (!$is_skater){
    echo '<p><a class="button" href="' . esc_url($edit_link) . '">Update Injury Log</a></p>';
}
echo '</div>';

get_footer();
