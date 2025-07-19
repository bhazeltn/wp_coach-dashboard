<?php
/**
 * Coach Dashboard Section: Skater Overview
 * * This template has been refactored for code style, UI consistency, and readability.
 * * Flag is now next to skater's name with a tooltip for the full federation name.
 */

// --- 1. PREPARE DATA ---
$skaters = spd_get_visible_skaters();
$today_ymd = date('Ymd');
$skaters_data = [];

if (!empty($skaters)) {
    foreach ($skaters as $skater) {
        // Basic skater info
        $skater_id   = $skater->ID;
        $skater_slug = $skater->post_name;
        
        // Get the 3-letter code for the flag function by telling ACF not to format the value.
        $federation_code = get_field('federation', $skater_id, false);
        // Get the full name label for the tooltip by letting ACF format the value (its default behavior).
        $federation_name = get_field('federation', $skater_id);

        // Find the current yearly plan for this skater
        $current_plan_post = null;
        $plans = get_posts([
            'post_type'   => 'yearly_plan',
            'numberposts' => -1,
            'post_status' => 'publish',
            'meta_query'  => [[
                'key'     => 'skater',
                'value'   => '"' . $skater_id . '"',
                'compare' => 'LIKE',
            ]],
        ]);

        foreach ($plans as $plan) {
            $season_dates = get_field('season_dates', $plan->ID);
            if (is_array($season_dates) && !empty($season_dates['start_date']) && !empty($season_dates['end_date'])) {
                $start_date_ymd = DateTime::createFromFormat('d/m/Y', $season_dates['start_date'])->format('Ymd');
                $end_date_ymd   = DateTime::createFromFormat('d/m/Y', $season_dates['end_date'])->format('Ymd');

                if ($today_ymd >= $start_date_ymd && $today_ymd <= $end_date_ymd) {
                    $current_plan_post = $plan;
                    break;
                }
            }
        }
        
        // Prepare data array for the view
        $skaters_data[] = [
            'name' => get_the_title($skater_id),
            'age' => get_field('age', $skater_id) ?: '—',
            'level' => get_field('current_level', $skater_id) ?: '—',
            'federation_name' => is_string($federation_name) ? $federation_name : '',
            'flag_emoji' => function_exists('spd_get_country_flag_emoji') ? spd_get_country_flag_emoji($federation_code) : '',
            'view_url' => site_url('/skater/' . $skater_slug),
            'edit_url' => site_url('/edit-skater/' . $skater_id),
            'current_plan' => $current_plan_post ? [
                'title' => get_the_title($current_plan_post->ID),
                'url' => get_permalink($current_plan_post->ID),
            ] : null,
        ];
    }
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Skater Overview</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-skater')); ?>">Add New Skater</a>
</div>

<?php if (empty($skaters_data)) : ?>

    <p>No skaters found.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Skater</th>
                <th>Age</th>
                <th>Level</th>
                <th>Current Yearly Plan</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($skaters_data as $skater) : ?>
                <tr>
                    <td>
                        <span title="<?php echo esc_attr($skater['federation_name']); ?>" style="margin-right: 0.75em; font-size: 1.5em; vertical-align: middle; cursor: help;"><?php echo $skater['flag_emoji']; ?></span>
                        <a href="<?php echo esc_url($skater['view_url']); ?>">
                            <?php echo esc_html($skater['name']); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($skater['age']); ?></td>
                    <td><?php echo esc_html($skater['level']); ?></td>
                    <td>
                        <?php if ($skater['current_plan']) : ?>
                            <a href="<?php echo esc_url($skater['current_plan']['url']); ?>">
                                <?php echo esc_html($skater['current_plan']['title']); ?>
                            </a>
                        <?php else : ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url($skater['view_url']); ?>">View</a> | 
                        <a href="<?php echo esc_url($skater['edit_url']); ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
