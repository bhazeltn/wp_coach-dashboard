<?php
// Template: yearly-plan/macrocycles.php

$post_id = $post_id ?? get_the_ID();
$macrocycles = get_field('macrocycles', $post_id);
?>

<hr>
<div class="dashboard-box">
    <h3>📆 Macrocycle Breakdown</h3>

    <?php if (!empty($macrocycles)) : ?>
        <?php foreach ($macrocycles as $index => $cycle) :
            $start_fmt = $cycle['phase_start']
                ? DateTime::createFromFormat('d/m/Y', $cycle['phase_start'])->format('M j')
                : '';
            $end_fmt = $cycle['phase_end']
                ? DateTime::createFromFormat('d/m/Y', $cycle['phase_end'])->format('M j')
                : '';
        ?>

            <?php if ($index > 0) echo '<hr class="macro-separator">'; ?>

            <div class="macrocycle-block">
                <details class="macrocycle-toggle">
                    <summary>
                        <strong><?= esc_html($cycle['phase_title']) ?></strong>
                        (<?= esc_html($start_fmt) ?> – <?= esc_html($end_fmt) ?>)
                    </summary>

                    <p><strong>Focus:</strong> <?= esc_html($cycle['phase_focus']) ?></p>
                    <p><strong>Evaluation:</strong><br><?= wp_kses_post($cycle['evaluation_strategy']) ?></p>
                    <p><strong>Coach Notes:</strong><br><?= wp_kses_post($cycle['coach_notes']) ?></p>

                    <?php if (!empty($cycle['element_focus'])) : ?>
                        <h4>Element Focus</h4>
                        <table class="dashboard-subtable">
                            <thead>
                                <tr>
                                    <th>Element(s)</th>
                                    <th>Type</th>
                                    <th>Stage(s)</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cycle['element_focus'] as $element) :
                                    $stages = $element['acquisition_stage'];
                                    $stage_display = is_array($stages) ? implode(', ', $stages) : ($stages ?: '—');
                                ?>
                                    <tr>
                                        <td><?= esc_html($element['element_name'] ?: '—') ?></td>
                                        <td><?= esc_html($element['element_type'] ?: '—') ?></td>
                                        <td><?= esc_html($stage_display) ?></td>
                                        <td><?= wp_kses_post($element['element_notes'] ?: '—') ?></td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </details>
            </div>

        <?php endforeach; ?>
    <?php else : ?>
        <p><em>No macrocycles defined for this season.</em></p>
    <?php endif; ?>
</div>
