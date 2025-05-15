<?php
/**
 * Redirect after ACF form submission for custom post types.
 * Applies only to front-end forms, not admin edits.
 */

add_action('acf/save_post', 'coach_dashboard_acf_post_redirects', 20);

function coach_dashboard_acf_post_redirects($post_id) {
    // Exit early if in admin
    if (is_admin()) return;

    // Determine post type
    $post_type = get_post_type($post_id);

    // Supported CPTs for redirection
    $redirect_types = array(
        'program',
        'goal',
        'meeting_log',
        'injury_log',
        'weekly_plan',
        'yearly_plan',
        'session_log',
        'competition',
        'competition_result'
    );

    // If supported, redirect to its permalink
    if (in_array($post_type, $redirect_types)) {
        wp_safe_redirect(get_permalink($post_id));
        exit;
    }
}
