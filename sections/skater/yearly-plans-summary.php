<?php
// --- Yearly Training Plans ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Yearly Training Plans</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=yearly_plan') . '">Add Yearly Plan</a></p>';

// Fetch plans linked to this skater
$plans = get_posts([
    'post_type'   => 'yearly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'season_dates_start_date',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
    'meta_query'  => [[
        'key'     => 'linked_skaters',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]]
]);

$current_date = date('Y-m-d');
$current_plan = null;
$past_future_plans = [];

foreach ($plans as $plan) {
    $dates = get_field('season_dates', $plan->ID);
    if ($dates && isset($dates['start_date'], $dates['end_date'])) {
        if ($current_date >= $dates['start_date'] && $current_date <= $dates['end_date']) {
            $current_plan = $plan;
        } else {
            $past_future_plans[] = $plan;
        }
    }
}

function display_yearly_plan_details($plan) {
    $title          = get_the_title($plan->ID) ?: '[Untitled]';
    $edit_url       = get_edit_post_link($plan->ID);
    $dates          = get_field('season_dates', $plan->ID);
    $peak           = get_field('peak_planning', $plan->ID);
    $primary_goal   = get_field('primary_goal', $plan->ID) ?: '—';
    $secondary_goal = get_field('secondary_goal', $plan->ID) ?: '—';
    $macrocycles    = get_field('macrocycles', $plan->ID);

    echo '<div class="ytp-block">';
    echo '<h3>' . esc_html($title);
    if ($edit_url) {
        echo ' <a class="button small" style="margin-left: 10px;" href="' . esc_url($edit_url) . '">Edit</a>';
    }
    echo '</h3>';

    if ($dates && isset($dates['start_date'], $dates['end_date'])) {
        $start = function_exists('coach_format_date') ? coach_format_date($dates['start_date']) : $dates['start_date'];
        $end   = function_exists('coach_format_date') ? coach_format_date($dates['end_date'])   : $dates['end_date'];
        echo '<p><strong>Season Dates:</strong> ' . esc_html($start) . ' to ' . esc_html($end) . '</p>';
    }

    if ($peak && is_array($peak)) {
        echo '<p><strong>Peak Type:</strong> ' . esc_html($peak['peak_type'] ?? '—') . '</p>';
        echo '<p><strong>Primary Peak:</strong> ' . esc_html($peak['primary_peak_event'] ?? '—') . '</p>';
        echo '<p><strong>Secondary Peak:</strong> ' . esc_html($peak['secondary_peak_event'] ?? '—') . '</p>';
    }

    echo '<p><strong>Primary Goal:</strong> ' . esc_html($primary_goal) . '</p>';
    echo '<p><strong>Secondary Goal:</strong> ' . esc_html($secondary_goal) . '</p>';

    if ($macrocycles && is_array($macrocycles)) {
        echo '<p><strong>Macrocycles:</strong></p><ul>';
        foreach ($macrocycles as $m) {
            $mc_title = $m['title'] ?? '';
            $mc_focus = $m['focus'] ?? '';
            echo '<li>' . esc_html($mc_title) . ' – ' . esc_html($mc_focus) . '</li>';
        }
        echo '</ul>';
    }

    echo '</div>';
}

if ($current_plan) {
    echo '<h3>Current Plan</h3>';
    display_yearly_plan_details($current_plan);
}

if (!empty($past_future_plans)) {
    echo '<h3>Other Plans</h3>';
    foreach ($past_future_plans as $plan) {
        display_yearly_plan_details($plan);
    }
}
