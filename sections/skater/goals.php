<?php
// --- Goals Section ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Goals</h2>';
echo '<p><a class="button" href="' . esc_url(site_url('/create-goal?skater_id=' . $skater_id)) . '">Add Goal</a></p>';

$timeframe_labels = [
    'long'   => 'Long-Term (> 1 year)',
    'season' => 'Seasonal',
    'medium' => 'Medium-Term (2–6 months)',
    'week'   => 'Weekly',
    'micro'  => 'Microcycle',
];

// Fetch all goals linked to this skater
$goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]]
]);

if ($goals) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>Goal</th>
            <th>Timeframe</th>
            <th>Status</th>
            <th>Target Date</th>
        </tr></thead><tbody>';

    foreach ($goals as $goal) {
        $title      = get_the_title($goal->ID) ?: '[Untitled]';
        $view_url   = get_permalink($goal->ID);
        $edit_url   = site_url('/edit-goal?goal_id=' . $goal->ID);

        $title_cell = esc_html($title);
        $title_cell .= ' <span style="font-size: 0.9em;">(';
        $title_cell .= '<a href="' . esc_url($view_url) . '">View</a>';
        if ($edit_url) {
            $title_cell .= ' | <a href="' . esc_url($edit_url) . '">Update</a>';
        }
        $title_cell .= ')</span>';

        $timeframe_raw = get_field('goal_timeframe', $goal->ID);
        $timeframe = $timeframe_labels[$timeframe_raw] ?? '—';


        $status_raw = get_field('current_status', $goal->ID);
        $status = is_array($status_raw) ? implode(', ', $status_raw) : ($status_raw ?: '—');

        $target_raw = get_field('target_date', $goal->ID);
        $target = '—';
        if ($target_raw) {
            $dt = DateTime::createFromFormat('d/m/Y', $target_raw);
            if ($dt) {
                $target = date_i18n('F j, Y', $dt->getTimestamp());
            }
        }

        echo '<tr>
            <td>' . $title_cell . '</td>
            <td>' . esc_html($timeframe) . '</td>
            <td>' . esc_html($status) . '</td>
            <td>' . esc_html($target) . '</td>
        </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No goals found.</p>';
}
