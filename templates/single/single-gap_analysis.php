<?php
get_header();

$skater = get_field('skater');
$skater_name = $skater ? get_the_title($skater) : 'Unknown Skater';
$group = 'group_gap_analysis';
?>

<div class="wrap">
  <h1><?= esc_html($skater_name); ?> – Gap Analysis</h1>

  <?php
  // Helper: render a collapsible section
  function render_gap_section($title, $fields, $always_show = false) {
      $has_data = $always_show;
      foreach ($fields as $field) {
          if (get_field($field . '_target') || get_field($field . '_status')) {
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
                  $status = get_field($key . '_status');
                  if ($always_show || $target || $status):
                  ?>
                  <tr>
                      <td><?= esc_html($label); ?></td>
                      <td><?= esc_html($target ?: '—'); ?></td>
                      <td><?= esc_html($status ?: '—'); ?></td>
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
      'Step Sequence' => 'step_sequence',
      'Skating Skills' => 'skating_skills',
      'Field Movements' => 'field_movements',
      'Performance Skills' => 'performance_skills'
  ], true);

  // Mental Section – only if data
  render_gap_section('Mental Skills', [
      'Mental Skills Training' => 'mental_training',
      'Goal Setting' => 'goal_setting',
      'Imagery' => 'imagery',
      'Arousal Management' => 'arousal',
      'Attention Control' => 'attention',
      'Emotional Regulation' => 'emotional',
      'Self Talk' => 'self_talk'
  ]);

  // Physical Section – only if data
  render_gap_section('Physical Capacities', [
      'Physical Literacy' => 'physical_literacy',
      'Aerobic Fitness' => 'aerobic',
      'Mobility & Flexibility' => 'mobility',
      'Strength, Stability & Power' => 'strength',
      'Periodization' => 'periodization',
      'Nutrition & Fueling' => 'nutrition',
      'Supplements' => 'supplements',
      'Physique Monitoring' => 'physique',
      'Recovery & Regeneration' => 'recovery',
      'Sleep' => 'sleep'
  ]);
  ?>
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
</style>

<?php get_footer(); ?>
