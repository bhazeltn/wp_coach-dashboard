<?php
/**
 * General-purpose utility functions for the Skater Planning Dashboard plugin.
 */

/**
 * Format a date string or timestamp as M j, Y.
 */
function coach_format_date($date_string) {
    $dt = DateTime::createFromFormat('Y-m-d', $date_string);
    return $dt ? $dt->format('M j, Y') : $date_string;
}

/**
 * Return a readable label for a post type (e.g., 'weekly_plan' → 'Weekly Plan').
 */
function coach_get_post_type_label($post_type) {
    $post_type_object = get_post_type_object($post_type);
    return $post_type_object ? $post_type_object->labels->singular_name : ucfirst(str_replace('_', ' ', $post_type));
}

/**
 * Get the title of a related post by ID, or fallback to [Untitled].
 */
function coach_get_post_title($post_id) {
    $title = get_the_title($post_id);
    return $title ?: '[Untitled]';
}

/**
 * Simple debug output (only visible to admins).
 */
function coach_debug($data) {
    if (current_user_can('administrator')) {
        echo '<pre style="background: #f6f6f6; padding: 1em; border: 1px solid #ddd;">';
        print_r($data);
        echo '</pre>';
    }
}

// --- YEARLY PLAN ---
add_filter('acf/pre_save_post', 'spd_set_yearly_plan_title_on_create', 1, 1);
function spd_set_yearly_plan_title_on_create($post_id) {
    if ($post_id !== 'new_post' || empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];
    $season  = $acf['field_681991c117f55'] ?? null;
    $skaters = $acf['field_681991e217f56'] ?? [];

    if ($season && is_array($skaters) && count($skaters) > 0) {
        $skater_name = get_the_title($skaters[0]);
        $_POST['acf']['_post_title'] = "{$season} – {$skater_name}";
        error_log("✅ Set Yearly Plan title: {$season} – {$skater_name}");
    }

    return $post_id;
}


// --- WEEKLY PLAN ---
add_filter('acf/pre_save_post', 'spd_set_weekly_plan_title_on_create', 1, 1);
function spd_set_weekly_plan_title_on_create($post_id) {
    if ($post_id !== 'new_post') {
        error_log("ℹ️ Weekly Plan: Not a new post, skipping: $post_id");
        return $post_id;
    }

    if (empty($_POST['acf'])) {
        error_log("❌ Weekly Plan: No ACF data in \$_POST");
        return $post_id;
    }

    $acf = $_POST['acf'];
    error_log("🔍 Weekly Plan: Raw ACF data: " . print_r($acf, true));

    $week_start = $acf['field_681c3d8e4e501'] ?? null;
    $skaters    = $acf['field_681c3d5d4e4ff'] ?? null;

    error_log("🔍 Weekly Plan: week_start = " . print_r($week_start, true));
    error_log("🔍 Weekly Plan: skaters = " . print_r($skaters, true));

    if ($week_start && is_array($skaters) && count($skaters) > 0) {
        $skater_id   = $skaters[0];
        $skater_name = get_the_title($skater_id);
        $formatted   = date('F j', strtotime($week_start));

        $_POST['acf']['_post_title'] = "Week of {$formatted} – {$skater_name}";
        error_log("✅ Set Weekly Plan title: Week of {$formatted} – {$skater_name}");
    } else {
        error_log("❌ Weekly Plan: Missing skater or week_start");
    }

    return $post_id;
}


// --- COMPETITION RESULT ---
add_filter('acf/pre_save_post', 'spd_set_competition_result_title_on_create', 1, 1);
function spd_set_competition_result_title_on_create($post_id) {
    if ($post_id !== 'new_post') return $post_id;
    if (empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];
    $skaters     = $acf['field_681c30ea1f0dd'] ?? null; // linked_skater
    $competition = $acf['field_681c31171f0de'] ?? null; // competition

    error_log("🔍 Competition Result: skaters = " . print_r($skaters, true));
    error_log("🔍 Competition Result: competition = " . print_r($competition, true));

    if (is_array($skaters) && count($skaters) > 0 && is_array($competition) && count($competition) > 0) {
        $skater_name = get_the_title($skaters[0]);
        $comp_name   = get_the_title($competition[0]);

        if ($skater_name && $comp_name) {
            $_POST['acf']['_post_title'] = "{$skater_name} – {$comp_name}";
            error_log("✅ Set Competition Result title: {$skater_name} – {$comp_name}");
        } else {
            error_log("❌ One or both post titles not found.");
        }
    } else {
        error_log("❌ Competition Result: Missing skater or competition");
    }

    return $post_id;
}


// --- MEETING LOG ---
add_filter('acf/pre_save_post', 'spd_set_meeting_log_title_on_create', 1, 1);
function spd_set_meeting_log_title_on_create($post_id) {
    if ($post_id !== 'new_post') return $post_id;
    if (empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];

    $skaters      = $acf['field_68242967056c1'] ?? null;
    $meeting_date = $acf['field_68242ae8056c2'] ?? null;
    $meeting_type = $acf['field_68242af6056c3'] ?? [];

    error_log("🔍 Meeting Log: skaters = " . print_r($skaters, true));
    error_log("🔍 Meeting Log: meeting_date = " . $meeting_date);
    error_log("🔍 Meeting Log: meeting_type = " . print_r($meeting_type, true));

    if (is_array($skaters) && count($skaters) > 0 && $meeting_date && !empty($meeting_type)) {
        $skater_name = get_the_title($skaters[0]);
        $type        = is_array($meeting_type) ? implode(', ', $meeting_type) : $meeting_type;
        $formatted_date = date('F j, Y', strtotime($meeting_date));
        $title = "{$skater_name} – {$type} – {$formatted_date}";

        $_POST['acf']['_post_title'] = $title;
        error_log("✅ Set Meeting Log title: $title");
    } else {
        error_log("❌ Meeting Log: Missing skater, meeting date, or type");
    }

    return $post_id;
}



