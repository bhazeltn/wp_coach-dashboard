<?php
// --- Coach Dashboard: Yearly Training Plans Summary ---

echo '<div class="dashboard-section">';
echo '<h2>Yearly Training Plans</h2>';

$today = date('Ymd');
$near_future = date('Ymd', strtotime('+30 days'));

$skaters = get_posts([
    'post_type'   => 'skater',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC',
]);

if (!$skaters) {
    echo '<p>No skaters found.</p>';
    echo '</div>';
    return;
}

foreach ($skaters as $skater) {
    $skater_id = $skater->ID;
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

    $current = null;
    $upcoming = null;

    foreach ($plans as $plan) {
        $dates = get_field('season_dates', $plan->ID);
        if ($dates && isset($dates['start_date'], $dates['end_date'])) {
            $start = DateTime::createFromFormat('d/m/Y', $dates['start_date'])?->format('Ymd');
            $end   = DateTime::createFromFormat('d/m/Y', $dates['end_date'])?->format('Ymd');

            if ($start && $end) {
                if ($today >= $start && $today <= $end) {
                    $current = $plan;
                } elseif ($start <= $near_future && $start > $today) {
                    $upcoming = $plan;
                }
            }
        }
    }

    if (!$current && !$upcoming) continue;

    echo '<h3>' . esc_html(get_the_title($skater)) . '</h3>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Season</th><th>Peak Type</th><th>Primary Peak</th><th>Goal</th><th>Actions</th></tr></thead><tbody>';

    foreach ([$current, $upcoming] as $plan) {
        if (!$plan) continue;

        $dates = get_field('season_dates', $plan->ID);
        $peak  = get_field('peak_planning', $plan->ID);
        $goal  = get_field('primary_season_goal', $plan->ID);
        $view  = get_permalink($plan->ID);
        $edit  = site_url('/edit-yearly-plan/' . $plan->ID);

        $start_fmt = $dates['start_date'] ? DateTime::createFromFormat('d/m/Y', $dates['start_date'])?->format('M j, Y') : '—';
        $end_fmt   = $dates['end_date']   ? DateTime::createFromFormat('d/m/Y', $dates['end_date'])?->format('M j, Y') : '—';
        $season    = $start_fmt . ' – ' . $end_fmt;

        $peak_type = $peak['peak_type'] ?? '—';
        $primary_peak = isset($peak['primary_peak_event'][0]) ? get_the_title($peak['primary_peak_event'][0]) : '—';
        $goal_summary = $goal ? wp_trim_words(strip_tags($goal), 20, '...') : '—';

        echo '<tr>';
        echo '<td>' . esc_html($season) . '</td>';
        echo '<td>' . esc_html($peak_type) . '</td>';
        echo '<td>' . esc_html($primary_peak) . '</td>';
        echo '<td>' . esc_html($goal_summary) . '</td>';
        echo '<td><a class="button-small" href="' . esc_url($view) . '">View</a> | ';
        echo '<a class="button-small" href="' . esc_url($edit) . '">Edit</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}

echo '</div>';
