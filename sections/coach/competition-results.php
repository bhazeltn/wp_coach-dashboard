<?php
/**
 * Coach Dashboard Section: Competition Results Summary
 * This template has been refactored for code style, UI consistency, and performance.
 */

// --- 1. PREPARE DATA ---
$visible_skater_ids = wp_list_pluck(spd_get_visible_skaters(), 'ID');
$competitions_data = [];

if (!empty($visible_skater_ids)) {
    // Build the meta query to find results for any of the visible skaters.
    $skater_meta_query = ['relation' => 'OR'];
    foreach ($visible_skater_ids as $skater_id) {
        $skater_meta_query[] = [
            'key'     => 'skater',
            'value'   => '"' . $skater_id . '"',
            'compare' => 'LIKE',
        ];
    }

    // Get all competition results for the visible skaters.
    $all_results = get_posts([
        'post_type'   => 'competition_result',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query'  => $skater_meta_query,
    ]);

    $today_ymd = date('Y-m-d');
    $grouped_competitions = [];

    // Group results by their linked competition.
    foreach ($all_results as $result) {
        $competition_post_array = get_field('linked_competition', $result->ID);
        if (empty($competition_post_array[0])) continue;

        $competition = $competition_post_array[0];
        $comp_date = get_field('competition_date', $competition->ID);

        // Only include competitions that are in the past.
        if ($comp_date && $comp_date < $today_ymd) {
            if (!isset($grouped_competitions[$competition->ID])) {
                $comp_date_obj = $comp_date ? DateTime::createFromFormat('Y-m-d', $comp_date) : null;
                $grouped_competitions[$competition->ID] = [
                    'name' => get_the_title($competition),
                    'date' => $comp_date_obj ? $comp_date_obj->format('M j, Y') : '—',
                    'date_raw' => $comp_date,
                    'results' => [],
                ];
            }
            $grouped_competitions[$competition->ID]['results'][] = $result;
        }
    }

    // Sort the competitions by date, most recent first.
    uasort($grouped_competitions, function ($a, $b) {
        return strtotime($b['date_raw']) <=> strtotime($a['date_raw']);
    });

    // Prepare the final data array for the view.
    foreach ($grouped_competitions as $comp_id => $comp_data) {
        $results_for_view = [];
        foreach ($comp_data['results'] as $result) {
            $skater_post_array = get_field('skater', $result->ID);
            $skater_name = !empty($skater_post_array[0]) ? get_the_title($skater_post_array[0]) : '—';
            
            $comp_score = get_field('comp_score', $result->ID);
            $placement = $comp_score['placement'] ?? null;
            $total_score = $comp_score['total_competition_score'] ?? '—';
            
            $medal = '';
            if ($placement) {
                if ($placement == 1) $medal = ' 🥇';
                if ($placement == 2) $medal = ' 🥈';
                if ($placement == 3) $medal = ' 🥉';
            }

            $results_for_view[] = [
                'skater_name' => $skater_name,
                'level' => get_field('level', $result->ID) ?: '—',
                'discipline' => get_field('discipline', $result->ID) ?: '—',
                'placement' => ($placement ?: '—') . $medal,
                'total_score' => is_numeric($total_score) ? number_format($total_score, 2) : '—',
                'view_url' => get_permalink($result->ID),
                'edit_url' => site_url('/edit-competition-result/' . $result->ID),
            ];
        }
        $competitions_data[$comp_id] = $comp_data;
        $competitions_data[$comp_id]['results'] = $results_for_view;
    }
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Competition Results Summary</h2>
    <div class="actions">
        <a href="<?php echo esc_url(site_url('/view-all-competitions/')); ?>">Manage All Competitions</a>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-competition-result/')); ?>">Add Past Result</a>
    </div>
</div>

<?php if (empty($competitions_data)) : ?>

    <p>No past competition results found for assigned skaters.</p>

<?php else : ?>

    <?php foreach ($competitions_data as $comp) : ?>
        <h3 style="margin-top: 2.5rem;"><?php echo esc_html($comp['name']); ?> <span style="font-weight: 400; color: var(--text-muted); font-size: 0.9em;">(<?php echo esc_html($comp['date']); ?>)</span></h3>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Skater</th>
                    <th>Level</th>
                    <th>Discipline</th>
                    <th>Placement</th>
                    <th>Total Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comp['results'] as $result) : ?>
                    <tr>
                        <td><?php echo esc_html($result['skater_name']); ?></td>
                        <td><?php echo esc_html($result['level']); ?></td>
                        <td><?php echo esc_html($result['discipline']); ?></td>
                        <td><?php echo esc_html($result['placement']); ?></td>
                        <td><?php echo esc_html($result['total_score']); ?></td>
                        <td>
                            <a href="<?php echo esc_url($result['view_url']); ?>">View</a> | 
                            <a href="<?php echo esc_url($result['edit_url']); ?>">Update</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

<?php endif; ?>
