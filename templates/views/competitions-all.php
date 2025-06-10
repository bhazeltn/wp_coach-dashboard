<?php
/**
 * Template: View All Competitions
 * Path: templates/views/competitions-all.php
 */

if (!is_user_logged_in()) {
    auth_redirect();
}

$current_user = wp_get_current_user();
if (!in_array('coach', (array) $current_user->roles) && !in_array('administrator', (array) $current_user->roles)) {
    wp_die('You do not have permission to view this page.');
}

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';
get_header();

echo '<div class="wrap coach-dashboard">';
echo '<h1>All Competitions</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/create-competition')) . '">Add New Competition</a></p>';

$competitions = get_posts([
    'post_type'   => 'competition',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'meta_value',
    'meta_key'    => 'competition_date',
    'order'       => 'DESC',
]);

if (empty($competitions)) {
    echo '<p>No competitions found.</p>';
    echo '</div>';
    get_footer();
    return;
}

echo '<table class="widefat fixed striped">';
echo '<thead><tr>
    <th>Name</th>
    <th>Date</th>
    <th>Type</th>
    <th>Location</th>
    <th>Actions</th>
</tr></thead><tbody>';

foreach ($competitions as $comp) {
    $comp_id = $comp->ID;
    $date = get_field('competition_date', $comp_id);
    $type = get_field('competition_type', $comp_id);
    $loc  = get_field('competition_location', $comp_id);

    echo '<tr>';
    echo '<td>' . esc_html(get_the_title($comp_id)) . '</td>';
    echo '<td>' . esc_html($date ?: '—') . '</td>';
    echo '<td>' . esc_html($type ?: '—') . '</td>';
    echo '<td>' . esc_html($loc ?: '—') . '</td>';
    echo '<td>';
    echo '<a href="' . esc_url(get_permalink($comp_id)) . '">View</a> | ';
    echo '<a href="' . esc_url(site_url('/edit-competition/' . $comp_id)) . '">Edit</a>';
    echo '</td>';
    echo '</tr>';
}

echo '</tbody></table>';
echo '</div>';

get_footer();
