<?php
/**
 * Template for creating a new Session Log using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_681c4231d6279'; // ✅ Session Log Details

echo '<div class="wrap coach-dashboard create-session-log">';
echo '<h1>Create New Session Log</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'session_log',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Session Log',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
