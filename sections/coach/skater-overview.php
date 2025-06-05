<?php
// --- Coach Dashboard: Skater Overview ---

echo '<h2>Skater Overview</h2>';
echo '<p><a class="button button-primary" href="' . esc_url(site_url('/create-skater')) . '">Add New Skater</a></p>';


$skaters = get_posts([
    'post_type'   => 'skater',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

if (empty($skaters)) {
    echo '<p>No skaters found.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead>
    <tr>
        <th>Skater</th>
        <th>Age</th>
        <th>Level</th>
        <th>Federation</th>
        <th>Current Yearly Plan</th>
    </tr>
</thead><tbody>';

$today = date('Y-m-d');

foreach ($skaters as $skater) {
    $skater_id   = $skater->ID;
    $skater_name = get_the_title($skater_id);
    $skater_slug = $skater->post_name;
    $age = get_field('age', $skater_id) ?: '—';
    $level       = get_field('current_level', $skater_id) ?: '—';
    $federation  = get_field('federation', $skater_id) ?: '—';

    $skater_view_url = site_url('/skater/' . $skater_slug);
    $edit_url = site_url('/edit-skater/' . $skater_id);

    // Find current plan (within season date range)
    $plans = get_posts([
        'post_type'   => 'yearly_plan',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query'  => [[
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ]],
        'orderby'   => 'meta_value',
        'meta_key'  => 'season_dates_start_date',
        'order'     => 'DESC',
    ]);

    $current_plan = null;
    foreach ($plans as $plan) {
        $season = get_field('season_dates', $plan->ID);
        if (is_array($season) && isset($season['start_date'], $season['end_date'])) {
            if ($today >= $season['start_date'] && $today <= $season['end_date']) {
                $current_plan = $plan;
                break;
            }
        }
    }

    $plan_link = $current_plan
        ? '<a href="' . esc_url(get_edit_post_link($current_plan->ID)) . '">' . esc_html(get_the_title($current_plan->ID)) . '</a>'
        : '—';

    echo '<tr>';
    echo '<td>';
    echo '<a href="' . esc_url($skater_view_url) . '">' . esc_html($skater_name) . '</a>';
    echo ' <a class="button small" style="margin-left: 6px;" href="' . esc_url($edit_url) . '">Edit</a>';
    echo '</td>';
    echo '<td>' . esc_html($age) . '</td>';
    echo '<td>' . esc_html($level) . '</td>';
    echo '<td>' . esc_html($federation) . '</td>';
    echo '<td>' . $plan_link . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';
