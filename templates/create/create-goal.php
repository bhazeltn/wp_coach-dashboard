<?php
/**
 * Template for creating a new Goal post using ACF form block
 */

acf_form_head();
get_header();

$skater_id = isset($_GET['skater_id']) ? intval($_GET['skater_id']) : null;

$field_group_key = 'group_681c4115a026f'; // Goal field group key

// Wrapper
echo '<div class="coach-dashboard create-goal">';
echo '<h1>Create New Goal</h1>';

acf_form([
    'post_id' => 'new_post',
    'new_post' => [
        'post_type'   => 'goal',
        'post_status' => 'publish',
    ],
    'field_groups' => [$field_group_key],
    'submit_value' => 'Create Goal',
    'html_before_fields' => $skater_id
        ? '<input type="hidden" name="acf[field_linked_skater]" value="' . esc_attr($skater_id) . '">'
        : '',
]);

echo '</div>';

get_footer();
