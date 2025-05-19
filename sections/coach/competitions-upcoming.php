<?php
// --- Coach Dashboard: Upcoming Competitions with Skaters ---

echo '<h2>Upcoming Competitions</h2>';

$today = date('Y-m-d');

// Step 1: Get all future competitions
$future_comps = get_posts([
    'post_type'   => 'competition',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'competition_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'competition_date',
        'value'   => $today,
        'compare' => '>=',
        'type'    => 'DATE'
    ]]
]);

// Step 2: Get all competition_result posts
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

// Step 3: Build a mapping: competition_id => [skater names]
$comp_skaters = [];

foreach ($results as $r) {
    $comp = get_field('linked_competition', $r->ID);
    $comp = is_array($comp) ? ($comp[0] ?? null) : $comp;

    $skater = get_field('linked_skater', $r->ID);
    $skater = is_array($skater) ? ($skater[0] ?? null) : $skater;

    if ($comp && $skater && is_object($comp) && is_object($skater)) {
        $comp_id = $comp->ID;
        $comp_skaters[$comp_id][] = get_the_title($skater->ID);
    }
}

// Step 4: Filter future comps to those with skaters
$filtered = array_filter($future_comps, fn($c) => isset($comp_skaters[$c->ID]));

if ($filtered) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>Date</th><th>Type</th><th>Location</th><th>Skaters</th></tr></thead><tbody>';

    foreach ($filtered as $c) {
        $name = get_the_title($c->ID);
        $date = get_field('competition_date', $c->ID) ?: '—';
        $type = get_field('competition_type', $c->ID) ?: '—';  // Field was renamed from "level" to "type"
        $loc  = get_field('competition_location', $c->ID) ?: '—';
        $skaters = $comp_skaters[$c->ID] ?? [];

        echo '<tr>';
        echo '<td>' . esc_html($name) . '</td>';
        echo '<td>' . esc_html($date) . '</td>';
        echo '<td>' . esc_html($type) . '</td>';
        echo '<td>' . esc_html($loc) . '</td>';
        echo '<td>' . esc_html(implode(', ', $skaters)) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No upcoming competitions with assigned skaters found.</p>';
}
