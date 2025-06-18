<?php
// Template: yearly-plan/weekly-plans.php

$post_id = $post_id ?? get_the_ID();
$skater = get_field('skater', $post_id)[0] ?? null;
$skater_id = $skater ? $skater->ID : null;

$current_user = wp_get_current_user();
$is_skater = in_array('skater', (array) $current_user->roles);

$weekly_plans = new WP_Query([
    'post_type'      => 'weekly_plan',
    'posts_per_page' => 5,
    'meta_key'       => 'week_start',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => [
        [
            'key'     => 'related_yearly_plan',
            'value'   => '"' . $post_id . '"',
            'compare' => 'LIKE',
        ]
    ],
]);
?>

<hr>
<div class="dashboard-box" id="weekly-plans">
    <h3>📋 Weekly Plans</h3>

    <?php if ($weekly_plans->have_posts()) : ?>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Week Starting</th>
                    <th>Theme</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($weekly_plans->have_posts()) :
                    $weekly_plans->the_post();
                    $week_start = get_field('week_start');
                    $date_fmt = $week_start
                        ? DateTime::createFromFormat('d/m/Y', $week_start)->format('M j, Y')
                        : '—';
                    ?>
                    <tr>
                        <td><?php echo esc_html($date_fmt); ?></td>
                        <td><?php echo esc_html(get_field('theme')); ?></td>
                        <td>
                            <a class="button-small" href="<?php echo esc_url(get_permalink()); ?>">View</a>
                            <?php if (!$is_skater): ?>
                                | <a class="button-small" href="<?php echo esc_url(site_url('/edit-weekly-plan/' . get_the_ID())); ?>">Update</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No Weekly Plans created yet.</p>
    <?php endif; ?>

    <?php if ($skater_id && !$is_skater) : ?>
        <p>
            <a class="button" href="<?php echo esc_url(site_url('/create-weekly-plan?skater_id=' . $skater_id . '&yearly_plan_id=' . $post_id)); ?>">
                Add Weekly Plan
            </a>
        </p>
    <?php endif; ?>
</div>

<?php wp_reset_postdata(); ?>
