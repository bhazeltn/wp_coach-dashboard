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

/**
 * Calculates a skater's age as of July 1st for the current skating season.
 *
 * @param string $dob_raw The date of birth string from ACF (e.g., 'd/m/Y').
 * @return int|string The calculated age as an integer, or '—' if invalid.
 */
function spd_get_skater_age_as_of_july_1($dob_raw) {
    if (!$dob_raw) {
        return '—';
    }

    try {
        $dob = DateTime::createFromFormat('d/m/Y', $dob_raw);
        if (!$dob) {
            return '—'; // Handle potential parsing errors
        }

        $current_date = new DateTime();
        $current_year = (int) $current_date->format('Y');
        $july_1_this_year = new DateTime($current_year . '-07-01');

        // Determine the correct July 1st to use for the current season.
        // If we are before July 1st of the current year, the season is still the previous one.
        if ($current_date < $july_1_this_year) {
            $season_july_1 = $july_1_this_year->modify('-1 year');
        } else {
            $season_july_1 = $july_1_this_year;
        }

        $age = $season_july_1->diff($dob)->y;

        return $age;

    } catch (Exception $e) {
        return '—'; // Return a fallback on any error
    }
}

/**
 * Fetches the CTES requirements from the most recent 'CTES Requirement' post.
 *
 * @return array An array of the CTES values, or an empty array if not found.
 */
function spd_get_current_season_ctes() {
    $args = [
        'post_type'      => 'ctes_requirement',
        'posts_per_page' => 1,
        'orderby'        => 'date', // More robust: sort by creation date
        'order'          => 'DESC', // Get the most recently created one
    ];
    $ctes_posts = get_posts($args);

    if (empty($ctes_posts)) {
        return [];
    }
    
    $post_id = $ctes_posts[0]->ID;
    
    // Fetches all fields from the ACF group for this post
    return get_fields($post_id);
}


/**
 * Calculates a skater's PB, SB, and CTES with detailed segment breakdowns.
 * This version returns competition IDs for creating links.
 *
 * @param int $skater_id The ID of the skater.
 * @return array A structured array with detailed performance data.
 */
