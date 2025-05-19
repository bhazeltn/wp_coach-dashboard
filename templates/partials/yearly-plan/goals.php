<?php
// Template: yearly-plan/goals.php

$post_id = $post_id ?? get_the_ID();
$skater = get_field('skater', $post_id)[0] ?? null;
$season_dates = get_field('season_dates', $post_id);
$season_start = DateTime::createFromFormat('d/m/Y', $season_dates['start_date'] ?? '')->format('Ymd');
$season_end   = DateTime::createFromFormat('d/m/Y', $season_dates['end_date'] ?? '')->format('Ymd');
$today = date('Ymd');

$goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater->ID . '"',
        'compare' => 'LIKE',
    ]],
]);

$seasonal = $medium = $longterm = $overdue = [];

foreach ($goals as $goal) {
    $status        = get_field('current_status', $goal->ID);
    $timeframe_raw = get_field('goal_timeframe', $goal->ID);
    $timeframes    = is_array($timeframe_raw) ? $timeframe_raw : [$timeframe_raw];
    $target_raw    = get_field('target_date', $goal->ID);
    $target_ymd    = $target_raw ? DateTime::createFromFormat('d/m/Y', $target_raw)?->format('Ymd') : null;

    $is_overdue = ($target_ymd && $target_ymd < $today && $status !== 'Achieved');
    $in_season  = $target_ymd && $target_ymd >= $season_start && $target_ymd <= $season_end;

    if ($is_overdue) {
        $overdue[] = $goal;
    }

    if (!$in_season && !$is_overdue) continue;

    foreach ($timeframes as $timeframe) {
        if ($timeframe === 'season') $seasonal[] = $goal;
        elseif ($timeframe === 'medium') $medium[] = $goal;
        elseif ($timeframe === 'long') $longterm[] = $goal;
    }
}

function render_goal_table($goals, $today, $highlight_overdue = true) {
    echo '<table class="dashboard-table"><thead><tr>
        <th>Goal</th><th>Timeframe</th><th>Status</th><th>Target Date</th><th>Actions</th>
    </tr></thead><tbody>';

    foreach ($goals as $goal) {
        $title       = get_the_title($goal->ID) ?: '[Untitled]';
        $timeframes  = get_field('goal_timeframe', $goal->ID);
        $status      = get_field('current_status', $goal->ID) ?: '—';
        $target_raw  = get_field('target_date', $goal->ID);
        $target_disp = '—';
        $is_overdue  = false;

        if ($target_raw) {
            $dt = DateTime::createFromFormat('d/m/Y', $target_raw);
            if ($dt) {
                $target_disp = $dt->format('F j, Y');
                $target_ymd = $dt->format('Ymd');
                $is_overdue = ($target_ymd < $today && $status !== 'Achieved');
            }
        }

        $row_class = ($highlight_overdue && $is_overdue) ? ' style="color: red; font-weight: bold;"' : '';
        $tf_disp = is_array($timeframes)
            ? implode(', ', array_map('ucfirst', $timeframes))
            : ucfirst($timeframes);

        echo '<tr' . $row_class . '>';
        echo '<td>' . esc_html($title) . ($is_overdue ? ' ⚠ Past Due' : '') . '</td>';
        echo '<td>' . esc_html($tf_disp) . '</td>';
        echo '<td>' . esc_html($status) . '</td>';
        echo '<td>' . esc_html($target_disp) . '</td>';
        echo '<td><a href="' . esc_url(get_permalink($goal->ID)) . '">View</a> | ';
        echo '<a href="' . esc_url(site_url('/edit-goal?goal_id=' . $goal->ID)) . '">Update</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
?>

<hr>
<div class="dashboard-box">
    <h3>🎯 Goals for This Season</h3>

    <p><a class="button" href="<?= esc_url(site_url('/create-goal?skater_id=' . $skater->ID)) ?>">Add Goal</a></p>

    <?php if (!empty($overdue)) : ?>
        <h4 style="color: red;">⚠ Past Due Goals</h4>
        <?php render_goal_table($overdue, $today); ?>
    <?php endif; ?>

    <?php if (!empty($seasonal)) : ?>
        <h4>Seasonal Goals</h4>
        <?php render_goal_table($seasonal, $today); ?>
    <?php endif; ?>

    <?php if (!empty($medium)) : ?>
        <h4>Medium Term Goals</h4>
        <?php render_goal_table($medium, $today); ?>
    <?php endif; ?>

    <?php if (!empty($longterm)) : ?>
        <h4>Long Term Goals Due This Season</h4>
        <?php render_goal_table($longterm, $today); ?>
    <?php endif; ?>

    <?php if (empty($seasonal) && empty($medium) && empty($longterm) && empty($overdue)) : ?>
        <p>No seasonal, medium term, or long term goals found for this skater.</p>
    <?php endif; ?>

    <p style="margin-top: 1em;">Weekly goals are available in the <a href="#weekly-plans">Weekly Plan section</a>.</p>
</div>
