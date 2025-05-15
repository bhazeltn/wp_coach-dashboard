<?php
/**
 * Template for creating a new Skater post using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_6819871fd44c9'; // ✅ Skater Profile

echo '<div class="wrap coach-dashboard create-skater">';
echo '<h1>Create New Skater</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'skater',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Skater',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