// --- SKATER ---
add_filter('acf/pre_save_post', 'spd_set_skater_title_on_create', 1, 1);
function spd_set_skater_title_on_create($post_id) {
    if ($post_id !== 'new_post') return $post_id;
    if (empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];
    $full_name = $acf['field_681987201e176'] ?? null; // ✅ Correct key

    error_log("🔍 Skater: full_name = " . print_r($full_name, true));

    if ($full_name) {
        $_POST['acf']['_post_title'] = sanitize_text_field($full_name);
        error_log("✅ Set Skater title: {$full_name}");
    } else {
        error_log("❌ Skater: Missing full_name");
    }

    return $post_id;
}

// --- SESSION LOG ---
add_filter('acf/pre_save_post', 'spd_set_session_log_title_on_create', 1, 1);
function spd_set_session_log_title_on_create($post_id) {
    if ($post_id !== 'new_post') return $post_id;
    if (empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];
    $session_date = $acf['field_681c425a575e3'] ?? null;
    $skaters      = $acf['field_681c4232575e1'] ?? null;

    error_log("🔍 Session Log: session_date = " . print_r($session_date, true));
    error_log("🔍 Session Log: skaters = " . print_r($skaters, true));

    if ($session_date && is_array($skaters) && count($skaters) > 0) {
        $skater_id   = $skaters[0];
        $skater_name = get_the_title($skater_id);
        $formatted   = date('F j, Y', strtotime($session_date));
        $title       = "{$skater_name} – Session Log – {$formatted}";

        $_POST['acf']['_post_title'] = $title;
        error_log("✅ Set Session Log title: {$title}");
    } else {
        error_log("❌ Session Log: Missing skater or date");
    }

    return $post_id;
}

// --- GAP ANALYSIS ---
add_filter('acf/pre_save_post', 'spd_set_gap_analysis_title_on_create', 1, 1);
function spd_set_gap_analysis_title_on_create($post_id) {
    if ($post_id !== 'new_post') return $post_id;
    if (empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];
    $skater_id = $acf['field_gap_skater'] ?? null;

    error_log("🔍 Gap Analysis: skater_id = " . print_r($skater_id, true));

    if ($skater_id) {
        $skater_name = get_the_title($skater_id);
        $title = "Gap Analysis – {$skater_name}";

        $_POST['acf']['_post_title'] = $title;
        error_log("✅ Set Gap Analysis title: {$title}");
    } else {
        error_log("❌ Gap Analysis: Missing skater");
    }

    return $post_id;
}

// --- INJURY LOG ---
add_filter('acf/pre_save_post', 'spd_set_injury_log_title_on_create', 1, 1);
function spd_set_injury_log_title_on_create($post_id) {
    if ($post_id !== 'new_post') return $post_id;
    if (empty($_POST['acf'])) return $post_id;

    $acf = $_POST['acf'];
    $skaters     = $acf['field_68242bb0de87d'] ?? null; // relationship array
    $injury_date = $acf['field_68242c07de880'] ?? null; // Ymd date

    error_log("🔍 Injury Log: skater_id = " . print_r($skaters, true));
    error_log("🔍 Injury Log: injury_date = " . print_r($injury_date, true));

    if (is_array($skaters) && count($skaters) > 0 && $injury_date) {
        $skater_name = get_the_title($skaters[0]);
        $formatted   = date('M j, Y', strtotime($injury_date));
        $title = "Injury – {$skater_name} – {$formatted}";

        $_POST['acf']['_post_title'] = $title;
        error_log("✅ Set Injury Log title: {$title}");
    } else {
        error_log("❌ Injury Log: Missing skater or injury date");
    }

    return $post_id;
}

/**
 * Returns a country flag emoji based on a 3-letter federation code.
 *
 * @param string $federation_code The 3-letter country code (e.g., 'CAN', 'PHI', 'USA').
 * @return string The corresponding flag emoji or an empty string if not found.
 */
function spd_get_country_flag_emoji($federation_code) {
    // A mapping of federations to their flag emojis.
    // This list can be expanded as needed.
    $flags = [
        'CAN' => '🇨🇦', // Canada
        'PHI' => '🇵🇭', // Philippines
        'USA' => '🇺🇸', // United States
    ];

    // Ensure the code is uppercase for a case-insensitive match.
    $code = strtoupper($federation_code);

    // Return the flag if it exists in our array, otherwise return an empty string.
    return $flags[$code] ?? ''; 
}


/**
 * Calculates and formats a human-readable countdown to a future date.
 *
 * @param string $future_date_str A date string in 'Y-m-d' format.
 * @return string A formatted string like "in 3 weeks", "in 5 days", or "Today".
 */
function spd_get_countdown_string($future_date_str) {
    try {
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Set time to midnight to ensure accurate day comparison

        $future_date = new DateTime($future_date_str);
        $future_date->setTime(0, 0, 0);

        if ($today > $future_date) {
            return 'Past';
        }

        $interval = $today->diff($future_date);
        $days_left = $interval->days;

        if ($days_left == 0) {
            return 'Today';
        }

        if ($days_left < 7) {
            return 'in ' . $days_left . ($days_left == 1 ? ' day' : ' days');
        }

        $weeks_left = floor($days_left / 7);
        return 'in ' . $weeks_left . ($weeks_left == 1 ? ' week' : ' weeks');

    } catch (Exception $e) {
        // In case of an invalid date format
        return '—';
    }
}