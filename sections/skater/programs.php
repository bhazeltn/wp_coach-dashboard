<?php
/**
 * Skater Dashboard Section: Programs
 * This template has been refactored for code style, UI consistency, and permissions.
 */

// --- 1. PREPARE DATA ---

// These global variables are set in the parent coach-skater-view.php template.
global $skater_id, $is_skater;

$programs_data = [];

// Find the skater's most recent yearly plan to determine the current season.
$current_ytp = get_posts([
    'post_type'   => 'yearly_plan',
    'numberposts' => 1,
    'meta_query'  => [['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE']],
    'orderby'     => 'date',
    'order'       => 'DESC',
]);

$current_season = !empty($current_ytp) ? get_field('season', $current_ytp[0]->ID) : null;

// Fetch all programs for this skater that match the current season.
if ($current_season) {
    $programs = get_posts([
        'post_type'   => 'program',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query'  => [
            'relation' => 'AND',
            ['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE'],
            ['key' => 'season', 'value' => $current_season, 'compare' => '='],
        ],
    ]);

    foreach ($programs as $program) {
        $program_id = $program->ID;
        $programs_data[] = [
            'title'    => get_the_title($program_id),
            'category' => get_field('program_category', $program_id) ?: '—',
            'season'   => get_field('season', $program_id) ?: '—',
            'view_url' => get_permalink($program_id),
            'edit_url' => site_url('/edit-program/' . $program_id),
        ];
    }
}

// --- 2. RENDER VIEW ---
?>

<div class="section-header">
    <h2 class="section-title">Programs (<?php echo esc_html($current_season ?: 'Current Season'); ?>)</h2>
    <?php if (!$is_skater) : ?>
        <a class="button button-primary" href="<?php echo esc_url(site_url('/create-program/?skater_id=' . $skater_id)); ?>">Add Program</a>
    <?php endif; ?>
</div>

<?php if (empty($programs_data)) : ?>

    <p>No programs have been created for this skater for the current season.</p>

<?php else : ?>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($programs_data as $program) : ?>
                <tr>
                    <td><?php echo esc_html($program['title']); ?></td>
                    <td><?php echo esc_html($program['category']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($program['view_url']); ?>">View</a>
                        <?php if (!$is_skater) : ?>
                            | <a href="<?php echo esc_url($program['edit_url']); ?>">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
