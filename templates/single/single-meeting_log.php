<?php
/**
 * Template: View Single Meeting Log
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
$meeting_date_raw = get_field('meeting_date');

if (!empty($meeting_date_raw)) {
    $date_obj = DateTime::createFromFormat('d/m/Y', $meeting_date_raw);
    $formatted_date = $date_obj ? $date_obj->format('F j, Y') : '';
} else {
    $formatted_date = '';
}

$meeting_type = get_field('meeting_type');
if (is_array($meeting_type)) {
    $meeting_type = implode(', ', $meeting_type);
}

$participants = get_field('participants');
$summary = get_field('summary_notes');

echo '<div class="wrap coach-dashboard">';
echo '<h1>' . esc_html(get_the_title()) . '</h1>';

echo '<div class="dashboard-box">';

echo '<p><strong>Date:</strong> ' . esc_html($formatted_date) . '</p>';
echo '<p><strong>Meeting Type:</strong> ' . esc_html($meeting_type) . '</p>';

if ($skaters && is_array($skaters)) {
    echo '<p><strong>Skater(s):</strong> ';
    $links = [];
    foreach ($skaters as $skater) {
        $links[] = '<a href="' . esc_url(site_url('/skater/' . $skater->post_name)) . '">' . esc_html(get_the_title($skater)) . '</a>';
    }
    echo implode(', ', $links);
    echo '</p>';
}

if (!empty($participants)) {
    echo '<p><strong>Participants:</strong> ' . esc_html($participants) . '</p>';
}

if (!empty($summary)) {
    echo '<h3>Summary / Notes</h3>';
    echo '<div class="dashboard-notes">' . wpautop(esc_html($summary)) . '</div>';
}

echo '</div>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Back to Dashboard</a></p>';
echo '</div>';

get_footer();
