<?php
/**
 * Skater Dashboard Section: Goals
 * This template has been refactored to include both active and missed/stalled goals.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$active_goals_data = [];
$missed_goals_data = [];
$today_ymd = date('Y-m-d');

// --- Query 1: Fetch Active Goals ---
$active_goals_query = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [
        'relation' => 'AND',
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ],
        [
            'key'     => 'current_status',
            'value'   => ['Achieved', 'Abandoned', 'On Hold'],
            'compare' => 'NOT IN',
        ]
    ]
]);

foreach ($active_goals_query as $goal) {
    $target_raw = get_field('target_date', $goal->ID);
    $date_obj = $target_raw ? DateTime::createFromFormat('d/m/Y', $target_raw) : null;

    $active_goals_data[] = [
        'title'      => get_the_title($goal->ID),
        'timeframe'  => get_field('goal_timeframe', $goal->ID) ?: '—',
        'status'     => get_field('current_status', $goal->ID) ?: '—',
        'target_date'=> $date_obj ? $date_obj->format('M j, Y') : '—',
        'is_overdue' => $date_obj && $date_obj < new DateTime('today'),
        'view_url'   => get_permalink($goal->ID),
        'edit_url'   => site_url('/edit-goal?goal_id=' . $goal->ID),
    ];
}

// --- Query 2: Fetch Missed or Stalled Goals ---
$missed_goals_query = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [
        'relation' => 'AND',
        [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ],
        [
            'relation' => 'OR',
            ['key' => 'current_status', 'value' => ['Abandoned', 'On Hold'], 'compare' => 'IN'],
            [
                'relation' => 'AND',
                ['key' => 'target_date', 'value' => $today_ymd, 'compare' => '<', 'type' => 'DATE'],
                ['key' => 'current_status', 'value' => 'Achieved', 'compare' => '!='],
            ]
        ]
    ]
]);

foreach ($missed_goals_query as $goal) {
    $target_raw = get_field('target_date', $goal->ID);
    $date_obj = $target_raw ? DateTime::createFromFormat('d/m/Y', $target_raw) : null;

    $missed_goals_data[] = [
        'title'      => get_the_title($goal->ID),
        'timeframe'  => get_field('goal_timeframe', $goal->ID) ?: '—',
        'status'     => get_field('current_status', $goal->ID) ?: '—',
        'target_date'=> $date_obj ? $date_obj->format('M j, Y') : '—',
        'view_url'   => get_permalink($goal->ID),
        'edit_url'   => site_url('/edit-goal?goal_id=' . $goal->ID),
    ];
}


// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Goals</h2>
    <?php if (!$is_skater) : ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-goal/?skater_id=' . $skater_id)); ?>">Add Goal</a>
    <?php endif; ?>
</div>

<!-- === Active Goals Table === -->
<h3>Active Goals</h3>
<?php if (empty($active_goals_data)) : ?>
    <p>No active goals found for this skater.</p>
<?php else : ?>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Goal</th>
                <th>Timeframe</th>
                <th>Status</th>
                <th>Target Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($active_goals_data as $goal) : ?>
                <tr>
                    <td><?php echo esc_html($goal['title']); ?></td>
                    <td><?php echo esc_html(ucfirst($goal['timeframe'])); ?></td>
                    <td><?php echo esc_html($goal['status']); ?></td>
                    <td <?php if ($goal['is_overdue']) echo 'style="color: #c0392b; font-weight: bold;"'; ?>>
                        <?php echo esc_html($goal['target_date']); ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url($goal['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($goal['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
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
                <th>Goal</th>
                <th>Timeframe</th>
                <th>Status</th>
                <th>Target Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($missed_goals_data as $goal) : ?>
                <tr>
                    <td><?php echo esc_html($goal['title']); ?></td>
                    <td><?php echo esc_html(ucfirst($goal['timeframe'])); ?></td>
                    <td><?php echo esc_html($goal['status']); ?></td>
                    <td><?php echo esc_html($goal['target_date']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($goal['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($goal['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
