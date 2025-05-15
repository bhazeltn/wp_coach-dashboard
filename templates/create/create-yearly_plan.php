<?php
/**
 * Template for creating a new Yearly Training Plan using ACF form block
 */

acf_form_head();
get_header();

$field_group_key = 'group_681991c0d3817'; // ✅ Yearly Plan Details

echo '<div class="wrap coach-dashboard create-yearly-plan">';
echo '<h1>Create New Yearly Training Plan</h1>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Cancel</a></p>';

acf_form([
    'post_id'         => 'new_post',
    'new_post'        => [
        'post_type'   => 'yearly_plan',
        'post_status' => 'publish',
    ],
    'field_groups'    => [$field_group_key],
    'submit_value'    => 'Create Yearly Plan',
    'uploader'        => 'wp',
]);

echo '</div>';

get_footer();
