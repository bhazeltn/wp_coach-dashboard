<?php
/**
 * Skater Dashboard Section: Upcoming Competitions
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$upcoming_data = [];
$today_ymd = date('Y-m-d');

// Get all competition result entries for this skater.
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE']]
]);

// Filter to find only the ones where the linked competition is in the future.
foreach ($results as $result) {
    $competition_post_array = get_field('linked_competition', $result->ID);
    if (empty($competition_post_array[0])) continue;

    $competition = $competition_post_array[0];
    $comp_date_raw = get_field('competition_date', $competition->ID);

    if ($comp_date_raw && $comp_date_raw >= $today_ymd) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $comp_date_raw);
        $upcoming_data[] = [
            'name'       => get_the_title($competition),
            'date'       => $date_obj ? $date_obj->format('M j, Y') : '—',
            'countdown'  => function_exists('spd_get_countdown_string') ? spd_get_countdown_string($comp_date_raw) : '',
            'type'       => get_field('competition_type', $competition) ?: '—',
            'level'      => get_field('level', $result->ID) ?: '—',
            'discipline' => get_field('discipline', $result->ID) ?: '—',
            'location'   => get_field('competition_location', $competition) ?: '—',
            'view_url'   => get_permalink($result->ID),
            'edit_url'   => site_url('/edit-competition-result/' . $result->ID),
            'sort_date'  => $comp_date_raw,
        ];
    }
}

// Sort the final array by the competition date.
usort($upcoming_data, function ($a, $b) {
    return strtotime($a['sort_date']) <=> strtotime($b['sort_date']);
});


// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Upcoming Competitions</h2>
    <?php if (!$is_skater) : ?>
        <div class="actions">
            <a href="<?php echo esc_url(site_url('/view-all-competitions/')); ?>">Manage All Competitions</a>
            <a class="button button-primary" href="<?php echo esc_url(site_url('/create-competition-result/?skater_id=' . $skater_id)); ?>">Add Skater Entry</a>
        </div>
    <?php endif; ?>
</div>

<?php if (empty($upcoming_data)) : ?>

    <p>No upcoming competitions found for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Competition</th>
                <th>Date</th>
                <th>Countdown</th>
                <th>Level</th>
                <th>Discipline</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($upcoming_data as $comp) : ?>
                <tr>
                    <td><?php echo esc_html($comp['name']); ?></td>
                    <td><?php echo esc_html($comp['date']); ?></td>
                    <td><span style="font-style: italic; color: var(--text-muted);"><?php echo esc_html($comp['countdown']); ?></span></td>
                    <td><?php echo esc_html($comp['level']); ?></td>
                    <td><?php echo esc_html($comp['discipline']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($comp['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($comp['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