function spd_get_skater_performance_summary($skater_id) {
    $summary = [
        'personal_best' => [
            'total' => ['score' => 0, 'competition' => 'N/A'],
            'short' => ['score' => 0, 'competition' => 'N/A'],
            'free'  => ['score' => 0, 'competition' => 'N/A'],
        ],
        'season_best'   => [
            'total' => ['score' => 0, 'competition' => 'N/A'],
            'short' => ['score' => 0, 'competition' => 'N/A'],
            'free'  => ['score' => 0, 'competition' => 'N/A'],
        ],
        'ctes'          => [
            'score'       => 0,
            'short_tes'   => 0,
            'short_comp'  => 'N/A',
            'short_comp_id' => 0,
            'free_tes'    => 0,
            'free_comp'   => 'N/A',
            'free_comp_id'  => 0,
        ],
    ];

    $results = get_posts([
        'post_type'   => 'competition_result',
        'numberposts' => -1,
        'meta_query'  => [['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE']],
    ]);

    if (empty($results)) {
        return $summary;
    }

    $valid_isu_comp_types = ['ISU International', 'Grand Prix', 'ISU Championships'];

    $today = new DateTime();
    $july_1_this_year = new DateTime(date('Y') . '-07-01');
    $season_start = ($today < $july_1_this_year) ? (clone $july_1_this_year)->modify('-1 year') : $july_1_this_year;
    $window_start = (clone $season_start)->modify('-1 year');

    foreach ($results as $result) {
        $competition_post_array = get_field('linked_competition', $result->ID);
        if (empty($competition_post_array[0])) continue;
        
        $competition_post = $competition_post_array[0];
        $comp_date_raw = get_field('competition_date', $competition_post->ID);
        if (empty($comp_date_raw)) continue;

        $comp_date = new DateTime($comp_date_raw);
        $comp_type = get_field('competition_type', $competition_post->ID);
        $comp_name = get_the_title($competition_post);
        
        $comp_score = get_field('comp_score', $result->ID) ?: [];
        $sp_score_place = get_field('sp_score_place', $result->ID) ?: [];
        $fs_score_field = get_field('fs_score', $result->ID) ?: [];
        
        $total_score = floatval($comp_score['total_competition_score'] ?? 0);
        $sp_total = floatval($sp_score_place['short_program_score'] ?? 0);
        $fs_total = floatval($fs_score_field['free_program_score'] ?? 0);

        if ($total_score > $summary['personal_best']['total']['score']) {
            $summary['personal_best']['total']['score'] = $total_score;
            $summary['personal_best']['total']['competition'] = $comp_name;
        }
        if ($sp_total > $summary['personal_best']['short']['score']) {
            $summary['personal_best']['short']['score'] = $sp_total;
            $summary['personal_best']['short']['competition'] = $comp_name;
        }
        if ($fs_total > $summary['personal_best']['free']['score']) {
            $summary['personal_best']['free']['score'] = $fs_total;
            $summary['personal_best']['free']['competition'] = $comp_name;
        }

        if ($comp_date >= $season_start) {
            if ($total_score > $summary['season_best']['total']['score']) {
                $summary['season_best']['total']['score'] = $total_score;
                $summary['season_best']['total']['competition'] = $comp_name;
            }
            if ($sp_total > $summary['season_best']['short']['score']) {
                $summary['season_best']['short']['score'] = $sp_total;
                $summary['season_best']['short']['competition'] = $comp_name;
            }
            if ($fs_total > $summary['season_best']['free']['score']) {
                $summary['season_best']['free']['score'] = $fs_total;
                $summary['season_best']['free']['competition'] = $comp_name;
            }
        }
        
        if (in_array($comp_type, $valid_isu_comp_types) && $comp_date >= $window_start) {
            $scores = get_field('scores', $result->ID) ?: [];
            $fs_scores = get_field('fs_scores', $result->ID) ?: [];
            $sp_tes = floatval($scores['tes_sp'] ?? 0);
            $fs_tes = floatval($fs_scores['tes_fs'] ?? 0);

            if ($sp_tes > $summary['ctes']['short_tes']) {
                $summary['ctes']['short_tes'] = $sp_tes;
                $summary['ctes']['short_comp'] = $comp_name;
                $summary['ctes']['short_comp_id'] = $competition_post->ID;
            }
            if ($fs_tes > $summary['ctes']['free_tes']) {
                $summary['ctes']['free_tes'] = $fs_tes;
                $summary['ctes']['free_comp'] = $comp_name;
                $summary['ctes']['free_comp_id'] = $competition_post->ID;
            }
        }
    }

    $summary['ctes']['score'] = $summary['ctes']['short_tes'] + $summary['ctes']['free_tes'];

    return $summary;
}





/**
 * Calculates a skater's progress towards their custom TES targets.
 *
 * @param int $skater_id The ID of the skater.
 * @return array A structured array with the progress for each custom target.
 */
