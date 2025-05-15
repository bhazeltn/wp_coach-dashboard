<?php
/**
 * Template for displaying a single Yearly Plan
 */

get_header();

echo '<div class="coach-dashboard yearly-plan">';
echo '<h1>' . esc_html(get_the_title()) . '</h1>';

$season = get_field('season_dates');
if ($season) {
    echo '<p><strong>Season:</strong> ' . esc_html($season['start_date']) . ' to ' . esc_html($season['end_date']) . '</p>';
}

echo '<hr><h2>Competition Schedule</h2>';
$competitions = get_field('competition_schedule');
if ($competitions) {
    echo '<ul>';
    foreach ($competitions as $comp) {
        echo '<li>' . esc_html($comp['name']) . ' – ' . esc_html($comp['date']) . ' – ' . esc_html($comp['location']) . ' (' . esc_html($comp['type']) . ')</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No competitions scheduled.</p>';
}

echo '<hr><h2>Macrocycles</h2>';
$macrocycles = get_field('macrocycles');
if ($macrocycles) {
    echo '<ul>';
    foreach ($macrocycles as $phase) {
        echo '<li><strong>' . esc_html($phase['title']) . '</strong>: ' . esc_html($phase['focus']);
        if (!empty($phase['start_date']) && !empty($phase['end_date'])) {
            echo ' (' . esc_html($phase['start_date']) . ' to ' . esc_html($phase['end_date']) . ')';
        }
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No macrocycles defined.</p>';
}

echo '<hr><h2>Weekly Themes</h2>';
$themes = get_field('weekly_themes');
if ($themes) {
    echo '<ul>';
    foreach ($themes as $week) {
        echo '<li>' . esc_html($week['week_start_date']) . ' – <strong>' . esc_html($week['theme']) . '</strong>';
        if (!empty($week['notes'])) {
            echo '<br><em>' . esc_html($week['notes']) . '</em>';
        }
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No weekly themes set.</p>';
}

echo '<hr><h2>Peak Planning</h2>';
$peak = get_field('peak_planning');
if ($peak) {
    echo '<ul>';
    echo '<li><strong>Primary Peak:</strong> ' . esc_html($peak['primary_peak_event']) . '</li>';
    echo '<li><strong>Secondary Peak:</strong> ' . esc_html($peak['secondary_peak_event']) . '</li>';
    echo '<li><strong>Peak Type:</strong> ' . esc_html($peak['peak_type']) . '</li>';
    echo '<li><strong>Peak Phase:</strong> ' . esc_html($peak['peak_phase_start']) . ' to ' . esc_html($peak['peak_phase_end']) . '</li>';
    echo '</ul>';
} else {
    echo '<p>No peak phase set.</p>';
}

echo '<hr><h2>Planned Off-Ice Activities</h2>';
$off_ice = get_field('off_ice_activities');
if ($off_ice) {
    echo '<ul>';
    foreach ($off_ice as $activity) {
        echo '<li>' . esc_html($activity['activity']) . ' – ' . esc_html($activity['frequency']) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No off-ice activities planned.</p>';
}

echo '<hr><h2>Session Structure</h2>';
$session = get_field('session_structure');
if ($session) {
    echo '<p>' . nl2br(esc_html($session)) . '</p>';
} else {
    echo '<p>No session structure defined.</p>';
}

echo '<hr><h2>Additional Notes</h2>';
$notes = get_field('notes');
echo $notes ? '<p>' . nl2br(esc_html($notes)) . '</p>' : '<p>No notes provided.</p>';

echo '</div>';

get_footer();
