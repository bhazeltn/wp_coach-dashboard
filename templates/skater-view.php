<?php
/**
 * Template for Skater View Page
 */

$skater_slug = get_query_var('skater_view');
$skater = get_page_by_path($skater_slug, OBJECT, 'skater');

if (!$skater) {
    wp_die('Skater not found.');
}

$skater_id = $skater->ID;
$GLOBALS['skater_id'] = $skater_id;

$edit_link = get_edit_post_link($skater_id);
$level = get_field('current_level', $skater_id);
$federation = get_field('federation', $skater_id);
$club = get_field('home_club', $skater_id); // optional future field

echo '<div class="wrap coach-dashboard skater-view">';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">← Back to Coach Dashboard</a></p>';

// Skater name + Edit button
echo '<h1>' . esc_html(get_the_title($skater_id)) . '</h1>';
if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Skater</a></p>';
}

// Basic skater info block
echo '<div class="skater-meta">';
echo '<p><strong>Level:</strong> ' . esc_html($level ?: '—') . '</p>';
echo '<p><strong>Federation:</strong> ' . esc_html($federation ?: '—') . '</p>';
if ($club) {
    echo '<p><strong>Club:</strong> ' . esc_html($club) . '</p>';
}
echo '</div>';

// Load stylesheet (if not globally enqueued)
echo '<link rel="stylesheet" href="' . esc_url(plugin_dir_url(__DIR__) . 'css/dashboard-style.css') . '" type="text/css" media="all">';

// Section wrappers (with IDs for tab/scroll nav later)
echo '<section id="yearly-plans">';
include plugin_dir_path(__DIR__) . 'sections/skater/yearly-plans.php';
echo '</section>';

echo '<section id="weekly-plans">';
include plugin_dir_path(__DIR__) . 'sections/skater/weekly-plans.php';
echo '</section>';

echo '<section id="goals">';
include plugin_dir_path(__DIR__) . 'sections/skater/goals.php';
echo '</section>';

echo '<section id="upcoming-competitions">';
include plugin_dir_path(__DIR__) . 'sections/skater/competitions-upcoming.php';
echo '</section>';

echo '<section id="competition-results">';
include plugin_dir_path(__DIR__) . 'sections/skater/competitions-results.php';
echo '</section>';

echo '<section id="session-logs">';
include plugin_dir_path(__DIR__) . 'sections/skater/session-logs.php';
echo '</section>';

echo '<section id="missed-goals">';
include plugin_dir_path(__DIR__) . 'sections/skater/missed-goals.php';
echo '</section>';

echo '</div>';
