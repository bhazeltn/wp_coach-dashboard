<?php
// Template: yearly-plan/competitions.php

$post_id = $post_id ?? get_the_ID();
$skater = get_field('skater', $post_id)[0] ?? null;
$season_dates = get_field('season_dates', $post_id);
$season_start = DateTime::createFromFormat('d/m/Y', $season_dates['start_date'] ?? '')->format('Ymd');

if (!$skater || !is_object($skater)) {
    echo '<hr><div class="dashboard-box"><strong>Error:</strong> Skater not linked properly to this Yearly Plan.</div>';
    return;
}

$results_query = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater->ID . '"',
        'compare' => 'LIKE',
    ]],
]);

$today = date('Ymd');
$upcoming = [];
$completed = [];

foreach ($results_query as $result) {
    $competition = get_field('linked_competition', $result->ID);
    $comp_obj = is_array($competition) ? ($competition[0] ?? null) : $competition;
    if (!$comp_obj || !is_object($comp_obj)) continue;

    $comp_date = get_field('competition_date', $comp_obj->ID);
    if (!$comp_date) continue;

    $comp_date_obj = DateTime::createFromFormat('Y-m-d', $comp_date);
    if (!$comp_date_obj) continue;

    $comp_date_ymd = $comp_date_obj->format('Ymd');

    $tes   = get_field('technical_element_scores', $result->ID);
    $pcs   = get_field('program_component_scores', $result->ID);
    $total = get_field('total_score', $result->ID);
    $has_scores = !empty($tes['tes_sp']) || !empty($tes['tes_fs']) || !empty($pcs['pcs_sp']) || !empty($pcs['pcs_fp']) || !empty($total['total_competition_score']);

    $entry = [
        'name'   => get_the_title($comp_obj->ID),
        'level'  => get_field('level', $result->ID),
        'date'   => $comp_date,
        'result' => get_permalink($result->ID),
        'edit'   => site_url('/edit-competition-result/' . $result->ID),
        'total'  => $total['total_competition_score'] ?? null,
    ];

    if (!$has_scores && $comp_date_ymd >= $season_start) {
        $upcoming[] = $entry;
    } elseif ($has_scores && $comp_date_ymd >= $season_start && $comp_date_ymd < $today) {
        $completed[] = $entry;
    }
}
?>

<hr>
<div class="dashboard-box">
    <h3>📅 Upcoming Competitions</h3>

    <?php if (!empty($upcoming)) : ?>
        <table class="dashboard-table">
            <thead>
                <tr><th>Name</th><th>Level</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($upcoming as $comp) :
                    $d = $comp['date'] ? date_create($comp['date']) : null;
                    ?>
                    <tr>
                        <td><?= esc_html($comp['name']) ?></td>
                        <td><?= esc_html($comp['level'] ?? '—') ?></td>
                        <td><?= $d ? esc_html($d->format('M j, Y')) : '—' ?></td>
                        <td><a class="button-small" href="<?= esc_url($comp['edit']) ?>">Update</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No Upcoming Competitions Planned. Please <a href="<?= esc_url(admin_url('post-new.php?post_type=competition_result')) ?>">add competitions for this skater</a>.</p>
    <?php endif; ?>

    <p><a class="button" href="<?= esc_url(admin_url('post-new.php?post_type=competition_result')) ?>">Add Competition</a></p>
</div>

<hr>
<div class="dashboard-box">
    <h3>🏆 Competition Results</h3>

    <?php if (!empty($completed)) : ?>
        <table class="dashboard-table">
            <thead>
                <tr><th>Event</th><th>Level</th><th>Date</th><th>Total</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($completed as $comp) :
                    $d = $comp['date'] ? date_create($comp['date']) : null;
                    ?>
                    <tr>
                        <td><?= esc_html($comp['name']) ?></td>
                        <td><?= esc_html($comp['level'] ?? '—') ?></td>
                        <td><?= $d ? esc_html($d->format('M j, Y')) : '—' ?></td>
                        <td><?= esc_html($comp['total'] ?? '—') ?></td>
                        <td><a class="button-small" href="<?= esc_url($comp['result']) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No Competition Results Yet This Season.</p>
    <?php endif; ?>

    <p><a class="button" href="<?= esc_url(admin_url('post-new.php?post_type=competition_result')) ?>">Add Competition Result</a></p>
</div>
