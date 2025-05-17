<?php
/**
 * Coach Dashboard Section Loader
 * Loads modular dashboard section files for multi-skater (coach/global) view.
 */

// Generic loader for coach section files
function coach_dashboard_section($section) {
    $fn = 'coach_section_' . $section;
    if (function_exists($fn)) {
        call_user_func($fn);
    } else {
        echo '<h2>' . ucfirst(str_replace('_', ' ', $section)) . '</h2><p>Section not implemented yet.</p>';
    }
}

// === Coach Dashboard Section Renderers ===

function coach_section_skater_overview() {
    include plugin_dir_path(__FILE__) . '../sections/coach/skater-overview.php';
}

function coach_section_yearly_plan_summary() {
    include plugin_dir_path(__FILE__) . '../sections/coach/yearly-plan-summary.php';
}

function coach_section_weekly_plan_tracker() {
    include plugin_dir_path(__FILE__) . '../sections/coach/weekly-plan-tracker.php';
}

function coach_section_goal_tracker() {
    include plugin_dir_path(__FILE__) . '../sections/coach/goal-tracker.php';
}

function coach_section_competition_results() {
    include plugin_dir_path(__FILE__) . '../sections/coach/competition-results.php';
}

function coach_section_competitions_upcoming() {
    include plugin_dir_path(__FILE__) . '../sections/coach/competitions-upcoming.php';
}

function coach_section_missed_goals() {
    include plugin_dir_path(__FILE__) . '../sections/coach/missed-goals.php';
}

function coach_section_session_logs() {
    include plugin_dir_path(__FILE__) . '../sections/coach/session-logs.php';
}

function coach_section_injury_log_summary() {
    include plugin_dir_path(__FILE__) . '../sections/coach/injury-log-summary.php';
}
