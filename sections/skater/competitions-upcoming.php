<?php
// --- Skater-Specific: Upcoming Competitions ---

$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Upcoming Competitions</h2>';

if (!$skater_id) {
    echo '<p>No skater selected.</p>';
    return;
}

$today = date('Y-m-d');

// Get all competition_result posts for this skater
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]]
]);

$upcoming = [];

foreach ($results as $r) {
    $comp = get_field('linked_competition', $r->ID);
    $comp = is_array($comp) ? ($comp[0] ?? null) : $comp;

    if ($comp && is_object($comp)) {
        $date = get_field('competition_date', $comp->ID);
        if ($date && $date >= $today) {
            $upcoming[] = [
                'result'      => $r,
                'competition' => $comp,
            ];
        }
    }
}

if ($upcoming) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>Date</th><th>Level</th><th>Discipline</th><th>Location</th></tr></thead><tbody>';

    foreach ($upcoming as $entry) {
        $r = $entry['result'];
        $c = $entry['competition'];

        $name       = get_the_title($c->ID);
        $date       = get_field('competition_date', $c->ID) ?: '—';
        $level      = get_field('level', $r->ID) ?: '—';
        $discipline = get_field('discipline', $r->ID) ?: '—';
        $location   = get_field('competition_location', $c->ID) ?: '—';

        echo '<tr>';
        echo '<td>' . esc_html($name) . '</td>';
        echo '<td>' . esc_html($date) . '</td>';
        echo '<td>' . esc_html($level) . '</td>';
        echo '<td>' . esc_html($discipline) . '</td>';
        echo '<td>' . esc_html($location) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No upcoming competitions found.</p>';
}
