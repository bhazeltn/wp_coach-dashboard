<?php
// --- Upcoming Competitions ---
echo '<h2>Upcoming Competitions</h2>';

$upcoming = get_posts([
    'post_type' => 'competition',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key' => 'date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => [[
        'key' => 'date',
        'value' => date('Y-m-d'),
        'compare' => '>=',
        'type' => 'DATE'
    ]]
]);

// Get all competition results for this skater to find linked competitions
$skater_results = get_posts([
    'post_type' => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query' => [[
        'key' => 'linked_skater',
        'value' => $skater_id,
        'compare' => '='
    ]]
]);

$linked_ids = [];
foreach ($skater_results as $result) {
    $linked_comp = get_field('linked_competition', $result->ID);
    if ($linked_comp) {
        $linked_ids[] = is_object($linked_comp) ? $linked_comp->ID : (int) $linked_comp;
    }
}

$filtered = array_filter($upcoming, function ($comp) use ($linked_ids) {
    return in_array($comp->ID, $linked_ids);
});

if ($filtered) {
    echo '<ul>';
    foreach ($filtered as $comp) {
        $date = get_field('date', $comp->ID);
        $location = get_field('location', $comp->ID);
        echo '<li>' . esc_html(get_the_title($comp->ID)) .
            ' – ' . esc_html($date ?: '—') .
            ($location ? ' @ ' . esc_html($location) : '') . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No upcoming competitions found.</p>';
}

