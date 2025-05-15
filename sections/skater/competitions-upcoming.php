<?php
// --- Upcoming Competitions ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Upcoming Competitions</h2>';

// Step 1: Get upcoming competitions (today or later)
$today = date('Y-m-d');

$upcoming = get_posts([
    'post_type'   => 'competition',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'date',
        'value'   => $today,
        'compare' => '>=',
        'type'    => 'DATE',
    ]]
]);

// Step 2: Get all competition_result entries for this skater
$skater_results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'linked_skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]]
]);

// Step 3: Extract all linked competition IDs from results
$linked_ids = [];

foreach ($skater_results as $result) {
    $linked = get_field('linked_competition', $result->ID);
    $comp   = is_array($linked) ? ($linked[0] ?? null) : $linked;

    if ($comp && is_object($comp)) {
        $linked_ids[] = $comp->ID;
    }
}

// Step 4: Filter to competitions the skater is linked to
$filtered = array_filter($upcoming, fn($comp) => in_array($comp->ID, $linked_ids));

if ($filtered) {
    echo '<ul>';
    foreach ($filtered as $comp) {
        $date     = get_field('date', $comp->ID) ?: '—';
        $location = get_field('location', $comp->ID);
        echo '<li>' . esc_html(get_the_title($comp->ID)) .
            ' – ' . esc_html($date) .
            ($location ? ' @ ' . esc_html($location) : '') . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No upcoming competitions found.</p>';
}
