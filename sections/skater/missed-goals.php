<?php
// --- Missed or Stalled Goals ---
$skater_id = $GLOBALS['skater_id'] ?? null;

echo '<h2>Missed or Stalled Goals</h2>';

// Get all goals for this skater
$goals = get_posts([
    'post_type'   => 'goal',
    'numberposts' => -1,
    'post_status' => 'publish',
    'meta_key'    => 'target_date',
    'orderby'     => 'meta_value',
    'order'       => 'ASC',
    'meta_query'  => [[
        'key'     => 'linked_skater',
        'value'   => '"' . $skater_id . '"',
        'compare' => 'LIKE',
    ]]
]);

$missed = [];
$today = new DateTime();

foreach ($goals as $goal) {
    $status      = get_field('goal_status', $goal->ID);
    $target_date = get_field('target_date', $goal->ID);

    $is_stalled = in_array($status, ['On Hold', 'Abandoned']);
    $is_missed  = $target_date && $target_date < $today->format('Y-m-d') && $status !== 'Achieved';

    if ($is_stalled || $is_missed) {
        $missed[] = [
            'goal'        => $goal,
            'status'      => $status,
            'target_date' => $target_date,
        ];
    }
}

if ($missed) {
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>Goal</th>
            <th>Status</th>
            <th>Target Date</th>
            <th>Time Overdue</th>
        </tr></thead><tbody>';

    foreach ($missed as $entry) {
        $goal        = $entry['goal'];
        $title       = get_the_title($goal->ID) ?: '[Untitled]';
        $status      = $entry['status'] ?: '—';
        $target_date = $entry['target_date'] ?: '—';

        // Calculate days overdue
        $overdue = '—';
        if ($target_date && $status !== 'Achieved') {
            $target = DateTime::createFromFormat('Y-m-d', $target_date);
            if ($target && $target < $today) {
                $interval = $target->diff($today);
                $overdue = $interval->days . ' day' . ($interval->days !== 1 ? 's' : '');
            }
        }

        echo '<tr>
            <td>' . esc_html($title) . '</td>
            <td>' . esc_html($status) . '</td>
            <td>' . esc_html($target_date) . '</td>
            <td>' . esc_html($overdue) . '</td>
        </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No missed or stalled goals found.</p>';
}
