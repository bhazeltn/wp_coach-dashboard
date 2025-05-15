<?php
/**
 * Template for creating a new Competition using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_681c237c3ab7d'; // ✅ Competition Details

echo '<div class="wrap coach-dashboard create-competition">';
echo '<h1>Create New Competition</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'competition',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Competition',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
