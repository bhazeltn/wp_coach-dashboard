<?php
/**
 * Skater Dashboard Section: Weekly Plans Tracker
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$plans_data = [];

// Fetch the 5 most recent weekly plans for this skater.
$plans_query = new WP_Query([
    'post_type'      => 'weekly_plan',
    'posts_per_page' => 5,
    'meta_key'       => 'week_start',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => [
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ],
    ],
]);

if ($plans_query->have_posts()) {
    while ($plans_query->have_posts()) {
        $plans_query->the_post();
        $plan_id = get_the_ID();

        $start_raw = get_field('week_start', $plan_id);
        $date_obj = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw) : null;
        
        $plans_data[] = [
            'week_start' => $date_obj ? $date_obj->format('M j, Y') : '—',
            'theme'      => get_field('theme', $plan_id) ?: '—',
            'view_url'   => get_permalink($plan_id),
            'edit_url'   => site_url('/edit-weekly-plan/' . $plan_id),
        ];
    }
}
wp_reset_postdata();

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Recent Weekly Plans</h2>
    <?php if (!$is_skater) : // Skaters cannot add their own weekly plans ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-weekly-plan/?skater_id=' . $skater_id)); ?>">Add Weekly Plan</a>
    <?php endif; ?>
</div>

<?php if (empty($plans_data)) : ?>

    <p>No weekly plans have been created for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Week Starting</th>
                <th>Theme</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans_data as $plan) : ?>
                <tr>
                    <td><?php echo esc_html($plan['week_start']); ?></td>
                    <td><?php echo esc_html($plan['theme']); ?></td>
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
