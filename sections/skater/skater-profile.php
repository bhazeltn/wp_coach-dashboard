<?php
/**
 * Template Part: Skater Profile
 * Displays the main information box on the individual skater dashboard.
 */

// --- 1. PREPARE DATA ---

// These variables are inherited from the main coach-skater-view.php template
global $skater_id, $is_skater;

// --- Prepare Skater Profile Data ---
$dob_raw = get_field('date_of_birth', $skater_id);
$age = function_exists('spd_get_skater_age_as_of_july_1') ? spd_get_skater_age_as_of_july_1($dob_raw) : '—';

$performance_summary = function_exists('spd_get_skater_performance_summary') ? spd_get_skater_performance_summary($skater_id) : null;
$ctes_requirements = function_exists('spd_get_current_season_ctes') ? spd_get_current_season_ctes() : null;
$custom_tes_targets = function_exists('spd_get_custom_tes_targets_progress') ? spd_get_custom_tes_targets_progress($skater_id) : null;
$accomplishments = function_exists('spd_get_notable_accomplishments') ? spd_get_notable_accomplishments($skater_id) : null;

// Determine the specific ISU CTES requirement(s) for this skater
$skater_level = get_field('current_level', $skater_id);
$skater_gender = get_field('gender', $skater_id);
$isu_ctes_targets = [];

if (!empty($ctes_requirements) && !empty($skater_level) && !empty($skater_gender)) {
    $level_lower = strtolower($skater_level);
    $gender_lower = strtolower($skater_gender);

    if ($level_lower === 'senior') {
        if ($gender_lower === 'male' && isset($ctes_requirements['cont_men'])) $isu_ctes_targets['Euros/4CC'] = $ctes_requirements['cont_men'];
        if ($gender_lower === 'female' && isset($ctes_requirements['cont_women'])) $isu_ctes_targets['Euros/4CC'] = $ctes_requirements['cont_women'];
        if ($gender_lower === 'male' && isset($ctes_requirements['worlds_men'])) $isu_ctes_targets['Worlds'] = $ctes_requirements['worlds_men'];
        if ($gender_lower === 'female' && isset($ctes_requirements['worlds_women'])) $isu_ctes_targets['Worlds'] = $ctes_requirements['worlds_women'];
    } elseif ($level_lower === 'junior') {
        if ($gender_lower === 'male' && isset($ctes_requirements['junior_worlds_men'])) $isu_ctes_targets['Jr Worlds'] = $ctes_requirements['junior_worlds_men'];
        if ($gender_lower === 'female' && isset($ctes_requirements['junior_worlds_women'])) $isu_ctes_targets['Jr Worlds'] = $ctes_requirements['junior_worlds_women'];
    }
}

$skater_data = [
    'age'            => $age,
    'level'          => $skater_level,
    'federation'     => get_field('federation', $skater_id),
    'club'           => get_field('home_club', $skater_id),
    'accomplishments' => $accomplishments,
    'edit_url'       => site_url('/edit-skater/' . $skater_id),
    'performance'    => $performance_summary,
    'isu_ctes_targets'   => $isu_ctes_targets,
    'custom_tes_targets' => $custom_tes_targets,
];

$gap_analysis_post = get_posts(['post_type' => 'gap_analysis', 'numberposts' => 1, 'meta_query' => [['key' => 'skater', 'value' => $skater_id]]]);

// --- 2. RENDER VIEW ---
?>

