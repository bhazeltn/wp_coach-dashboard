<?php
/**
 * Template: View Single Weekly Plan
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

global $post;
setup_postdata($post);

// === Fields ===
$skater        = get_field('skater');
$yearly_plan   = get_field('related_yearly_plan');
$week_start    = get_field('week_start');
$theme         = get_field('theme');
$off_ice       = get_field('planned_off_ice_activities');
$sessions      = get_field('session_breakdown');

// === Format Week Start ===
$week_obj = DateTime::createFromFormat('d/m/Y', $week_start);
$formatted_week = $week_obj ? $week_obj->format('F j, Y') : esc_html($week_start);

// === Header ===
echo '<div class="wrap coach-dashboard">';
echo '<h1>Weekly Plan</h1>';

echo '<div class="dashboard-box">';
echo '<p><strong>Week Starting:</strong> ' . esc_html($formatted_week) . '</p>';

if ($skater && is_array($skater)) {
    $skater = $skater[0];
}
if ($skater) {
    echo '<p><strong>Skater:</strong> <a href="' . esc_url(site_url('/skater/' . $skater->post_name)) . '">' . esc_html(get_the_title($skater)) . '</a></p>';
}

if ($yearly_plan && is_array($yearly_plan)) {
    $yearly_plan = $yearly_plan[0];
}
if ($yearly_plan) {
    echo '<p><strong>Yearly Plan:</strong> ' . esc_html(get_the_title($yearly_plan)) . '</p>';
}

if (!empty($theme)) {
    echo '<p><strong>Theme:</strong> ' . esc_html($theme) . '</p>';
}
echo '</div>';

// === Off-Ice Activities ===
if ($off_ice && is_array($off_ice)) {
    echo '<h3>🧘 Planned Off-Ice Activities</h3>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Day</th><th>Type</th><th>Activity</th></tr></thead><tbody>';
    foreach ($off_ice as $entry) {
        $type = $entry['type'];
        if (is_array($type)) {
            $type = implode(', ', $type);
        }
        $notes = $entry['activity'] ?? '';
        echo '<tr>';
        echo '<td>' . esc_html($entry['day']) . '</td>';
        echo '<td>' . esc_html($type) . '</td>';
        echo '<td>' . nl2br(esc_html($notes)) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

// === On-Ice Session Breakdown ===
if ($sessions && is_array($sessions)) {
    echo '<h3>⛸️ Session Overview</h3>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Day</th><th>Primary Focus</th><th>Program Run-Through</th></tr></thead><tbody>';
    foreach ($sessions as $entry) {
        echo '<tr>';
        echo '<td>' . esc_html($entry['day']) . '</td>';
        echo '<td>' . esc_html($entry['primary_focus']) . '</td>';
        echo '<td>' . esc_html($entry['program_run_thru']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

// === Detailed Session Plans (nested inside each session) ===
if ($sessions && is_array($sessions)) {
    echo '<h3>📋 Detailed Session Plan</h3>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Day</th><th>Session #</th><th>Detailed Plan</th></tr></thead><tbody>';

    foreach ($sessions as $entry) {
        $day = $entry['day'] ?? '—';
        $details = $entry['detailed_session_plan'] ?? [];

        if (!empty($details) && is_array($details)) {
            foreach ($details as $row) {
                $session_number = $row['session_number'] ?? '—';
                $plan_detail = $row['detailed_plan'] ?? '—';
                echo '<tr>';
                echo '<td>' . esc_html($day) . '</td>';
                echo '<td>' . esc_html($session_number) . '</td>';
                echo '<td>' . wp_kses_post($plan_detail) . '</td>';
                echo '</tr>';
            }
        }
    }

    echo '</tbody></table>';
}

echo '<p><a class="button" href="' . esc_url(site_url('/edit-weekly-plan/' . get_the_ID())) . '">Update This Plan</a></p>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Back to Dashboard</a></p>';
echo '</div>';

get_footer();
