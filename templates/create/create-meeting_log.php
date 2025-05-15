<?php
/**
 * Template for creating a new Meeting Log using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_6824296728a7d'; // ✅ Meeting Details

echo '<div class="wrap coach-dashboard create-meeting-log">';
echo '<h1>Create New Meeting Log</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'meeting_log',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Meeting Log',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
