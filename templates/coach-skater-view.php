<?php
/**
 * Template: Individual Skater Dashboard
 * This version has been updated to calculate age from date_of_birth.
 */

// --- 1. PREPARE DATA & CHECK PERMISSIONS ---

include plugin_dir_path(__FILE__) . 'partials/header-dashboard.php';

$skater_slug = get_query_var('skater_view');
$skater_post = get_page_by_path($skater_slug, OBJECT, 'skater');

if (!$skater_post) { wp_die('Skater not found.'); }

$skater_id = $skater_post->ID;
$current_user = wp_get_current_user();
$user_roles = (array) $current_user->roles;
$is_current_user_a_skater = in_array('skater', $user_roles);
$is_current_user_a_coach = in_array('coach', $user_roles) || in_array('administrator', $user_roles);

// --- Permission Check ---
$is_allowed = false;
if ($is_current_user_a_coach) {
    $is_allowed = true;
} elseif ($is_current_user_a_skater) {
    $linked_user_field = get_field('skater_account', $skater_id);
    $linked_user_id = is_array($linked_user_field) ? $linked_user_field['ID'] : $linked_user_field;
    if ($linked_user_id && $linked_user_id == $current_user->ID) {
        $is_allowed = true;
    }
}

if (!$is_allowed) { wp_die('You do not have permission to view this skater dashboard.'); }

// --- Prepare Skater Profile Data ---
$dob_raw = get_field('date_of_birth', $skater_id);
$age = function_exists('spd_get_skater_age_as_of_july_1') ? spd_get_skater_age_as_of_july_1($dob_raw) : '—';

$skater_data = [
    'name'       => get_the_title($skater_id),
    'age'        => $age,
    'level'      => get_field('current_level', $skater_id),
    'federation' => get_field('federation', $skater_id),
    'club'       => get_field('home_club', $skater_id),
    'notes'      => get_field('notes', $skater_id),
    'edit_url'   => site_url('/edit-skater/' . $skater_id),
];

$gap_analysis_post = get_posts(['post_type' => 'gap_analysis', 'numberposts' => 1, 'meta_query' => [['key' => 'skater', 'value' => $skater_id]]]);

$GLOBALS['skater_id'] = $skater_id;
$GLOBALS['is_skater'] = $is_current_user_a_skater;

// --- 2. RENDER VIEW ---
?>

<div class="wrap coach-dashboard">

    <div class="page-header">
        <h1><?php echo esc_html($skater_data['name']); ?></h1>
        <?php if ($is_current_user_a_coach) : ?>
            <a class="button" href="<?php echo esc_url(site_url('/coach-dashboard')); ?>">&larr; Back to Coach Dashboard</a>
        <?php endif; ?>
    </div>

    <div class="dashboard-box">
        <ul style="list-style: none; padding-left: 0; margin: 0;">
            <?php if ($skater_data['level']) echo '<li><strong>Level:</strong> ' . esc_html($skater_data['level']) . '</li>'; ?>
            <?php if ($skater_data['age']) echo '<li><strong>Age (As of July 1):</strong> ' . esc_html($skater_data['age']) . '</li>'; ?>
            <?php if ($skater_data['federation']) echo '<li><strong>Federation:</strong> ' . esc_html($skater_data['federation']) . '</li>'; ?>
            <?php if ($skater_data['club']) echo '<li><strong>Home Club:</strong> ' . esc_html($skater_data['club']) . '</li>'; ?>
        </ul>

        <?php if ($skater_data['notes']) : ?>
            <div class="skater-notes" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <h4>Notes:</h4>
                <?php echo wp_kses_post($skater_data['notes']); ?>
            </div>
        <?php endif; ?>

        <div class="actions" style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <?php if ($is_current_user_a_coach) : ?>
                <a class="button" href="<?php echo esc_url($skater_data['edit_url']); ?>">Edit Skater Info</a>
                
                <?php if (!empty($gap_analysis_post)) : ?>
                    <a href="<?php echo esc_url(get_permalink($gap_analysis_post[0]->ID)); ?>">View Gap Analysis</a>
                <?php else : ?>
                    <a href="<?php echo esc_url(site_url('/create-gap-analysis/?skater_id=' . $skater_id)); ?>">Create Gap Analysis</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // --- Load all the individual section templates for this skater ---
    include plugin_dir_path(__FILE__) . '../sections/skater/competition-highlights.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/injury-log.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/yearly-plans-summary.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/weekly-plans-tracker.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/goals.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/competitions-upcoming.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/competitions-results.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/programs.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/session-logs.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/meeting-upcoming.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/missed-goals.php';
    ?>

</div>
