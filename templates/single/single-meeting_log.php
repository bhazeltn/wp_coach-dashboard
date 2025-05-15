<?php
/**
 * Template for displaying a single Meeting Log
 */

get_header();
the_post();

$meeting_id    = get_the_ID();
$date          = get_field('meeting_date', $meeting_id);
$attendees     = get_field('attendees', $meeting_id);
$summary       = get_field('summary', $meeting_id);
$actions       = get_field('action_items', $meeting_id);
$edit_link     = get_edit_post_link($meeting_id);

// Format date
$formatted_date = $date && function_exists('coach_format_date') ? coach_format_date($date) : $date;

echo '<div class="wrap coach-dashboard single-meeting-log">';

// Title & Edit
echo '<h1>' . esc_html(get_the_title()) . '</h1>';
if ($edit_link) {
    echo '<p><a class="button small" href="' . esc_url($edit_link) . '">Edit Meeting Log</a></p>';
}

// Date
echo '<p><strong>Date:</strong> ' . esc_html($formatted_date ?: '—') . '</p>';

// Attendees
if ($attendees) {
    echo '<h2>Attendees</h2><ul>';
    foreach ($attendees as $person) {
        echo '<li>' . esc_html($person) . '</li>';
    }
    echo '</ul>';
}

// Summary
if ($summary) {
    echo '<h2>Meeting Summary</h2><p>' . nl2br(esc_html($summary)) . '</p>';
}

// Action Items
if ($actions) {
    echo '<h2>Action Items</h2><ul>';
    foreach ($actions as $item) {
        echo '<li>' . esc_html($item['description'] ?? '—');
        if (!empty($item['assigned_to'])) {
            echo ' <em>(Assigned to: ' . esc_html($item['assigned_to']) . ')</em>';
        }
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p><em>No action items recorded.</em></p>';
}

echo '</div>';

get_footer();
