<?php
/**
 * Template for creating a new Injury Log using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_68242bb05b02a'; // ✅ Injury Details

echo '<div class="wrap coach-dashboard create-injury-log">';
echo '<h1>Create New Injury Log</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'injury_log',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Injury Log',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
