<?php
// --- Coach Dashboard: Upcoming Competitions ---

echo '<h2>Upcoming Competitions</h2>';

$today        = date('Y-m-d');
$visible      = spd_get_visible_skaters();
$visible_ids  = wp_list_pluck($visible, 'ID');

// Build meta query for competition_result posts
$meta_clauses = array_map(function($id) {
    return [
        'key'     => 'skater',
        'value'   => '"' . $id . '"', // match serialized array
        'compare' => 'LIKE',
    ];
}, $visible_ids);

$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [
        'relation' => 'OR',
        ...$meta_clauses,
    ],
]);

if (empty($results)) {
    echo '<p>No upcoming competitions found for assigned skaters.</p>';
    return;
}

// Group future competitions
$grouped = [];

foreach ($results as $result) {
    $comp = get_field('linked_competition', $result->ID);
    $comp = is_array($comp) ? ($comp[0] ?? null) : $comp;
    if (!$comp || !is_object($comp)) continue;

    $comp_date = get_field('competition_date', $comp->ID);
    if (!$comp_date || $comp_date <= $today) continue;

    $grouped[$comp->ID]['competition'] = $comp;
    $grouped[$comp->ID]['results'][]   = $result;
}

if (empty($grouped)) {
    echo '<p>No upcoming competitions found for assigned skaters.</p>';
    return;
}

// Sort competitions by date
uasort($grouped, function ($a, $b) {
    $dateA = get_field('competition_date', $a['competition']->ID);
    $dateB = get_field('competition_date', $b['competition']->ID);
    return strtotime($dateA) - strtotime($dateB);
});

// Display table
echo '<table class="widefat fixed striped">';
echo '<thead>
<tr>
    <th>Competition</th>
    <th>Date</th>
    <th>Location</th>
    <th>Skater(s)</th>
    <th>Actions</th>
</tr>
</thead><tbody>';

foreach ($grouped as $entry) {
    $comp         = $entry['competition'];
    $results      = $entry['results'];
    $comp_id      = $comp->ID;
    $comp_name    = get_the_title($comp_id);
    $comp_date    = get_field('competition_date', $comp_id);
    $comp_date_fmt = function_exists('coach_format_date') ? coach_format_date($comp_date) : $comp_date;
    $location     = get_field('competition_location', $comp_id);

    $skater_names = array_map(function ($res) {
        $skater = get_field('linked_skater', $res->ID);
        $skater = is_array($skater) ? ($skater[0] ?? null) : $skater;
        return $skater ? get_the_title($skater->ID) : '—';
    }, $results);

    echo '<tr>';
    echo '<td><a href="' . esc_url(get_permalink($comp_id)) . '">' . esc_html($comp_name) . '</a></td>';
    echo '<td>' . esc_html($comp_date_fmt) . '</td>';
    echo '<td>' . esc_html($location ?: '—') . '</td>';
    echo '<td>' . esc_html(implode(', ', $skater_names)) . '</td>';
    echo '<td><a href="' . esc_url(get_permalink($comp_id)) . '" class="button">View</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';
