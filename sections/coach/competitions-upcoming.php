<?php
// --- Coach Dashboard: Upcoming Competitions ---

echo '<h2>Upcoming Competitions</h2>';
echo '<p><a class="button" href="' . admin_url('post-new.php?post_type=competition') . '">Add Competition</a></p>';

$today = date('Y-m-d');

$competitions = get_posts([
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
        'type'    => 'DATE'
    ]]
]);

if (empty($competitions)) {
    echo '<p>No upcoming competitions found.</p>';
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead><tr>
    <th>Name</th>
    <th>Date</th>
    <th>Location</th>
    <th>Type</th>
    <th></th>
</tr></thead><tbody>';

foreach ($competitions as $comp) {
    $date     = get_field('date', $comp->ID) ?: '—';
    $location = get_field('location', $comp->ID) ?: '—';
    $type     = get_field('type', $comp->ID) ?: '—';
    $edit_url = get_edit_post_link($comp->ID);

    echo '<tr>';
    echo '<td>' . esc_html(get_the_title($comp->ID)) . '</td>';
    echo '<td>' . esc_html($date) . '</td>';
    echo '<td>' . esc_html($location) . '</td>';
    echo '<td>' . esc_html($type) . '</td>';
    echo '<td><a class="button small" href="' . esc_url($edit_url) . '">Edit</a></td>';
    echo '</tr>';
}

echo '</tbody></table>';
