<?php
// Template: yearly-plan/injuries.php

$post_id = $post_id ?? get_the_ID();
$skater = get_field('skater', $post_id)[0] ?? null;
$season_dates = get_field('season_dates', $post_id);

$season_start = DateTime::createFromFormat('d/m/Y', $season_dates['start_date'] ?? '')->format('Ymd');
$season_end   = DateTime::createFromFormat('d/m/Y', $season_dates['end_date'] ?? '')->format('Ymd');
$today = date('Ymd');

$injury_logs = get_posts([
    'post_type'   => 'injury_log',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'injured_skater',
        'value'   => '"' . $skater->ID . '"',
        'compare' => 'LIKE',
    ]],
]);

$season_injuries = [];

foreach ($injury_logs as $log) {
    $log_id = $log->ID;

    $onset_raw = get_field('date_of_onset', $log_id);
    $recovery_raw = get_field('date_cleared', $log_id);
    $status = get_field('recovery_status', $log_id);
    $status_value = is_array($status) ? ($status['value'] ?? '') : sanitize_title($status);

    $onset_ymd = $onset_raw ? DateTime::createFromFormat('d/m/Y', $onset_raw)?->format('Ymd') : null;
    $recovery_ymd = $recovery_raw ? DateTime::createFromFormat('d/m/Y', $recovery_raw)?->format('Ymd') : null;

    $still_active =
        !$recovery_ymd &&
        $status_value !== 'cleared';

    $during_season =
        ($onset_ymd && $onset_ymd >= $season_start && $onset_ymd <= $season_end) ||
        ($recovery_ymd && $recovery_ymd >= $season_start && $recovery_ymd <= $season_end) ||
        ($onset_ymd && $onset_ymd <= $season_end && $still_active);

    if ($during_season) {
        $season_injuries[] = $log;
    }
}
?>

<hr>
<div class="dashboard-box">
    <h3>🩹 Injury & Health Log</h3>

    <?php
    $is_skater = in_array('skater', (array) $current_user->roles);
    if (!$is_skater) {
        echo '<p><a class="button" href="' . esc_url(site_url('/create-injury-log?skater_id=' . $skater->ID)) . '">Add Injury Log</a></p>';
    }
    ?>



    <?php if (empty($season_injuries)) : ?>
        <p>No injuries recorded during this training season.</p>
    <?php else : ?>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Onset</th>
                    <th>Return</th>
                    <th>Severity</th>
                    <th>Body Area</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $colors = [
                    'cleared'     => '#3c763d',
                    'limited'     => '#e67e22',
                    'modified'    => '#3498db',
                    'resting'     => '#c0392b',
                    'rehab_only'  => '#9b59b6',
                ];

                foreach ($season_injuries as $log) :
                    $log_id = $log->ID;

                    $onset = get_field('date_of_onset', $log_id);
                    $onset_fmt = $onset ? DateTime::createFromFormat('d/m/Y', $onset)?->format('M j, Y') : '—';

                    $recovery = get_field('date_cleared', $log_id);
                    $recovery_fmt = $recovery ? DateTime::createFromFormat('d/m/Y', $recovery)?->format('M j, Y') : '—';

                    $status = get_field('recovery_status', $log_id);
                    $status_value = is_array($status) ? ($status['value'] ?? '') : sanitize_title($status);
                    $status_label = is_array($status) ? ($status['label'] ?? '—') : ($status ?: '—');

                    $severity = get_field('severity', $log_id);
                    $severity_label = is_array($severity) ? ($severity['label'] ?? '—') : ($severity ?: '—');

                    $body_area = get_field('body_area', $log_id);
                    $body_area_display = is_array($body_area) ? implode(', ', $body_area) : ($body_area ?: '—');

                    $dot_color = $colors[$status_value] ?? '#999';

                    $view_link = get_permalink($log_id);
                    $edit_link = site_url('/edit-injury-log/' . $log_id);
                    ?>
                    <tr>
                        <td>
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:<?= esc_attr($dot_color) ?>;margin-right:6px;"></span>
                            <?= esc_html($status_label) ?>
                        </td>
                        <td><?= esc_html($onset_fmt) ?></td>
                        <td><?= esc_html($recovery_fmt) ?></td>
                        <td><?= esc_html($severity_label) ?></td>
                        <td><?= esc_html($body_area_display) ?></td>
                        <td>
                            <a href="<?= esc_url($view_link) ?>">View</a> |
                            <a href="<?= esc_url($edit_link) ?>">Update</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
