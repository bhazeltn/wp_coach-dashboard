<?php
get_header();
the_post();

$skater_id = get_field('skater');
$skater_name = get_the_title($skater_id);
?>

<h1><?php the_title(); ?></h1>

<p><strong>Skater:</strong> <?php echo esc_html($skater_name); ?></p>
<p><strong>Season:</strong> <?php the_field('season'); ?></p>
<p><strong>Discipline:</strong> <?php the_field('discipline'); ?></p>
<p><strong>Level:</strong> <?php the_field('level'); ?></p>
<p><strong>Program Type:</strong> <?php the_field('program_type'); ?></p>

<h3>Music</h3>
<ul>
    <li><strong>Title:</strong> <?php the_field('music_title'); ?></li>
    <li><strong>Composer:</strong> <?php the_field('composer'); ?></li>
    <li><strong>Performer:</strong> <?php the_field('performer'); ?></li>
</ul>

<h3>Choreography</h3>
<ul>
    <li><strong>Choreographer:</strong> <?php the_field('choreographer'); ?></li>
    <li><strong>Start Date:</strong> <?php the_field('start_date'); ?></li>
</ul>

<h3>Program Layout</h3>
<p><strong>Layout Notes:</strong><br><?php the_field('layout_notes'); ?></p>
<p><strong>Planned Content:</strong><br><?php the_field('planned_content'); ?></p>

<h3>Notes</h3>
<p><?php the_field('notes'); ?></p>

<?php get_footer(); ?>
