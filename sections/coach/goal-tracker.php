<?php
/**
 * Coach Dashboard Section: Goal Tracker
 * This template has been refactored for code style, UI consistency, and performance.
 */

// --- 1. PREPARE DATA ---
$visible_skater_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');
$long_term_goals_data = [];
$weekly_goals_data = [];
$missed_goals_data = [];

if (!empty($visible_skater_ids)) {
    $today_ymd = date('Y-m-d');

    // Build the meta query to find goals for any of the visible skaters.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    // --- Query 1: Mid/Long-Term Goals (In Progress) ---
    $long_term_goals = get_posts([
        'post_type'   => 'goal',
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            ['key' => 'goal_timeframe', 'value' => ['long', 'medium', 'season'], 'compare' => 'IN'],
            ['key' => 'current_status', 'value' => ['Not Started', 'In Progress'], 'compare' => 'IN'],
            $skater_meta_query,
        ],
        'meta_key' => 'target_date', 'orderby'  => 'meta_value', 'order'    => 'ASC'
    ]);
    $long_term_goals_data = $long_term_goals; // Assign directly for this simple case

    // --- Query 2: Active Weekly Goals ---
    $weekly_goals = get_posts([
        'post_type'   => 'goal',
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            ['key' => 'goal_timeframe', 'value' => ['week', 'micro'], 'compare' => 'IN'],
            ['key' => 'target_date', 'value' => [$today_ymd, date('Y-m-d', strtotime('+14 days'))], 'compare' => 'BETWEEN', 'type' => 'DATE'],
            $skater_meta_query,
        ],
        'meta_key' => 'target_date', 'orderby'  => 'meta_value', 'order'    => 'ASC'
    ]);
    $weekly_goals_data = $weekly_goals;

    // --- Query 3: Missed or Stalled Goals ---
    $missed_goals = get_posts([
        'post_type'   => 'goal',
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            [
                'relation' => 'OR',
                ['key' => 'current_status', 'value' => ['Abandoned', 'On Hold'], 'compare' => 'IN'],
                [
                    'relation' => 'AND',
                    ['key' => 'target_date', 'value' => $today_ymd, 'compare' => '<', 'type' => 'DATE'],
                    ['key' => 'current_status', 'value' => 'Achieved', 'compare' => '!='],
                ]
            ],
            $skater_meta_query,
        ],
        'meta_key' => 'target_date', 'orderby'  => 'meta_value', 'order'    => 'ASC'
    ]);
    $missed_goals_data = $missed_goals;
}

/**
 * Reusable function to render a single goal row in a table.
 * This avoids repeating HTML and logic.
 *
 * @param WP_Post $goal The goal post object.
 */
function spd_render_goal_row($goal) {
    // Prepare data for the row
    $goal_id = $goal->ID;
    $skater_post_array = get_field('skater', $goal_id);
    $skater_name = !empty($skater_post_array[0]) ? get_the_title($skater_post_array[0]) : '—';
    
    $target_raw = get_field('target_date', $goal_id);
    $date_obj = $target_raw ? DateTime::createFromFormat('d/m/Y', $target_raw) : null;
    $target_date = $date_obj ? $date_obj->format('M j, Y') : '—';

    $is_overdue = $date_obj && $date_obj < new DateTime('today') && get_field('current_status', $goal_id) !== 'Achieved';

    ?>
    <tr>
        <td><?php echo esc_html($skater_name); ?></td>
        <td><?php echo esc_html(get_the_title($goal_id)); ?></td>
        <td><?php echo esc_html(get_field('current_status', $goal_id) ?: '—'); ?></td>
        <td <?php if ($is_overdue) echo 'style="color: #c0392b; font-weight: bold;"'; ?>>
            <?php echo esc_html($target_date); ?>
        </td>
        <td>
            <a href="<?php echo esc_url(get_permalink($goal_id)); ?>">View</a> |
            <a href="<?php echo esc_url(site_url('/edit-goal?goal_id=' . $goal_id)); ?>">Update</a>
        </td>
    </tr>
    <?php
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Goal Tracker</h2>
    <a class="button button-primary" href="<?php echo esc_url(site_url('/create-goal/')); ?>">Add Goal</a>
</div>

<!-- === Mid/Long-Term Goals Table === -->
<h3>Mid/Long-Term Goals</h3>
<?php if (empty($long_term_goals_data)) : ?>
    <p>No active mid or long-term goals found.</p>
<?php else : ?>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Skater</th>
                <th>Goal</th>
                <th>Status</th>
                <th>Target Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($long_term_goals_data as $goal) { spd_render_goal_row($goal); } ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- === Active Weekly Goals Table === -->
<h3 style="margin-top: 2.5rem;">Active Weekly Goals</h3>
<?php if (empty($weekly_goals_data)) : ?>
    <p>No weekly goals found for the upcoming 2 weeks.</p>
<?php else : ?>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Skater</th>
                <th>Goal</th>
                <th>Status</th>
                <th>Target Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weekly_goals_data as $goal) { spd_render_goal_row($goal); } ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- === Missed or Stalled Goals Table === -->
<h3 style="margin-top: 2.5rem;">Stalled or Missed Goals</h3>
<?php if (empty($missed_goals_data)) : ?>
    <p>No stalled or missed goals found.</p>
<?php else : ?>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Skater</th>
                <th>Goal</th>
                <th>Status</th>
                <th>Target Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($missed_goals_data as $goal) { spd_render_goal_row($goal); } ?>
        </tbody>
    </table>
<?php endif; ?>
