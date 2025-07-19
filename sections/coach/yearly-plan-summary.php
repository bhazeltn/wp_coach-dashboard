<?php
/**
 * Coach Dashboard Section: Yearly Training Plan Summary
 * This template has been refactored for code style, UI consistency, and performance.
 */

// --- 1. PREPARE DATA ---
$visible_skaters = spd_get_visible_skaters();
$visible_skater_ids = wp_list_pluck($visible_skaters, 'ID');
$plans_by_skater = [];

if (!empty($visible_skater_ids)) {
    // Get all yearly plans for all visible skaters in a single query.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    $all_plans = get_posts([
        'post_type'   => 'yearly_plan',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query'  => $skater_meta_query,
    ]);

    // Process and categorize each plan.
    $today_ymd       = date('Ymd');
    $near_future_ymd = date('Ymd', strtotime('+30 days'));

    foreach ($all_plans as $plan) {
        $skater_post_array = get_field('skater', $plan->ID);
        if (empty($skater_post_array[0])) continue;

        $skater_id = $skater_post_array[0]->ID;
        $skater_name = get_the_title($skater_id);

        $dates = get_field('season_dates', $plan->ID);
        $status = '';

        if (is_array($dates) && !empty($dates['start_date']) && !empty($dates['end_date'])) {
            $start_ymd = DateTime::createFromFormat('d/m/Y', $dates['start_date'])->format('Ymd');
            $end_ymd   = DateTime::createFromFormat('d/m/Y', $dates['end_date'])->format('Ymd');

            if ($today_ymd >= $start_ymd && $today_ymd <= $end_ymd) {
                $status = 'Current';
            } elseif ($start_ymd > $today_ymd && $start_ymd <= $near_future_ymd) {
                $status = 'Upcoming';
            }
        }

        // Only include current or upcoming plans in the summary.
        if ($status) {
            $peak_planning = get_field('peak_planning', $plan->ID);
            $primary_peak_post = $peak_planning['primary_peak_event'][0] ?? null;

            if (!isset($plans_by_skater[$skater_id])) {
                $plans_by_skater[$skater_id] = [
                    'skater_name' => $skater_name,
                    'plans' => [],
                ];
            }

            $plans_by_skater[$skater_id]['plans'][] = [
                'season' => get_field('season', $plan->ID) ?: get_the_title($plan->ID), // Use season field, fallback to title
                'status' => $status,
                'peak_event' => $primary_peak_post ? get_the_title($primary_peak_post) : '—',
                'goal_summary' => wp_trim_words(get_field('primary_season_goal', $plan->ID), 15, '...'),
                'view_url' => get_permalink($plan->ID),
                'edit_url' => site_url('/edit-yearly-plan/' . $plan->ID),
            ];
        }
    }
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Yearly Training Plans</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-yearly-plan/')); ?>">Add Yearly Plan</a>
</div>

<?php if (empty($plans_by_skater)) : ?>

    <p>No current or upcoming yearly plans found for assigned skaters.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Skater</th>
                <th>Season</th>
                <th>Status</th>
                <th>Primary Peak Event</th>
                <th>Primary Goal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans_by_skater as $skater_data) : ?>
                <?php foreach ($skater_data['plans'] as $index => $plan) : ?>
                    <tr>
                        <?php if ($index === 0) : // Show skater name only on the first row for that skater ?>
                            <td rowspan="<?php echo count($skater_data['plans']); ?>">
                                <strong><?php echo esc_html($skater_data['skater_name']); ?></strong>
                            </td>
                        <?php endif; ?>
                        <td><?php echo esc_html($plan['season']); ?></td>
                        <td><?php echo esc_html($plan['status']); ?></td>
                        <td><?php echo esc_html($plan['peak_event']); ?></td>
                        <td><?php echo esc_html($plan['goal_summary']); ?></td>
                        <td>
                            <a href="<?php echo esc_url($plan['view_url']); ?>">View</a> | 
                            <a href="<?php echo esc_url($plan['edit_url']); ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