<div class="dashboard-box">
    <div class="skater-profile-grid">
        <div class="profile-details">
            <h4>Skater Profile</h4>
            <ul class="profile-list">
                <li><strong>Age (as of July 1):</strong> <?php echo esc_html($skater_data['age']); ?></li>
                <li><strong>Level:</strong> <?php echo esc_html($skater_data['level']); ?></li>
                <li><strong>Federation:</strong> <?php echo esc_html($skater_data['federation']); ?></li>
                <li><strong>Home Club:</strong> <?php echo esc_html($skater_data['club']); ?></li>
            </ul>
        </div>
        <div class="profile-performance">
            <h4>Performance Metrics</h4>
            <?php if ($skater_data['performance']) : ?>
                <ul class="profile-list">
                    <li>
                        <strong>Personal Bests:</strong>
                        <?php if (isset($skater_data['performance']['personal_best']['total']['score']) && $skater_data['performance']['personal_best']['total']['score'] > 0) : ?>
                            <ul class="score-breakdown">
                                <li><strong>Total:</strong> <?php echo number_format($skater_data['performance']['personal_best']['total']['score'], 2); ?> <small>(<?php echo esc_html($skater_data['performance']['personal_best']['total']['competition']); ?>)</small></li>
                                <li><strong>Short:</strong> <?php echo number_format($skater_data['performance']['personal_best']['short']['score'], 2); ?> <small>(<?php echo esc_html($skater_data['performance']['personal_best']['short']['competition']); ?>)</small></li>
                                <li><strong>Free:</strong> <?php echo number_format($skater_data['performance']['personal_best']['free']['score'], 2); ?> <small>(<?php echo esc_html($skater_data['performance']['personal_best']['free']['competition']); ?>)</small></li>
                            </ul>
                        <?php else: ?>
                            <span>No Results Recorded</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <strong>Season Bests:</strong>
                        <?php if (isset($skater_data['performance']['season_best']['total']['score']) && $skater_data['performance']['season_best']['total']['score'] > 0) : ?>
                            <ul class="score-breakdown">
                                <li><strong>Total:</strong> <?php echo number_format($skater_data['performance']['season_best']['total']['score'], 2); ?> <small>(<?php echo esc_html($skater_data['performance']['season_best']['total']['competition']); ?>)</small></li>
                                <li><strong>Short:</strong> <?php echo number_format($skater_data['performance']['season_best']['short']['score'], 2); ?> <small>(<?php echo esc_html($skater_data['performance']['season_best']['short']['competition']); ?>)</small></li>
                                <li><strong>Free:</strong> <?php echo number_format($skater_data['performance']['season_best']['free']['score'], 2); ?> <small>(<?php echo esc_html($skater_data['performance']['season_best']['free']['competition']); ?>)</small></li>
                            </ul>
                        <?php else: ?>
                            <span>No Results Recorded This Season</span>
                        <?php endif; ?>
                    </li>
                </ul>
            <?php else: ?>
                <p>No competition data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($skater_data['isu_ctes_targets']) || !empty($skater_data['custom_tes_targets'])) : ?>
    <div class="profile-section">
        <h4>Technical Score Targets</h4>
        <ul class="profile-list">
            <?php // Display ISU CTES Targets ?>
            <?php if (!empty($skater_data['isu_ctes_targets'])) : ?>
                <?php foreach ($skater_data['isu_ctes_targets'] as $event => $target) : 
                    $ctes_data = $skater_data['performance']['ctes'];
                    $is_achieved = $ctes_data['score'] >= $target;
                    
                    $sp_needs = max(0, $target - $ctes_data['free_tes']);
                    $fs_needs = max(0, $target - $ctes_data['short_tes']);
                    ?>
                    <li>
                        <div class="ctes-header">
                            <strong>Tech Minimum for <?php echo esc_html($event); ?>:</strong>
                            <span class="<?php if ($is_achieved) echo 'is-achieved-text'; ?>"><?php echo number_format($target, 2); ?></span>
                        </div>
                        <div class="ctes-breakdown-table">
                            <div class="ctes-breakdown-row is-header">
                                <div>Segment</div>
                                <div>Score / Needs</div>
                            </div>
                            <div class="ctes-breakdown-row">
                                <div>SP</div>
                                <div>
                                    <a href="<?php echo esc_url(get_permalink($ctes_data['short_comp_id'])); ?>"><?php echo number_format($ctes_data['short_tes'], 2); ?></a>
                                    <span class="needs-text">(Needs: <?php echo number_format($sp_needs, 2); ?>)</span>
                                </div>
                            </div>
                            <div class="ctes-breakdown-row">
                                <div>FS</div>
                                <div>
                                    <a href="<?php echo esc_url(get_permalink($ctes_data['free_comp_id'])); ?>"><?php echo number_format($ctes_data['free_tes'], 2); ?></a>
                                    <span class="needs-text">(Needs: <?php echo number_format($fs_needs, 2); ?>)</span>
                                </div>
                            </div>
                            <div class="ctes-breakdown-row is-footer">
                                <div><strong>Combined:</strong> <?php echo number_format($ctes_data['score'], 2); ?></div>
                                <div><strong>Off By:</strong> <?php echo number_format(max(0, $target - $ctes_data['score']), 2); ?></div>
                                <div><strong>Met:</strong> <span class="<?php if ($is_achieved) echo 'is-achieved-text'; ?>"><?php echo $is_achieved ? '✔️ Yes' : '❌ No'; ?></span></div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php // Display Custom TES Targets ?>
            <?php if (!empty($skater_data['custom_tes_targets'])) : ?>
                <?php foreach ($skater_data['custom_tes_targets'] as $target) : 
                    $is_achieved = $target['achieved_score'] >= $target['target_score'];
                    ?>
                     <li>
                        <strong><?php echo esc_html($target['name']); ?>:</strong>
                        <?php 
                        $progress_percent = ($target['achieved_score'] > 0 && $target['target_score'] > 0) ? ($target['achieved_score'] / $target['target_score']) * 100 : 0;
                        ?>
                        <span><?php echo number_format($target['achieved_score'], 2); ?> / <?php echo number_format($target['target_score'], 2); ?></span>
                        <div class="progress-bar-container">
                            <div class="progress-bar <?php if ($is_achieved) echo 'is-achieved'; ?>" style="width: <?php echo min($progress_percent, 100); ?>%;"></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($skater_data['accomplishments'] && (!empty($skater_data['accomplishments']['major']) || !empty($skater_data['accomplishments']['other']))) : ?>
        <div class="profile-section">
            <h4>Notable Accomplishments</h4>
            <?php if (!empty($skater_data['accomplishments']['major'])) : ?>
                <h5>Major Championship Podiums</h5>
                <ul class="accomplishments-list">
                    <?php foreach ($skater_data['accomplishments']['major'] as $title) : ?>
                        <li><?php echo $title; // HTML is safe from our function ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (!empty($skater_data['accomplishments']['other'])) : ?>
                <h5>Other Competition Wins</h5>
                <ul class="accomplishments-list">
                    <?php foreach ($skater_data['accomplishments']['other'] as $title) : ?>
                        <li><?php echo $title; // HTML is safe from our function ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="profile-section actions">
        <?php if (!$is_skater) : // Only coaches can edit skater info or create gap analysis ?>
            <a class="button" href="<?php echo esc_url($skater_data['edit_url']); ?>">Edit Skater Info</a>
            <?php if (!empty($gap_analysis_post)) : ?>
                <a href="<?php echo esc_url(get_permalink($gap_analysis_post[0]->ID)); ?>">View Gap Analysis</a>
            <?php else : ?>
                <a href="<?php echo esc_url(site_url('/create-gap-analysis/?skater_id=' . $skater_id)); ?>">Create Gap Analysis</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
