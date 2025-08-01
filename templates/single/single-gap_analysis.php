<?php
/**
 * The template for displaying a single Gap Analysis.
 *
 * This template provides a detailed view of a skater's gap analysis,
 * comparing target standards to their current status.
 *
 * @package Coach_Operating_System
 * @since 1.0.0
 */

// Load the dashboard-specific header.
include plugin_dir_path( __FILE__ ) . '../partials/header-dashboard.php';

// Redirect user if they are not logged in.
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

/**
 * Helper function to render a comparison row.
 *
 * @param string $label The label for the skill.
 * @param string $target The target standard content.
 * @param string $actual The skater's actual status content.
 */
function coachos_render_gap_row( $label, $target, $actual ) {
    if ( empty( $target ) && empty( $actual ) ) {
        return; // Don't render empty rows.
    }
    ?>
    <div class="gap-analysis-row">
        <h4><?php echo esc_html( $label ); ?></h4>
        <div class="gap-grid">
            <div class="gap-cell">
                <h5>Target Standard</h5>
                <div class="gap-content"><?php echo wp_kses_post( $target ?: '<em>Not specified.</em>' ); ?></div>
            </div>
            <div class="gap-cell">
                <h5>Skater Status</h5>
                <div class="gap-content"><?php echo wp_kses_post( $actual ?: '<em>Not specified.</em>' ); ?></div>
            </div>
        </div>
    </div>
    <?php
}

?>

<div class="wrap coach-dashboard">
    <?php
    // Start the WordPress loop.
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // --- PREPARE DATA ---
            $gap_analysis_id = get_the_ID();
            $skater_id       = get_field( 'skater' ); // This field returns a post ID.
            $skater_post     = $skater_id ? get_post( $skater_id ) : null;
            $last_updated    = get_field( 'date_updated' );

            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1><?php the_title(); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-gap-analysis/' . $gap_analysis_id . '/' ) ); ?>" class="button button-primary">Update Analysis</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Overview Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: max-content 1fr; gap: 1rem 1.5rem; align-items: center;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Last Updated:</strong>
                        <div><?php echo esc_html( $last_updated ? $last_updated : '—' ); ?></div>
                    </div>
                </div>

                <!-- Technical Skills -->
                <div class="section-header">
                    <h2 class="section-title">Technical Skills</h2>
                </div>
                <div class="dashboard-box">
                    <?php
                    coachos_render_gap_row( 'Jumps', get_field( 'jumps_target' ), get_field( 'jumps_actual' ) );
                    coachos_render_gap_row( 'Spins', get_field( 'spins_target' ), get_field( 'spins_actual' ) );
                    coachos_render_gap_row( 'Step Sequences', get_field( 'step_sequences_target' ), get_field( 'step_sequences_actual' ) );
                    coachos_render_gap_row( 'Skating Skills', get_field( 'skating_skills_target' ), get_field( 'skating_skills_actual' ) );
                    ?>
                </div>

                <!-- Mental Skills -->
                <div class="section-header">
                    <h2 class="section-title">Mental Skills</h2>
                </div>
                <div class="dashboard-box">
                    <?php
                    coachos_render_gap_row( 'Goal Setting', get_field( 'goal_setting_target' ), get_field( 'goal_setting_actual' ) );
                    coachos_render_gap_row( 'Imagery', get_field( 'imagery_target' ), get_field( 'imagery_actual' ) );
                    coachos_render_gap_row( 'Attention Control', get_field( 'attention_control_target' ), get_field( 'attention_control_actual' ) );
                    ?>
                </div>
                
                <!-- Physical Capabilities -->
                <div class="section-header">
                    <h2 class="section-title">Physical Capabilities</h2>
                </div>
                <div class="dashboard-box">
                    <?php
                    coachos_render_gap_row( 'Aerobic Fitness', get_field( 'aerobic_fitness_target' ), get_field( 'aerobic_fitness_actual' ) );
                    coachos_render_gap_row( 'Mobility & Flexibility', get_field( 'mobility_flexibility_target' ), get_field( 'mobility_flexibility_actual' ) );
                    coachos_render_gap_row( 'Strength & Power', get_field( 'strength_stability_power_target' ), get_field( 'strength_stability_power_actual' ) );
                    ?>
                </div>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Gap analysis not found.</p>';
    endif;
    ?>
</div>

<style>
    .gap-analysis-row { margin-bottom: 2rem; }
    .gap-analysis-row:last-child { margin-bottom: 0; }
    .gap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 0.5rem; }
    .gap-cell { border: 1px solid var(--border-color); border-radius: 6px; }
    .gap-cell h5 { margin: 0; padding: 0.75rem 1rem; background-color: #f8f9fa; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
    .gap-content { padding: 1rem; }
    .gap-content p:last-child { margin-bottom: 0; }
</style>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
