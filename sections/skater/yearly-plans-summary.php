<?php
// --- Yearly Training Plans ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Yearly Training Plans</h2>';
if (!$is_skater) {
    echo '<p><a class="button" href="' . esc_url(site_url('/create-yearly-plan?skater_id=' . $skater_id)) . '">Add Yearly Plan</a></p>';
}

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
function display_yearly_plan_summary($plan, $is_skater = false) {
    $view_url = get_permalink($plan->ID);
    $edit_url = site_url('/edit-yearly-plan/' . $plan->ID);

    $season = get_field('season', $plan->ID) ?: '—';
    $dates = get_field('season_dates', $plan->ID);
    $goal  = get_field('primary_season_goal', $plan->ID);
    $peak  = get_field('peak_planning', $plan->ID);
    $macro = get_field('macrocycles', $plan->ID);

    // Season date range for top section only
    $date_range = '';
    if ($dates && isset($dates['start_date'], $dates['end_date'])) {
        $start = DateTime::createFromFormat('d/m/Y', $dates['start_date'])?->format('M j, Y');
        $end   = DateTime::createFromFormat('d/m/Y', $dates['end_date'])?->format('M j, Y');
        $date_range = $start && $end ? "$start to $end" : '';
    }

    // Primary Peak
    $primary_peak = '—';
    if (!empty($peak['primary_peak_event'][0])) {
        $primary_peak = get_the_title($peak['primary_peak_event'][0]);
    }

    // Macrocycles summary with dates
    $macro_summary = '';
    if ($macro && is_array($macro)) {
        $macro_summary .= '<ul class="macrocycle-list">';
        foreach ($macro as $m) {
            $title = $m['phase_title'] ?? '';
            $focus = $m['phase_focus'] ?? '';
            $start = !empty($m['phase_start']) ? DateTime::createFromFormat('d/m/Y', $m['phase_start'])->format('M j') : '';
            $end   = !empty($m['phase_end'])   ? DateTime::createFromFormat('d/m/Y', $m['phase_end'])->format('M j') : '';
            $range = ($start || $end) ? " ($start – $end)" : '';
            if ($title || $focus) {
                $macro_summary .= '<li><strong>' . esc_html($title . $range) . ':</strong> ' . esc_html($focus) . '</li>';
            }
        }
        $macro_summary .= '</ul>';
    }

    echo '<div class="plan-card">';
    echo '<div class="plan-header">';
    //echo '<h3>' . esc_html($season) . '</h3>';
    //if ($date_range) {
    //    echo '<p class="date-range"><em>' . esc_html($date_range) . '</em></p>';
    //}
    echo '</div>';

    echo '<p><strong>Primary Peak:</strong> ' . esc_html($primary_peak) . '</p>';

    if ($goal) {
        echo '<h4>🎯 Primary Goal</h4>';
        echo '<div class="wysiwyg">' . wp_kses_post($goal) . '</div>';
    }

    if ($macro_summary) {
        echo '<h4>📘 Macrocycles</h4>';
        echo $macro_summary;
    }

    echo '<div class="plan-actions">';
    echo '<a class="button button-small" href="' . esc_url($view_url) . '">View</a>';
    if (!$is_skater) {
        echo '<a class="button button-small" href="' . esc_url($edit_url) . '" style="margin-left: 8px;">Update</a>';
    }
    echo '</div>';

    echo '</div>';
}



// --- Render blocks
if ($current_plan) {
    echo '<h3>Current Plan – ' . esc_html(get_field('season', $current_plan->ID)) . '</h3>';
    display_yearly_plan_summary($current_plan, $is_skater);
}

if ($upcoming_plan) {
    echo '<h3>Upcoming Plan – ' . esc_html(get_field('season', $upcoming_plan->ID)) . '</h3>';
    display_yearly_plan_summary($upcoming_plan, $is_skater);
}

if (!empty($other_plans)) {
    echo '<h3>Past Plans</h3>';
    foreach ($other_plans as $plan) {
        display_yearly_plan_summary($plan, $is_skater);
    }
}
?>

<style>
.macrocycle-list {
  padding-left: 1.2em;
  margin: 0;
}
.macrocycle-list li {
  margin-bottom: 4px;
  line-height: 1.4;
}
</style>
