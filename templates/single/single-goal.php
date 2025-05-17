<?php
/**
 * Template: View Goal
 */

wp_enqueue_script('jquery');
acf_form_head();
get_header();

echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';
echo '<div class="wrap coach-dashboard">';

if (!have_posts()) {
    echo '<p>Goal not found.</p>';
} else {
    while (have_posts()) : the_post();

        // Fields
        $title         = get_the_title() ?: '[Untitled Goal]';
        $type          = get_field('goal_type') ?: '—';
        $timeframe     = get_field('goal_timeframe') ?: '—';

        $status_raw    = get_field('current_status');
        $status        = is_array($status_raw) ? implode(', ', $status_raw) : ($status_raw ?: '—');

        $target_raw    = get_field('target_date');
        $target        = '—';
        if ($target_raw) {
            $dt = DateTime::createFromFormat('d/m/Y', $target_raw);
            if ($dt) {
                $target = date_i18n('F j, Y', $dt->getTimestamp()); // Localized output
            }
        }

        $skater_raw    = get_field('skater');
        $skater        = is_array($skater_raw) ? ($skater_raw[0] ?? null) : $skater_raw;

        $description   = get_field('smart_description');
        $progress      = get_field('progress_notes');

        // Title with Skater prefix
        $skater_display = '';
        if ($skater && get_post_type($skater) === 'skater') {
            $skater_id    = is_object($skater) ? $skater->ID : $skater;
            $skater_name  = get_the_title($skater_id);
            $skater_slug  = get_post_field('post_name', $skater_id);
            $skater_display = $skater_name . ': ';
        }

        echo '<h1>' . esc_html($skater_display . $title) . '</h1>';

        // Summary fields
        echo '<ul>';
        echo '<li><strong>Goal Type:</strong> ' . esc_html($type) . '</li>';
        echo '<li><strong>Timeframe:</strong> ' . esc_html($timeframe) . '</li>';
        echo '<li><strong>Status:</strong> ' . esc_html($status) . '</li>';
        echo '<li><strong>Target Date:</strong> ' . esc_html($target) . '</li>';
        echo '</ul>';

        // Description and notes
        if ($description) {
            echo '<h2>SMART Description</h2>';
            echo '<div>' . wpautop(wp_kses_post($description)) . '</div>';
        }

        if ($progress) {
            echo '<h2>Progress Notes</h2>';
            echo '<div>' . wpautop(wp_kses_post($progress)) . '</div>';
        }

        if (current_user_can('edit_post', get_the_ID())) {
            $edit_url = site_url('/edit-goal?goal_id=' . get_the_ID());
            echo '<p><a class="button" href="' . esc_url($edit_url) . '">Update Goal</a></p>';
        }   


        echo '<div class="button-row">';
        if ($skater && get_post_type($skater) === 'skater') {
            $skater_id = is_object($skater) ? $skater->ID : $skater;
            $skater_slug = get_post_field('post_name', $skater_id);
            echo '<a class="button" href="' . esc_url(site_url('/skater/' . $skater_slug)) . '">&larr; Back to Skater</a> ';
        }
        echo '<a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">&larr; Back to Dashboard</a>';
        echo '</div>';
        

    endwhile;
}

echo '</div>';
get_footer();
