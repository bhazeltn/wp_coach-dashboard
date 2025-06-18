<?php
get_header();
echo '<link rel="stylesheet" href="/wp-content/plugins/skater-planning-dashboard/css/dashboard-style.css">';

$gap_id = get_the_ID();
$skater = get_field('skater');
$skater_id = is_object($skater) ? $skater->ID : $skater;
$skater_name = $skater ? get_the_title($skater) : 'Unknown Skater';
$skater_slug = get_post_field('post_name', $skater_id);
?>

<div class="wrap coach-dashboard">
  <h1><?= esc_html($skater_name); ?> – Gap Analysis</h1>

  <div class="button-row" style="margin-bottom: 1.5em;">
    <?php
    $is_skater = in_array('skater', (array) $current_user->roles);
    if (!$is_skater){
      echo '<a class="button" href="<?= esc_url(site_url('/edit-gap-analysis/' . $gap_id)) ?>">Update Gap Analysis</a>';
    }
    ?>
    <a class="button" href="<?= esc_url(site_url('/skater/' . $skater_slug)) ?>">&larr; Back to Skater</a>
  </div>

  <?php
    $last_updated_raw = get_field('date_updated');
    if ($last_updated_raw) {
        $last_updated = DateTime::createFromFormat('Y-m-d', $last_updated_raw);
        if ($last_updated) {
            echo '<p><strong>Last Updated:</strong> ' . esc_html($last_updated->format('F j, Y')) . '</p>';
        }
    }
  ?>

  <?php
  // Helper: render a collapsible section
  function render_gap_section($title, $fields, $always_show = false) {
    $has_data = $always_show;
    foreach ($fields as $field) {
        if (get_field($field . '_target') || get_field($field . '_actual')) {
            $has_data = true;
            break;
        }
    }

    if (!$has_data) return;

    ?>
    <details open>
        <summary><strong><?= esc_html($title); ?></strong></summary>
        <table class="widefat fixed striped">
            <thead><tr><th>Area</th><th>Target Standard</th><th>Current Status</th></tr></thead>
            <tbody>
            <?php foreach ($fields as $label => $key): ?>
                <?php
                $target = get_field($key . '_target');
                $status = get_field($key . '_actual');
                if ($always_show || $target || $status):
                ?>
                <tr>
                    <td><?= esc_html($label); ?></td>
                    <td><?= $target ? wp_kses_post($target) : '—'; ?></td>
                    <td><?= $status ? wp_kses_post($status) : '—'; ?></td>

                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </details>
    <br>
    <?php
  }


  
  // Technical Section – always show
  render_gap_section('Technical Skills', [
      'Jumps' => 'jumps',
      'Spins' => 'spins',
      'Step Sequence' => 'step_sequences',
      'Skating Skills' => 'skating_skills',
      'Field Movements' => 'field_movements',
      'Performance Skills' => 'performance_skills'
  ], true);

  // Mental Section – only if data
  render_gap_section('Mental Skills', [
      'Mental Skills Training' => 'mental_skills_training',
      'Goal Setting' => 'goal_setting',
      'Imagery' => 'imagery',
      'Arousal Management' => 'arousal_management',
      'Attention Control' => 'attention_control',
      'Emotional Regulation' => 'emotional_regulation',
      'Self Talk' => 'self_talk'
  ]);

  // Physical Section – only if data
  render_gap_section('Physical Capabilities', [
      'Physical Literacy / Movement Skills' => 'physical_literacy_movement_skills',
      'Aerobic Fitness' => 'aerobic_fitness',
      'Mobility & Flexibility' => 'mobility_flexibility',
      'Strength, Stability & Power' => 'strength_stability_power',
      'Periodization' => 'periodization',
      'Nutrition & Fueling' => 'nutrition_fueling',
      'Supplements' => 'supplements',
      'Physique Monitoring' => 'physique_monitoring',
      'Recovery & Regeneration' => 'recovery_regeneration',
      'Sleep' => 'sleep'
  ]);
  ?>

  <div class="button-row" style="margin-top: 2em;">
    <a class="button" href="<?= esc_url(site_url('/edit-gap-analysis/' . $gap_id)) ?>">Update Gap Analysis</a>
    <a class="button" href="<?= esc_url(site_url('/skater/' . $skater_slug)) ?>">&larr; Back to Skater</a>
  </div>
</div>

<style>
summary {
  cursor: pointer;
  font-size: 1.1em;
  padding: 4px;
}
details {
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 8px;
}
table.widefat th, table.widefat td {
  vertical-align: top;
}
.button-row {
  display: flex;
  gap: 10px;
  margin-bottom: 1em;
}
</style>

<?php get_footer(); ?>
