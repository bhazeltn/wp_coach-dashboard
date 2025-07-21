<?php
/**
 * Skater Dashboard Section: Yearly Training Plans Summary
 * This template has been refactored for code style and UI consistency.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$plans_data = [];

// Fetch all YTPs linked to this skater
$plans = get_posts([
    'post_type'   => 'yearly_plan',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'season_dates_start_date',
    'orderby'     => 'meta_value',
    'order'       => 'DESC',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]],
]);

$today_ymd = date('Ymd');
$near_future_ymd = date('Ymd', strtotime('+30 days'));

foreach ($plans as $plan) {
    $plan_id = $plan->ID;
    $dates = get_field('season_dates', $plan_id);
    $status = 'Past'; // Default status

    if (is_array($dates) && !empty($dates['start_date']) && !empty($dates['end_date'])) {
        $start_ymd = DateTime::createFromFormat('d/m/Y', $dates['start_date'])->format('Ymd');
        $end_ymd   = DateTime::createFromFormat('d/m/Y', $dates['end_date'])->format('Ymd');

        if ($today_ymd >= $start_ymd && $today_ymd <= $end_ymd) {
            $status = 'Current';
        } elseif ($start_ymd > $today_ymd && $start_ymd <= $near_future_ymd) {
            $status = 'Upcoming';
        }
    }

    $peak_planning = get_field('peak_planning', $plan_id);
    $primary_peak_post = $peak_planning['primary_peak_event'][0] ?? null;

    $plans_data[] = [
        'season'     => get_field('season', $plan_id) ?: '—',
        'status'     => $status,
        'peak_event' => $primary_peak_post ? get_the_title($primary_peak_post) : '—',
        'view_url'   => get_permalink($plan_id),
        'edit_url'   => site_url('/edit-yearly-plan/' . $plan_id),
    ];
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Yearly Training Plans</h2>
    <?php if (!$is_skater) : // Skaters cannot add their own yearly plans ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-yearly-plan/?skater_id=' . $skater_id)); ?>">Add Yearly Plan</a>
    <?php endif; ?>
</div>

<?php if (empty($plans_data)) : ?>

    <p>No yearly training plans have been created for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Season</th>
                <th>Status</th>
                <th>Primary Peak Event</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans_data as $plan) : ?>
                <tr>
                    <td><?php echo esc_html($plan['season']); ?></td>
                    <td><?php echo esc_html($plan['status']); ?></td>
                    <td><?php echo esc_html($plan['peak_event']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($plan['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : // Skaters cannot update plans ?>
                            | <a href="<?php echo esc_url($plan['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
