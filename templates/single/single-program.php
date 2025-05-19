<?php
/**
 * Template: Single Program View
 */

get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

global $post;
setup_postdata($post);

$post_id = get_the_ID();
$title = get_the_title($post_id);

$skater = get_field('skater', $post_id)[0] ?? null;
$skater_name = $skater ? get_the_title($skater) : '—';
$season = get_field('season', $post_id) ?? '—';
$category = get_field('program_category', $post_id) ?? '—';

$content = get_field('planned_program_content', $post_id);
$music = get_field('music_selections', $post_id);
$outfits = get_field('outfit_photos', $post_id);
$revisions = get_field('revision_log', $post_id);
$coach_notes = get_field('coach_notes', $post_id);
$skater_notes = get_field('skater_notes', $post_id);
?>

<div class="wrap coach-dashboard">
    <h1><?= esc_html($title) ?></h1>

    <div class="dashboard-box">
        <p><strong>Skater:</strong> <?= esc_html($skater_name) ?></p>
        <p><strong>Season:</strong> <?= esc_html($season) ?></p>
        <p><strong>Program Type:</strong> <?= esc_html($category) ?></p>
    </div>

    <hr>
    <div class="dashboard-box">
        <h3>⛸ Planned Program Content</h3>
        <?php if (!empty($content)) : ?>
            <ul>
                <?php foreach ($content as $item) : ?>
                    <li><?= esc_html($item['element']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><em>No layout submitted yet.</em></p>
        <?php endif; ?>
    </div>

    <hr>
    <div class="dashboard-box">
        <h3>🎵 Music Selection</h3>
        <?php if (!empty($music)) : ?>
            <ul>
                <?php foreach ($music as $track) : ?>
                    <li><?= esc_html($track['track'] ?? '—') ?> by <?= esc_html($track['artist'] ?? '—') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><em>No music listed.</em></p>
        <?php endif; ?>
    </div>

    <hr>
    <div class="dashboard-box">
        <h3>👗 Outfit</h3>
        <?php if (!empty($outfits)) : ?>
            <?php foreach ($outfits as $img_id) :
                $url = wp_get_attachment_image_url($img_id, 'medium'); ?>
                <img src="<?= esc_url($url) ?>" alt="" style="max-width: 200px; margin-right: 10px;">
            <?php endforeach; ?>
        <?php else : ?>
            <p><em>No outfit photos uploaded.</em></p>
        <?php endif; ?>
    </div>

    <hr>
    <div class="dashboard-box">
        <h3>📝 Revision Log</h3>
        <?php if (!empty($revisions)) : ?>
            <ul>
                <?php foreach ($revisions as $entry) : ?>
                    <li>
                        <?= esc_html($entry['revision_date']) ?>:
                        <?= esc_html($entry['change_notes']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><em>No revisions recorded.</em></p>
        <?php endif; ?>
    </div>

    <hr>
    <div class="dashboard-box">
        <h3>📓 Notes</h3>
        <p><strong>Coach:</strong><br><?= wp_kses_post($coach_notes ?: '<em>None provided.</em>') ?></p>
        <p><strong>Skater:</strong><br><?= wp_kses_post($skater_notes ?: '<em>None provided.</em>') ?></p>
    </div>

    <p><a class="button" href="<?= esc_url(site_url('/edit-program/' . $post_id)) ?>">Edit Program</a></p>
    <p><a class="button" href="<?= esc_url($skater ? '/skater/' . $skater->post_name . '/' : '/coach-dashboard') ?>">Back</a></p>
</div>

<?php get_footer(); ?>
