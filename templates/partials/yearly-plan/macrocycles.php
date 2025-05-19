<?php
// Template: yearly-plan/macrocycles.php

$post_id = $post_id ?? get_the_ID();
$macrocycles = get_field('macrocycles', $post_id);
?>

<hr>
<div class="dashboard-box">
    <h3>📆 Macrocycle Breakdown</h3>

    <?php if (!empty($macrocycles)) : ?>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Phase</th>
                    <th>Focus</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Evaluation</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($macrocycles as $cycle) :
                    $start_fmt = $cycle['phase_start']
                        ? DateTime::createFromFormat('d/m/Y', $cycle['phase_start'])->format('M j')
                        : '';
                    $end_fmt = $cycle['phase_end']
                        ? DateTime::createFromFormat('d/m/Y', $cycle['phase_end'])->format('M j')
                        : '';
                    ?>
                    <tr>
                        <td><?= esc_html($cycle['phase_title']) ?></td>
                        <td><?= esc_html($cycle['phase_focus']) ?></td>
                        <td><?= esc_html($start_fmt) ?></td>
                        <td><?= esc_html($end_fmt) ?></td>
                        <td><?= wp_kses_post($cycle['evaluation_strategy']) ?></td>
                        <td><?= wp_kses_post($cycle['coach_notes']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><em>No macrocycles defined for this season.</em></p>
    <?php endif; ?>
</div>
