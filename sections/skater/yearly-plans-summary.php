<?php
// --- Yearly Training Plans ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Yearly Training Plans</h2>';
echo '<p><a class="button" href="' . esc_url(site_url('/create-yearly-plan?skater_id=' . $skater_id)) . '">Add Yearly Plan</a></p>';

if (!$skater_id) {
    echo '<p>No skater context available.</p>';
    return;
}

// Fetch all YTPs linked to this skater
$plans = get_posts([
    'post_type'   => 'yearly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'season_dates_start_date',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]],
]);

$current_date = date('Ymd');
$current_plan = null;
$upcoming_plan = null;
$other_plans = [];

foreach ($plans as $plan) {
    $dates = get_field('season_dates', $plan->ID);
    if ($dates && isset($dates['start_date'], $dates['end_date'])) {
        $start = DateTime::createFromFormat('d/m/Y', $dates['start_date'])?->format('Ymd');
        $end   = DateTime::createFromFormat('d/m/Y', $dates['end_date'])?->format('Ymd');

        if ($start && $end) {
            if ($current_date >= $start && $current_date <= $end) {
                $current_plan = $plan;
            } elseif ($start <= date('Ymd', strtotime('+30 days')) && $start > $current_date) {
                $upcoming_plan = $plan;
            } else {
                $other_plans[] = $plan;
            }
        }
    }
}

// --- Output helper
function display_yearly_plan_summary($plan) {
    $view_url = get_permalink($plan->ID);
    $edit_url = site_url('/edit-yearly-plan/' . $plan->ID);
    $dates = get_field('season_dates', $plan->ID);
    $goal  = get_field('primary_season_goal', $plan->ID);
    $peak  = get_field('peak_planning', $plan->ID);
    $macro = get_field('macrocycles', $plan->ID);

    // Format season dates
    $season_range = '—';
    if ($dates && isset($dates['start_date'], $dates['end_date'])) {
        $start = DateTime::createFromFormat('d/m/Y', $dates['start_date'])?->format('M j, Y');
        $end   = DateTime::createFromFormat('d/m/Y', $dates['end_date'])?->format('M j, Y');
        $season_range = esc_html($start . ' to ' . $end);
    }

    // Primary Peak
    $primary_peak = '—';
    if (!empty($peak['primary_peak_event'][0])) {
        $primary_peak = get_the_title($peak['primary_peak_event'][0]);
    }

    // Goal summary
    $goal_summary = $goal ? wp_trim_words(strip_tags($goal), 25, '...') : '—';

    // Macrocycles summary
    $macro_summary = '';
    if ($macro && is_array($macro)) {
        $chunks = [];
        foreach ($macro as $m) {
            $title = $m['phase_title'] ?? '';
            $focus = $m['phase_focus'] ?? '';
            if ($title || $focus) {
                $chunks[] = esc_html($title . ' – ' . $focus);
            }
        }
        $macro_summary = implode('; ', $chunks);
    }

    // Output horizontal row
    echo '<div class="dashboard-box yearly-summary">';
    echo '<table class="dashboard-table yearly-summary-table">';
    echo '<thead><tr><th>Season</th><th>Primary Peak</th><th>Goal</th><th>Macrocycles</th><th>Actions</th></tr></thead>';
    echo '<tbody><tr>';
    echo '<td>' . $season_range . '</td>';
    echo '<td>' . esc_html($primary_peak) . '</td>';
    echo '<td>' . esc_html($goal_summary) . '</td>';
    echo '<td>' . $macro_summary . '</td>';
    echo '<td><a class="button-small" href="' . esc_url($view_url) . '">View</a> | ';
    echo '<a class="button-small" href="' . esc_url($edit_url) . '">Update</a></td>';
    echo '</tr></tbody></table>';
    echo '</div>';
}




// --- Render blocks
if ($current_plan) {
    echo '<h3>Current Plan</h3>';
    display_yearly_plan_summary($current_plan);
}

if ($upcoming_plan) {
    echo '<h3>Upcoming Plan</h3>';
    display_yearly_plan_summary($upcoming_plan);
}

if (!empty($other_plans)) {
    echo '<h3>Past Plans</h3>';
    foreach ($other_plans as $plan) {
        display_yearly_plan_summary($plan);
    }
}
