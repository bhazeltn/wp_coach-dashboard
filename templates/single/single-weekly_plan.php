<?php
/**
 * Template for displaying a single Weekly Plan
 */

get_header();

$plan_id = get_the_ID();
$skater = get_field('linked_skater', $plan_id);

$start_date = get_field('week_start_date', $plan_id);
$theme = get_field('weekly_theme', $plan_id);
$notes = get_field('weekly_notes', $plan_id);
$goal_refs = get_field('related_goals', $plan_id);
$plan_summary = get_field('plan_summary', $plan_id);
$program_work = get_field('program_work', $plan_id);
$energy = get_field('energy_check', $plan_id);
$mental = get_field('mental_check', $plan_id);


// Wrapper
echo '<div class="coach-dashboard weekly-plan">';

// Back button
if ($skater) {
    echo '<p><a class="button" href="' . site_url('/skater/' . $skater->post_name) . '">← Back to Skater</a></p>';
}

// Heading
echo '<h1>Weekly Plan – ' . esc_html($start_date) . '</h1>';

// Theme
if ($theme) {
    echo '<p><strong>Theme:</strong> ' . esc_html($theme) . '</p>';
}

// Notes
if ($notes) {
    echo '<h2>Weekly Notes</h2><p>' . nl2br(esc_html($notes)) . '</p>';
}

// Related Goals
if ($goal_refs) {
    echo '<h2>Related Goals</h2><ul>';
    foreach ($goal_refs as $goal) {
        echo '<li><a href="' . get_permalink($goal->ID) . '">' . esc_html(get_the_title($goal->ID)) . '</a></li>';
    }
    echo '</ul>';
}

// Plan Summary
if ($plan_summary) {
    echo '<h2>Planned Training Summary</h2><p>' . nl2br(esc_html($plan_summary)) . '</p>';
}

// Program Work
if ($program_work) {
    echo '<h2>Program Work</h2><p>' . nl2br(esc_html($program_work)) . '</p>';
}

// Energy Check
if ($energy) {
    echo '<h2>Energy/Stamina Notes</h2><p>' . nl2br(esc_html($energy)) . '</p>';
}

// Mental Well-being
if ($mental) {
    echo '<h2>Mental Training & Well-being</h2><p>' . nl2br(esc_html($mental)) . '</p>';
}

// End wrapper
echo '</div>';

get_footer();
