<?php
/**
 * Coach Dashboard Section: Upcoming Competitions
 * This template has been refactored to include a countdown and improved actions.
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
    $results = get_posts([
        'post_type'   => 'competition_result',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query'  => $skater_meta_query,
    ]);

    $today_ymd = date('Y-m-d');
    $grouped_competitions = [];

    // Group results by their linked competition.
    foreach ($results as $result) {
        $competition_post_array = get_field('linked_competition', $result->ID);
        if (empty($competition_post_array[0])) continue;
        
        $competition = $competition_post_array[0];
        $comp_date = get_field('competition_date', $competition->ID);

        // Only include competitions that are in the future.
        if ($comp_date && $comp_date >= $today_ymd) {
            if (!isset($grouped_competitions[$competition->ID])) {
                $grouped_competitions[$competition->ID] = [
                    'competition' => $competition,
                    'results' => [],
                ];
            }
            $grouped_competitions[$competition->ID]['results'][] = $result;
        }
    }

    // Sort the grouped competitions by date.
    uasort($grouped_competitions, function ($a, $b) {
        $dateA = get_field('competition_date', $a['competition']->ID);
        $dateB = get_field('competition_date', $b['competition']->ID);
        return strtotime($dateA) <=> strtotime($dateB);
    });

    // Prepare the final data array for the view.
    foreach ($grouped_competitions as $group) {
        $competition = $group['competition'];
        $comp_date_raw = get_field('competition_date', $competition->ID);
        $date_obj = $comp_date_raw ? DateTime::createFromFormat('Y-m-d', $comp_date_raw) : null;

        $skater_names = array_map(function ($result) {
            $skater_post_array = get_field('skater', $result->ID);
            return !empty($skater_post_array[0]) ? get_the_title($skater_post_array[0]) : '—';
        }, $group['results']);

        $competitions_data[] = [
            'name' => get_the_title($competition),
            'date' => $date_obj ? $date_obj->format('M j, Y') : '—',
            'countdown' => function_exists('spd_get_countdown_string') ? spd_get_countdown_string($comp_date_raw) : '',
            'location' => get_field('competition_location', $competition->ID) ?: '—',
            'skaters' => implode(', ', $skater_names),
            'view_url' => get_permalink($competition->ID),
        ];
    }
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Upcoming Competitions</h2>
    <div class="actions">
        <a href="<?php echo esc_url(site_url('/view-all-competitions/')); ?>">Manage All Competitions</a>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-competition-result/')); ?>">Add Skater Entry</a>
    </div>
</div>

<?php if (empty($competitions_data)) : ?>

    <p>No upcoming competitions found for assigned skaters.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Competition</th>
                <th>Date</th>
                <th>Countdown</th>
                <th>Location</th>
                <th>Skater(s)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competitions_data as $comp) : ?>
                <tr>
                    <td><?php echo esc_html($comp['name']); ?></td>
                    <td><?php echo esc_html($comp['date']); ?></td>
                    <td><span style="font-style: italic; color: var(--text-muted);"><?php echo esc_html($comp['countdown']); ?></span></td>
                    <td><?php echo esc_html($comp['location']); ?></td>
                    <td><?php echo esc_html($comp['skaters']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($comp['view_url']); ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
