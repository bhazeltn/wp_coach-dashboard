<?php
// Template: yearly-plan/peak.php

$post_id = $post_id ?? get_the_ID();
$peak = get_field('peak_planning', $post_id);

$primary_event   = $peak['primary_peak_event'][0] ?? null;
$secondary_event = $peak['secondary_peak_event'][0] ?? null;

$primary_name = $primary_event ? get_the_title($primary_event) : '—';
$secondary_name = $secondary_event ? get_the_title($secondary_event) : '—';

$primary_start = $peak['primary_peak_start_date'] ?? '';
$primary_end   = $peak['primary_peak_end_date'] ?? '';
$secondary_start = $peak['secondary_peak_start_date'] ?? '';
$secondary_end   = $peak['secondary_peak_end_date'] ?? '';

$primary_range = ($primary_start || $primary_end)
    ? trim(
        ($primary_start ? DateTime::createFromFormat('d/m/Y', $primary_start)->format('M j') : '') .
        ' – ' .
        ($primary_end ? DateTime::createFromFormat('d/m/Y', $primary_end)->format('M j') : ''),
        ' –'
    )
    : '—';

$secondary_range = ($secondary_start || $secondary_end)
    ? trim(
        ($secondary_start ? DateTime::createFromFormat('d/m/Y', $secondary_start)->format('M j') : '') .
        ' – ' .
        ($secondary_end ? DateTime::createFromFormat('d/m/Y', $secondary_end)->format('M j') : ''),
        ' –'
    )
    : '';
?>

<hr>
<div class="dashboard-box">
    <h3>📌 Peak Planning</h3>

    <table class="dashboard-table">
        <thead>
            <tr>
                <?php if ($secondary_event): ?>
                    <th>Peak</th>
                <?php endif; ?>
                <th>Event</th>
                <th>Dates</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php if ($secondary_event): ?>
                    <td>Primary</td>
                <?php endif; ?>
                <td><?= esc_html($primary_name) ?></td>
                <td><?= esc_html($primary_range) ?></td>
            </tr>

            <?php if ($secondary_event): ?>
            <tr>
                <td>Secondary</td>
                <td><?= esc_html($secondary_name) ?></td>
                <td><?= esc_html($secondary_range ?: '—') ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
