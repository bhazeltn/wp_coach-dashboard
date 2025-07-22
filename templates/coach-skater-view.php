<?php
/**
 * Template: Individual Skater Dashboard
 * This is the main template file that structures the page.
 */

// --- 1. INITIAL SETUP & PERMISSION CHECK ---

include plugin_dir_path(__FILE__) . 'partials/header-dashboard.php';

$skater_slug = get_query_var('skater_view');
$skater_post = get_page_by_path($skater_slug, OBJECT, 'skater');

if (!$skater_post) { wp_die('Skater not found.'); }

$skater_id = $skater_post->ID;
$current_user = wp_get_current_user();
$user_roles = (array) $current_user->roles;
$is_current_user_a_skater = in_array('skater', $user_roles);
$is_current_user_a_coach = in_array('coach', $user_roles) || in_array('administrator', $user_roles);

$is_allowed = false;
if ($is_current_user_a_coach) {
    $is_allowed = true;
} elseif ($is_current_user_a_skater) {
    $linked_user_field = get_field('skater_account', $skater_id);
    $linked_user_id = is_array($linked_user_field) ? ($linked_user_field['ID'] ?? null) : $linked_user_field;
    if ($linked_user_id && $linked_user_id == $current_user->ID) {
        $is_allowed = true;
    }
}

if (!$is_allowed) { wp_die('You do not have permission to view this skater dashboard.'); }

// Set globals for use in template parts
$GLOBALS['skater_id'] = $skater_id;
$GLOBALS['is_skater'] = $is_current_user_a_skater;

// --- 2. RENDER VIEW ---
?>

<div class="wrap coach-dashboard">

    <div class="page-header">
        <h1><?php echo esc_html(get_the_title($skater_id)); ?></h1>
        <?php if ($is_current_user_a_coach) : ?>
            <a class="button" href="<?php echo esc_url(site_url('/coach-dashboard')); ?>">&larr; Back to Coach Dashboard</a>
        <?php endif; ?>
    </div>

    <?php
    // Load the new, separate profile template part
    include plugin_dir_path(__FILE__) . '../sections/skater/skater-profile.php';

    // Load all the other section templates for this skater
    include plugin_dir_path(__FILE__) . '../sections/skater/injury-log.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/yearly-plans-summary.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/weekly-plans-tracker.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/goals.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/competitions-upcoming.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/competitions-results.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/programs.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/session-logs.php';
    include plugin_dir_path(__FILE__) . '../sections/skater/meeting-upcoming.php';

    ?>

</div>

<?php include plugin_dir_path(__FILE__) . 'partials/footer.php'; ?>
