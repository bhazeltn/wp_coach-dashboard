<?php
// Template: yearly-plan/header.php

$post_id = $args['post_id'] ?? get_the_ID();
$season = get_field('season', $post_id);
$skaters = get_field('skater', $post_id);
$season_dates = get_field('season_dates', $post_id);

$start_raw = $season_dates['start_date'] ?? '';
$end_raw = $season_dates['end_date'] ?? '';
$start_fmt = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw)->format('F j, Y') : '';
$end_fmt = $end_raw ? DateTime::createFromFormat('d/m/Y', $end_raw)->format('F j, Y') : '';

$skater = $skaters[0] ?? null;
$skater_name = $skater ? get_the_title($skater) : '—';
$skater_slug = $skater ? $skater->post_name : '';
?>

<div class="wrap coach-dashboard">
<h1>Yearly Training Plan</h1>

<div class="dashboard-box">
    <p><strong>Skater:</strong>
        <a href="<?= esc_url(site_url('/skater/' . $skater_slug)) ?>">
            <?= esc_html($skater_name) ?>
        </a>
    </p>
    <p><strong>Season:</strong> <?= esc_html($season) ?>
        <?php if ($start_fmt || $end_fmt): ?>
            (<?= esc_html($start_fmt . ' to ' . $end_fmt) ?>)
        <?php endif; ?>
    </p>
</div>
