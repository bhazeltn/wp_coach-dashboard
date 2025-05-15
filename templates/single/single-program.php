<?php
/**
 * Template for displaying a single Program
 */

get_header();
the_post();

$program_id    = get_the_ID();
$skater_id     = get_field('skater');
$season        = get_field('season');
$discipline    = get_field('discipline');
$level         = get_field('level');
$type          = get_field('program_type');
$edit_link     = get_edit_post_link($program_id);

// Music fields
$title         = get_field('music_title');
$composer      = get_field('composer');
$performer     = get_field('performer');
$music_file    = get_field('music'); // New ACF file field

// Choreo
$choreographer = get_field('choreographer');
$start_date    = get_field('start_date');
$start_date_fmt = $start_date && function_exists('coach_format_date') ? coach_format_date($start_date) : $start_date;

// Layout & Notes
$layout        = get_field('layout_notes');
$content       = get_field('planned_content');
$notes         = get_field('notes');

// Skater info
$skater_link = $skater_id ? site_url('/skater/' . get_post_field('post_name', $skater_id)) : null;
$skater_name = $skater_id ? get_the_title($skater_id) : '—';

echo '<div class="wrap coach-dashboard single-program">';

// Back to skater
if ($skater_link) {
    echo '<p><a class="button" href="' . esc_url($skater_link) . '">← Back to Skater</a></p>';
}

// Heading
echo '<h1>' . esc_html(get_the_title()) . '</h1>';
if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Program</a></p>';
}

// Overview
echo '<p><strong>Skater:</strong> ' . esc_html($skater_name) . '</p>';
echo '<p><strong>Season:</strong> ' . esc_html($season ?: '—') . '</p>';
echo '<p><strong>Discipline:</strong> ' . esc_html($discipline ?: '—') . ' | <strong>Level:</strong> ' . esc_html($level ?: '—') . '</p>';
echo '<p><strong>Program Type:</strong> ' . esc_html($type ?: '—') . '</p>';

// Music
echo '<hr><h2>Music</h2>';
echo '<ul>';
echo '<li><strong>Title:</strong> ' . esc_html($title ?: '—') . '</li>';
echo '<li><strong>Composer:</strong> ' . esc_html($composer ?: '—') . '</li>';
echo '<li><strong>Performer:</strong> ' . esc_html($performer ?: '—') . '</li>';
if ($music_file && is_array($music_file)) {
    echo '<li><strong>File:</strong> <a href="' . esc_url($music_file['url']) . '" target="_blank" rel="noopener">Download / Play</a></li>';
} else {
    echo '<li><strong>File:</strong> —</li>';
}
echo '</ul>';

// Choreography
echo '<hr><h2>Choreography</h2>';
echo '<ul>';
echo '<li><strong>Choreographer:</strong> ' . esc_html($choreographer ?: '—') . '</li>';
echo '<li><strong>Start Date:</strong> ' . esc_html($start_date_fmt ?: '—') . '</li>';
echo '</ul>';

// Layout & Content
if ($layout) {
    echo '<h3>Layout Notes</h3><p>' . nl2br(esc_html($layout)) . '</p>';
}
if ($content) {
    echo '<h3>Planned Content</h3><p>' . nl2br(esc_html($content)) . '</p>';
}

// Notes
if ($notes) {
    echo '<hr><h2>Notes</h2><p>' . nl2br(esc_html($notes)) . '</p>';
}

echo '</div>';

get_footer();
