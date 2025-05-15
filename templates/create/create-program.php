<?php
/**
 * Template for creating a new Program using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_682426973af85'; // ✅ Program Details

echo '<div class="wrap coach-dashboard create-program">';
echo '<h1>Create New Program</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'program',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Program',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
