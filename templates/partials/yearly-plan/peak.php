<?php
// Template: yearly-plan/peak.php

$post_id = $post_id ?? get_the_ID();
$peak = get_field('peak_planning', $post_id);

$primary_event = $peak['primary_peak_event'][0] ?? null;
$secondary_event = $peak['secondary_peak_event'][0] ?? null;
?>

<hr>
<div class="dashboard-box">
    <h3>📌 Peak Planning</h3>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Peak</th>
                <th>Event</th>
                <th>Dates</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (['Primary' => $primary_event, 'Secondary' => $secondary_event] as $label => $event) :
                $name = $event ? get_the_title($event) : '—';
                $start_key = strtolower($label) . '_peak_start_date';
                $end_key = strtolower($label) . '_peak_end_date';
                $start = $peak[$start_key] ?? '';
                $end = $peak[$end_key] ?? '';
                $start_fmt = $start ? DateTime::createFromFormat('d/m/Y', $start)->format('M j') : '';
                $end_fmt = $end ? DateTime::createFromFormat('d/m/Y', $end)->format('M j') : '';
                $range = $start_fmt || $end_fmt ? trim($start_fmt . ' – ' . $end_fmt, ' –') : '—';
                ?>
                <tr>
                    <td><?= $label ?></td>
                    <td><?= esc_html($name) ?></td>
                    <td><?= esc_html($range) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
