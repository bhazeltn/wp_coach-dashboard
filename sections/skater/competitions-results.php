<?php
/**
 * Skater Dashboard Section: Competition Results
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$results_data = [];
$today_ymd = date('Y-m-d');

// Get all competition result entries for this skater.
$results = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE']]
]);

// Filter to find only the ones where the linked competition is in the past.
foreach ($results as $result) {
    $competition_post_array = get_field('linked_competition', $result->ID);
    if (empty($competition_post_array[0])) continue;

    $competition = $competition_post_array[0];
    $comp_date_raw = get_field('competition_date', $competition->ID);

    if ($comp_date_raw && $comp_date_raw < $today_ymd) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $comp_date_raw);

        $comp_score = get_field('comp_score', $result->ID) ?: [];
        $placement = $comp_score['placement'] ?? null;
        $total_score = $comp_score['total_competition_score'] ?? '—';
        
        $medal = '';
        if ($placement) {
            if ($placement == 1) $medal = ' 🥇';
            if ($placement == 2) $medal = ' 🥈';
            if ($placement == 3) $medal = ' 🥉';
        }

        $results_data[] = [
            'name'       => get_the_title($competition),
            'date'       => $date_obj ? $date_obj->format('M j, Y') : '—',
            'level'      => get_field('level', $result->ID) ?: '—',
            'placement'  => ($placement ?: '—') . $medal,
            'total_score'=> is_numeric($total_score) ? number_format($total_score, 2) : '—',
            'view_url'   => get_permalink($result->ID),
            'edit_url'   => site_url('/edit-competition-result/' . $result->ID),
            'sort_date'  => $comp_date_raw,
        ];
    }
}

// Sort the final array by the competition date, most recent first.
usort($results_data, function ($a, $b) {
    return strtotime($b['sort_date']) <=> strtotime($a['sort_date']);
});

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Competition Results</h2>
    <?php if (!$is_skater) : ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-competition-result/?skater_id=' . $skater_id)); ?>">Add Past Result</a>
    <?php endif; ?>
</div>

<?php if (empty($results_data)) : ?>

    <p>No past competition results found for this skater.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Competition</th>
                <th>Date</th>
                <th>Level</th>
                <th>Placement</th>
                <th>Total Score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results_data as $result) : ?>
                <tr>
                    <td><?php echo esc_html($result['name']); ?></td>
                    <td><?php echo esc_html($result['date']); ?></td>
                    <td><?php echo esc_html($result['level']); ?></td>
                    <td><?php echo esc_html($result['placement']); ?></td>
                    <td><?php echo esc_html($result['total_score']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($result['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($result['edit_url']); ?>">Update</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
