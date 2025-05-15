<?php
echo '<h2>Skater Overview</h2>';
echo '<p><a class="button button-primary" href="' . admin_url('post-new.php?post_type=skater') . '">Add New Skater</a></p>';

$skaters = get_posts([
    'post_type' => 'skater',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

if (empty($skaters)) {
    echo '<p>No skaters found.</p>';
    return;
}

echo '<table class="widefat fixed striped">
    <thead>
        <tr>
            <th>Skater</th>
            <th>Level</th>
            <th>Federation</th>
            <th>Current Yearly Plan</th>
        </tr>
    </thead>
    <tbody>';

foreach ($skaters as $skater) {
    $skater_link = site_url('/skater/' . $skater->post_name);
    $level = get_field('current_level', $skater->ID);
    $federation = get_field('federation', $skater->ID);

    $plans = get_posts([
        'post_type' => 'yearly_plan',
        'numberposts' => 1,
        'meta_query' => [[
            'key' => 'linked_skaters',
            'value' => '"' . $skater->ID . '"',
            'compare' => 'LIKE'
        ]],
        'orderby' => 'date',
        'order' => 'DESC'
    ]);

    $plan_link = count($plans) > 0
        ? '<a href="' . get_edit_post_link($plans[0]->ID) . '">' . get_the_title($plans[0]->ID) . '</a>'
        : '—';

    echo '<tr>
        <td><a href="' . esc_url($skater_link) . '">' . esc_html(get_the_title($skater->ID)) . '</a></td>
        <td>' . esc_html($level ?: '—') . '</td>
        <td>' . esc_html($federation ?: '—') . '</td>
        <td>' . $plan_link . '</td>
    </tr>';
}

echo '</tbody></table>';
