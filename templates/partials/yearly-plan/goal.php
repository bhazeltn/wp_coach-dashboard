<?php
// Template: yearly-plan/goal.php

$post_id = $post_id ?? get_the_ID();
$goal = get_field('primary_season_goal', $post_id);
?>

<hr>
<div class="dashboard-box">
    <h3>🎯 Primary Season Goal</h3>
    <?php if (!empty($goal)): ?>
        <?= wp_kses_post($goal) ?>
    <?php else: ?>
        <p><em>No primary goal recorded for this season.</em></p>
    <?php endif; ?>
</div>
