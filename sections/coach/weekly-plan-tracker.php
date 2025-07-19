<?php
/**
 * Coach Dashboard Section: Recent Weekly Plans Tracker
 * This template has been refactored to group plans by week for better readability.
 */

// --- 1. PREPARE DATA ---
$visible_skater_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');
$plans_by_week = [];

if (!empty($visible_skater_ids)) {
    // Build the meta query to find plans for any of the visible skaters.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    // Fetch the 10 most recent weekly plans.
    $recent_plans = new WP_Query([
        'post_type'      => 'weekly_plan',
        'posts_per_page' => 10,
        'meta_key'       => 'week_start',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
        'meta_query'     => $skater_meta_query,
    ]);

    if ($recent_plans->have_posts()) {
        while ($recent_plans->have_posts()) {
            $recent_plans->the_post();
            $plan_id = get_the_ID();

            $skater_post_array = get_field('skater', $plan_id);
            $skater_name = !empty($skater_post_array[0]) ? get_the_title($skater_post_array[0]) : '—';

            $start_raw = get_field('week_start', $plan_id);
            $date_obj = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw) : null;
            $formatted_date = $date_obj ? $date_obj->format('M j, Y') : '—';

            // Group the plans by the formatted date string.
            if (!isset($plans_by_week[$formatted_date])) {
                $plans_by_week[$formatted_date] = [];
            }

            $plans_by_week[$formatted_date][] = [
                'skater_name' => $skater_name,
                'theme' => get_field('theme', $plan_id) ?: '—',
                'view_url' => get_permalink($plan_id),
                'edit_url' => site_url('/edit-weekly-plan/' . $plan_id),
            ];
        }
    }
    wp_reset_postdata();
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Recent Weekly Plans</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-weekly-plan/')); ?>">Add Weekly Plan</a>
</div>

<?php if (empty($plans_by_week)) : ?>

    <p>No recent weekly plans found for assigned skaters.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Week Starting</th>
                <th>Skater</th>
                <th>Theme</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans_by_week as $week_start => $plans) : ?>
                <?php foreach ($plans as $index => $plan) : ?>
                    <tr>
                        <?php if ($index === 0) : // Show the date only on the first row of its group ?>
                            <td rowspan="<?php echo count($plans); ?>">
                                <strong><?php echo esc_html($week_start); ?></strong>
                            </td>
                        <?php endif; ?>
                        <td><?php echo esc_html($plan['skater_name']); ?></td>
                        <td><?php echo esc_html($plan['theme']); ?></td>
                        <td>
                            <a href="<?php echo esc_url($plan['view_url']); ?>">View</a> | 
                            <a href="<?php echo esc_url($plan['edit_url']); ?>">Update</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
