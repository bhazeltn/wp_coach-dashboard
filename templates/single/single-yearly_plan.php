<?php
/**
 * Template: View Single Yearly Plan (Formatted)
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

if (!is_user_logged_in()) {
    auth_redirect();
}

global $post;
setup_postdata($post);

// === Yearly Plan Fields ===
$season = get_field('season');
$skaters = get_field('skater');
$season_dates = get_field('season_dates');
$goal = get_field('primary_season_goal');
$macrocycles = get_field('macrocycles');
$peak = get_field('peak_planning');
$eval = get_field('evaluation_strategy');
$notes = get_field('coach_notes');

// === Season Dates ===
$start_raw = $season_dates['start_date'] ?? '';
$end_raw = $season_dates['end_date'] ?? '';
$season_start = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw)->format('Ymd') : '';
$season_end = $end_raw ? DateTime::createFromFormat('d/m/Y', $end_raw)->format('Ymd') : '';
$start_fmt = $start_raw ? DateTime::createFromFormat('d/m/Y', $start_raw)->format('F j, Y') : '';
$end_fmt = $end_raw ? DateTime::createFromFormat('d/m/Y', $end_raw)->format('F j, Y') : '';

// === Skater Setup ===
$skater = null;
$skater_name = '';
$skater_slug = '';

if ($skaters && is_array($skaters)) {
    $skater = $skaters[0];
    if ($skater) {
        $skater_name = get_the_title($skater);
        $skater_slug = $skater->post_name;
    }
}

echo '<div class="wrap coach-dashboard">';
echo '<h1>Yearly Training Plan</h1>';

echo '<div class="dashboard-box">';
echo '<p><strong>Skater:</strong> <a href="' . esc_url(site_url('/skater/' . $skater_slug)) . '">' . esc_html($skater_name) . '</a></p>';
echo '<p><strong>Season:</strong> ' . esc_html($season);
if ($start_fmt || $end_fmt) {
    echo ' (' . esc_html($start_fmt . ' to ' . $end_fmt) . ')';
}
echo '</p>';
echo '</div>';

// === Primary Goal ===
if (!empty($goal)) {
    echo '<hr><div class="dashboard-box">';
    echo '<h3>🎯 Primary Season Goal</h3>';
    echo wp_kses_post($goal);
    echo '</div>';
}

// === Macrocycles ===
if (!empty($macrocycles)) {
    echo '<hr><div class="dashboard-box">';
    echo '<h3>📆 Macrocycle Breakdown</h3>';
    echo '<table class="dashboard-table"><thead><tr>';
    echo '<th>Phase</th><th>Focus</th><th>Start</th><th>End</th><th>Evaluation</th><th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($macrocycles as $cycle) {
        $start_fmt = $cycle['phase_start'] ? DateTime::createFromFormat('d/m/Y', $cycle['phase_start'])->format('M j') : '';
        $end_fmt = $cycle['phase_end'] ? DateTime::createFromFormat('d/m/Y', $cycle['phase_end'])->format('M j') : '';
        echo '<tr>';
        echo '<td>' . esc_html($cycle['phase_title']) . '</td>';
        echo '<td>' . esc_html($cycle['phase_focus']) . '</td>';
        echo '<td>' . esc_html($start_fmt) . '</td>';
        echo '<td>' . esc_html($end_fmt) . '</td>';
        echo '<td>' . wp_kses_post($cycle['evaluation_strategy']) . '</td>';
        echo '<td>' . wp_kses_post($cycle['coach_notes']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

// === Peak Planning ===
if (!empty($peak)) {
    $primary_event = $peak['primary_peak_event'][0] ?? null;
    $secondary_event = $peak['secondary_peak_event'][0] ?? null;

    echo '<hr><div class="dashboard-box">';
    echo '<h3>📌 Peak Planning</h3>';
    echo '<table class="dashboard-table">';
    echo '<thead><tr><th>Peak</th><th>Event</th><th>Dates</th></tr></thead><tbody>';

    foreach (['Primary' => $primary_event, 'Secondary' => $secondary_event] as $label => $event) {
        $name = $event ? get_the_title($event) : '—';
        $start_key = strtolower($label) . '_peak_start_date';
        $end_key = strtolower($label) . '_peak_end_date';
        $start = $peak[$start_key] ?? '';
        $end = $peak[$end_key] ?? '';
        $start_fmt = $start ? DateTime::createFromFormat('d/m/Y', $start)->format('M j') : '';
        $end_fmt = $end ? DateTime::createFromFormat('d/m/Y', $end)->format('M j') : '';
        $range = $start_fmt || $end_fmt ? trim($start_fmt . ' – ' . $end_fmt, ' –') : '—';
        echo '<tr><td>' . $label . '</td><td>' . esc_html($name) . '</td><td>' . esc_html($range) . '</td></tr>';
    }

    echo '</tbody></table></div>';
}

// === Weekly Plans Preview ===
$weekly_plans = new WP_Query([
    'post_type'      => 'weekly_plan',
    'posts_per_page' => 5,
    'meta_key'       => 'week_start',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => [
        [
            'key'     => 'related_yearly_plan',
            'value'   => '"' . get_the_ID() . '"',
            'compare' => 'LIKE',
        ]
    ],
]);

if ($weekly_plans->have_posts()) {
    echo '<hr><div class="dashboard-box">';
echo '<h3>📋 Weekly Plans</h3>';

if ($weekly_plans->have_posts()) {
    echo '<table class="dashboard-table"><thead><tr><th>Week Starting</th><th>Theme</th><th>Actions</th></tr></thead><tbody>';
    while ($weekly_plans->have_posts()) {
        $weekly_plans->the_post();
        $week_start = get_field('week_start');
        $date_fmt = $week_start ? DateTime::createFromFormat('d/m/Y', $week_start)->format('M j, Y') : '';
        echo '<tr>';
        echo '<td>' . esc_html($date_fmt) . '</td>';
        echo '<td>' . esc_html(get_field('theme')) . '</td>';
        echo '<td><a class="button-small" href="' . get_permalink() . '">View</a> | ';
        echo '<a class="button-small" href="' . site_url('/edit-weekly-plan/' . get_the_ID()) . '">Update</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No Weekly Plans created yet.</p>';
}

echo '<p><a class="button" href="' . esc_url(admin_url('post-new.php?post_type=weekly_plan')) . '">Add Weekly Plan</a></p>';
echo '</div>';

wp_reset_postdata();

}

// === Validate Skater before Competitions ===
if (!$skater || !is_object($skater)) {
    echo '<div class="dashboard-box"><strong>Error:</strong> Skater not linked properly to this Yearly Plan.</div>';
    get_footer();
    return;
}

// === Competition Result & Upcoming Logic ===

$results_query = get_posts([
    'post_type'   => 'competition_result',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_query'  => [[
        'key'     => 'linked_skater',
        'value'   => '"' . $skater->ID . '"',
        'compare' => 'LIKE',
    ]],
]);

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
    $today = date('Ymd');

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

// === Output Upcoming Competitions ===
echo '<hr><div class="dashboard-box">';
echo '<h3>📅 Upcoming Competitions</h3>';

if (!empty($upcoming)) {
    echo '<table class="dashboard-table"><thead><tr><th>Name</th><th>Level</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
    foreach ($upcoming as $comp) {
        $d = $comp['date'] ? date_create($comp['date']) : null;
        echo '<tr>';
        echo '<td>' . esc_html($comp['name']) . '</td>';
        echo '<td>' . esc_html($comp['level'] ?? '—') . '</td>';
        echo '<td>' . ($d ? esc_html($d->format('M j, Y')) : '—') . '</td>';
        echo '<td><a class="button-small" href="' . esc_url($comp['edit']) . '">Update</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No Upcoming Competitions Planned. Please <a href="' . esc_url(admin_url('post-new.php?post_type=competition_result')) . '">add competitions for this skater</a>.</p>';
}
echo '<p><a class="button" href="' . esc_url(admin_url('post-new.php?post_type=competition_result')) . '">Add Competition</a></p>';
echo '</div>';


// === Output Completed Results ===
echo '<hr><div class="dashboard-box">';
echo '<h3>🏆 Competition Results</h3>';

if (!empty($completed)) {
    echo '<table class="dashboard-table"><thead><tr><th>Event</th><th>Level</th><th>Date</th><th>Total</th><th>Actions</th></tr></thead><tbody>';
    foreach ($completed as $comp) {
        $d = $comp['date'] ? date_create($comp['date']) : null;
        echo '<tr>';
        echo '<td>' . esc_html($comp['name']) . '</td>';
        echo '<td>' . esc_html($comp['level'] ?? '—') . '</td>';
        echo '<td>' . ($d ? esc_html($d->format('M j, Y')) : '—') . '</td>';
        echo '<td>' . esc_html($comp['total'] ?? '—') . '</td>';
        echo '<td><a class="button-small" href="' . esc_url($comp['result']) . '">View</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No Competition Results Yet This Season.</p>';
}
echo '<p><a class="button" href="' . esc_url(admin_url('post-new.php?post_type=competition_result')) . '">Add Competition Result</a></p>';
echo '</div>';

wp_reset_postdata();

// === Goals Section ===
echo '<hr><div class="dashboard-box">';
echo '<h3>🎯 Goals for This Season</h3>';

// Add Goal Button
echo '<p><a class="button" href="' . esc_url(site_url('/create-goal?skater_id=' . $skater->ID)) . '">Add Goal</a></p>';

// Load all goals linked to this skater
$goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'skater',
        'value'   => '"' . $skater->ID . '"',
        'compare' => 'LIKE',
    ]]
]);

$today = date('Ymd');
$seasonal = [];
$medium = [];
$longterm = [];
$overdue = [];

foreach ($goals as $goal) {
    $status        = get_field('current_status', $goal->ID);
    $timeframe_raw = get_field('goal_timeframe', $goal->ID);
    $timeframes    = is_array($timeframe_raw) ? $timeframe_raw : [$timeframe_raw];
    $target_raw    = get_field('target_date', $goal->ID);
    $target_ymd    = null;

    if ($target_raw) {
        $dt = DateTime::createFromFormat('d/m/Y', $target_raw);
        $target_ymd = $dt ? $dt->format('Ymd') : null;
    }

    $is_overdue = ($target_ymd && $target_ymd < $today && $status !== 'Achieved');
    $in_season  = $target_ymd && $target_ymd >= $season_start && $target_ymd <= $season_end;

    // Always include overdue goals
    if ($is_overdue) {
        $overdue[] = $goal;
    }

    // Skip goals not due in season or overdue
    if (!$in_season && !$is_overdue) {
        continue;
    }

    foreach ($timeframes as $timeframe) {
        if ($timeframe === 'season') {
            $seasonal[] = $goal;
        } elseif ($timeframe === 'medium') {
            $medium[] = $goal;
        } elseif ($timeframe === 'long') {
            $longterm[] = $goal;
        }
    }
}

// === Helper: Render goal table
function render_goal_table($goals, $today, $highlight_overdue = true) {
    echo '<table class="dashboard-table"><thead><tr>
        <th>Goal</th><th>Timeframe</th><th>Status</th><th>Target Date</th><th>Actions</th>
    </tr></thead><tbody>';

    foreach ($goals as $goal) {
        $title       = get_the_title($goal->ID) ?: '[Untitled]';
        $timeframes  = get_field('goal_timeframe', $goal->ID);
        $status      = get_field('current_status', $goal->ID) ?: '—';
        $target_raw  = get_field('target_date', $goal->ID);
        $target_disp = '—';
        $is_overdue  = false;

        if ($target_raw) {
            $dt = DateTime::createFromFormat('d/m/Y', $target_raw);
            if ($dt) {
                $target_disp = $dt->format('F j, Y');
                $target_ymd = $dt->format('Ymd');
                $is_overdue = ($target_ymd < $today && $status !== 'Achieved');
            }
        }

        $row_class = ($highlight_overdue && $is_overdue) ? ' style="color: red; font-weight: bold;"' : '';
        $tf_disp = is_array($timeframes)
            ? implode(', ', array_map('ucfirst', $timeframes))
            : ucfirst($timeframes);

        echo '<tr' . $row_class . '>';
        echo '<td>' . esc_html($title) . ($is_overdue ? ' ⚠ Past Due' : '') . '</td>';
        echo '<td>' . esc_html($tf_disp) . '</td>';
        echo '<td>' . esc_html($status) . '</td>';
        echo '<td>' . esc_html($target_disp) . '</td>';
        echo '<td><a href="' . esc_url(get_permalink($goal->ID)) . '">View</a> | ';
        echo '<a href="' . esc_url(site_url('/edit-goal?goal_id=' . $goal->ID)) . '">Update</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}

// === Overdue Goals Section
if (!empty($overdue)) {
    echo '<h4 style="color: red;">⚠ Past Due Goals</h4>';
    render_goal_table($overdue, $today);
}

// === Seasonal Goals
if (!empty($seasonal)) {
    echo '<h4>Seasonal Goals</h4>';
    render_goal_table($seasonal, $today);
}

// === Medium Term Goals
if (!empty($medium)) {
    echo '<h4>Medium Term Goals</h4>';
    render_goal_table($medium, $today);
}

// === Long Term Goals (due this season)
if (!empty($longterm)) {
    echo '<h4>Long Term Goals Due This Season</h4>';
    render_goal_table($longterm, $today);
}

// === Fallback Message
if (empty($seasonal) && empty($medium) && empty($longterm) && empty($overdue)) {
    echo '<p>No seasonal, medium term, or long term goals found for this skater.</p>';
}

echo '<p style="margin-top: 1em;">Weekly goals are available in the <a href="#weekly-plans">Weekly Plan section</a>.</p>';
echo '</div>';

wp_reset_postdata();

// === Meetings This Season ===
echo '<hr><div class="dashboard-box">';
echo '<h3>📅 Meetings This Season</h3>';
echo '<p><a class="button" href="' . esc_url(site_url('/create-meeting-log?skater_id=' . $skater->ID)) . '">Add Meeting</a></p>';

$meeting_query = new WP_Query([
    'post_type'      => 'meeting_log',
    'posts_per_page' => -1,
    'meta_key'       => 'meeting_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => 'meeting_date',
            'value'   => [$season_start, $season_end],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ],
        [
            'key'     => 'skater',
            'value'   => '"' . $skater->ID . '"',
            'compare' => 'LIKE',
        ]
    ],
]);

if ($meeting_query->have_posts()) {
    echo '<table class="dashboard-table"><thead><tr>
        <th>Date</th><th>Title</th><th>Type(s)</th><th>Actions</th>
    </tr></thead><tbody>';

    while ($meeting_query->have_posts()) {
        $meeting_query->the_post();

        $title = get_the_title();
        if (!$title) {
            $title = get_field('meeting_title');
        }

        $meeting_date = get_field('meeting_date');
        $date_fmt = $meeting_date
            ? DateTime::createFromFormat('d/m/Y', $meeting_date)->format('M j, Y')
            : '—';

        $types_raw = get_field('meeting_type');
        $types = is_array($types_raw) ? implode(', ', $types_raw) : ($types_raw ?: '—');

        echo '<tr>';
        echo '<td>' . esc_html($date_fmt) . '</td>';
        echo '<td>' . esc_html($title) . '</td>';
        echo '<td>' . esc_html($types) . '</td>';
        echo '<td><a class="button-small" href="' . esc_url(get_permalink()) . '">View</a> | ';
        echo '<a class="button-small" href="' . esc_url(site_url('/edit-meeting-log/' . get_the_ID())) . '">Update</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No meetings recorded during this training season.</p>';
}

echo '</div>';
wp_reset_postdata();

// === Injury Log This Season ===
echo '<hr><div class="dashboard-box">';
echo '<h3>🩹 Injury & Health Log</h3>';

echo '<p><a class="button" href="' . esc_url(site_url('/create-injury-log?skater_id=' . $skater->ID)) . '">Add Injury Log</a></p>';

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

$today = date('Ymd');
$season_injuries = [];

foreach ($injury_logs as $log) {
    $log_id = $log->ID;

    $onset_raw = get_field('date_of_onset', $log_id);
    $recovery_raw = get_field('date_cleared', $log_id);
    $status = get_field('recovery_status', $log_id);
    $status_value = is_array($status) ? ($status['value'] ?? '') : sanitize_title($status);

    $onset_ymd = $onset_raw ? DateTime::createFromFormat('d/m/Y', $onset_raw)?->format('Ymd') : null;
    $recovery_ymd = $recovery_raw ? DateTime::createFromFormat('d/m/Y', $recovery_raw)?->format('Ymd') : null;

    $still_active = !$recovery_ymd && $status_value !== 'cleared';

    $during_season =
        ($onset_ymd && $onset_ymd >= $season_start && $onset_ymd <= $season_end) ||
        ($recovery_ymd && $recovery_ymd >= $season_start && $recovery_ymd <= $season_end) ||
        ($onset_ymd && $onset_ymd <= $season_end && $still_active);

    if ($during_season) {
        $season_injuries[] = $log;
    }
}

if (empty($season_injuries)) {
    echo '<p>No injuries recorded during this training season.</p>';
} else {
    echo '<table class="dashboard-table">';
    echo '<thead><tr>
        <th>Status</th>
        <th>Onset</th>
        <th>Return</th>
        <th>Severity</th>
        <th>Body Area</th>
        <th>Actions</th>
    </tr></thead><tbody>';

    foreach ($season_injuries as $log) {
        $log_id = $log->ID;

        $onset_raw = get_field('date_of_onset', $log_id);
        $onset = DateTime::createFromFormat('d/m/Y', $onset_raw);
        $onset_display = $onset ? $onset->format('M j, Y') : '—';

        $severity = get_field('severity', $log_id);
        $severity_display = is_array($severity) ? ($severity['label'] ?? '—') : ($severity ?: '—');

        $body_area = get_field('body_area', $log_id);
        $body_area_display = is_array($body_area) ? implode(', ', $body_area) : ($body_area ?: '—');

        $recovery_raw = get_field('date_cleared', $log_id);
        $recovery = $recovery_raw ? DateTime::createFromFormat('d/m/Y', $recovery_raw) : null;
        $recovery_display = $recovery ? $recovery->format('M j, Y') : '—';


        $status = get_field('recovery_status', $log_id);
        $status_value = is_array($status) ? ($status['value'] ?? '') : sanitize_title($status);
        $status_label = is_array($status) ? ($status['label'] ?? '—') : ($status ?: '—');

        $colors = [
            'cleared'     => '#3c763d', // green
            'limited'     => '#e67e22', // orange
            'modified'    => '#3498db', // blue
            'resting'     => '#c0392b', // red
            'rehab_only'  => '#9b59b6', // purple
        ];
        $dot_color = $colors[$status_value] ?? '#999';

        $view_link = get_permalink($log_id);
        $edit_link = site_url('/edit-injury-log/' . $log_id);

        echo '<tr>';
        echo '<td><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:' . esc_attr($dot_color) . ';margin-right:6px;"></span>' . esc_html($status_label) . '</td>';
        echo '<td>' . esc_html($onset_display) . '</td>';
        echo '<td>' . esc_html($recovery_display) . '</td>';
        echo '<td>' . esc_html($severity_display) . '</td>';
        echo '<td>' . esc_html($body_area_display) . '</td>';
        echo '<td><a href="' . esc_url($view_link) . '">View</a> | <a href="' . esc_url($edit_link) . '">Update</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}

echo '</div>';





echo '<hr />';
echo '<p><a class="button" href="' . esc_url(site_url('/edit-yearly-plan/' . get_the_ID())) . '">Update This Plan</a></p>';
echo '<p><a class="button" href="' . esc_url(site_url('/coach-dashboard')) . '">Back to Dashboard</a></p>';
echo '</div>';

get_footer();