function spd_get_custom_tes_targets_progress($skater_id) {
    $custom_targets = get_field('minimum_scores', $skater_id);
    $progress_data = [];

    if (empty($custom_targets)) {
        return $progress_data;
    }

    // Fetch all competition results for the skater just once for efficiency.
    $results = get_posts([
        'post_type'   => 'competition_result',
        'numberposts' => -1,
        'meta_query'  => [['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE']],
    ]);

    if (empty($results)) {
        // If there are no results, we can't have achieved any targets.
        // Still, we should return the targets so the UI can show 0 progress.
        foreach ($custom_targets as $target) {
             $progress_data[] = [
                'name' => $target['event'] ?: 'Untitled Target',
                'target_score' => floatval($target['minimum_technical_score']),
                'achieved_score' => 0,
            ];
        }
        return $progress_data;
    }

    // Loop through each custom target defined for the skater.
    foreach ($custom_targets as $target) {
        $target_score = floatval($target['minimum_technical_score']);
        $start_date_obj = !empty($target['start_date']) ? DateTime::createFromFormat('d/m/Y', $target['start_date']) : null;
        $end_date_obj = !empty($target['end_date']) ? DateTime::createFromFormat('d/m/Y', $target['end_date']) : null;
        
        $highest_tes_in_range = 0;

        // Now, loop through the skater's results to find the best score for this target.
        foreach ($results as $result) {
            $competition_post_array = get_field('linked_competition', $result->ID);
            if (empty($competition_post_array[0])) continue;

            $competition_post = $competition_post_array[0];
            $comp_date_raw = get_field('competition_date', $competition_post->ID);
            if (empty($comp_date_raw)) continue;

            $comp_date_obj = DateTime::createFromFormat('Y-m-d', $comp_date_raw);

            // Check if the competition date is within the target's valid range.
            $is_in_range = true; // Assume it's valid unless proven otherwise.
            if ($start_date_obj && $comp_date_obj < $start_date_obj) {
                $is_in_range = false;
            }
            if ($end_date_obj && $comp_date_obj > $end_date_obj) {
                $is_in_range = false;
            }

            if ($is_in_range) {
                // Find the highest TES from either segment in this competition.
                $scores = get_field('scores', $result->ID);
                $fs_scores = get_field('fs_scores', $result->ID);
                $sp_tes = isset($scores['tes_sp']) ? floatval($scores['tes_sp']) : 0;
                $fs_tes = isset($fs_scores['tes_fs']) ? floatval($fs_scores['tes_fs']) : 0;
                
                $highest_in_comp = max($sp_tes, $fs_tes);

                // If this is the best score we've found so far for this target, update it.
                if ($highest_in_comp > $highest_tes_in_range) {
                    $highest_tes_in_range = $highest_in_comp;
                }
            }
        }

        // Add the calculated progress to our results array.
        $progress_data[] = [
            'name' => $target['event'] ?: 'Untitled Target',
            'target_score' => $target_score,
            'achieved_score' => $highest_tes_in_range,
        ];
    }

    return $progress_data;
}

/**
 * Finds and categorizes a skater's notable competition accomplishments.
 *
 * @param int $skater_id The ID of the skater.
 * @return array A structured array with 'major' and 'other' accomplishments.
 */
function spd_get_notable_accomplishments($skater_id) {
    $accomplishments = [
        'major' => [],
        'other' => [],
    ];

    $results = get_posts([
        'post_type'   => 'competition_result',
        'numberposts' => -1,
        'meta_query'  => [['key' => 'skater', 'value' => '"' . $skater_id . '"', 'compare' => 'LIKE']],
    ]);

    if (empty($results)) {
        return $accomplishments;
    }

    $major_event_types = [
        'ISU Championships',
        'National Championships',
        'Provincial/State Championships',
        'Grand Prix',
    ];

    $medal_emojis = [
        '1' => '🥇',
        '2' => '🥈',
        '3' => '🥉',
    ];

    foreach ($results as $result) {
        $comp_score = get_field('comp_score', $result->ID) ?: [];
        $placement = $comp_score['placement'] ?? null;

        if (!$placement) {
            continue;
        }

        $competition_post_array = get_field('linked_competition', $result->ID);
        if (empty($competition_post_array[0])) continue;

        $competition_post = $competition_post_array[0];
        $comp_type = get_field('competition_type', $competition_post->ID);
        $comp_name = get_the_title($competition_post);

        $is_major_podium = in_array($comp_type, $major_event_types) && in_array($placement, ['1', '2', '3']);
        $is_other_win = ($placement == '1');

        $medal = $medal_emojis[$placement] ?? '';
        $formatted_string = trim($medal . ' ' . $placement . 'st/nd/rd/th Place - ' . $comp_name);
        // Basic grammar correction for 1st, 2nd, 3rd
        $formatted_string = str_replace(['1st/nd/rd/th', '2st/nd/rd/th', '3st/nd/rd/th'], ['1st', '2nd', '3rd'], $formatted_string);


        if ($is_major_podium) {
            $accomplishments['major'][] = $formatted_string;
        } elseif ($is_other_win) {
            $accomplishments['other'][] = $formatted_string;
        }
    }

    return $accomplishments;
}