<?php
/**
 * The template for displaying a single Yearly Plan.
 *
 * This template provides a detailed view of a skater's yearly training plan,
 * including macrocycles and peak planning.
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

?>

<div class="wrap coach-dashboard">
    <?php
    // Start the WordPress loop.
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // --- PREPARE DATA ---
            $yearly_plan_id = get_the_ID();
            
            // Get the linked skater post object.
            $skater_posts = get_field( 'skater' );
            $skater_post  = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            // Get all other yearly plan details.
            $season_dates         = get_field( 'season_dates' );
            $start_date_raw       = $season_dates['start_date'] ?? '';
            $start_date           = $start_date_raw ? DateTime::createFromFormat('d/m/Y', $start_date_raw)->format('F j, Y') : '—';
            $end_date_raw         = $season_dates['end_date'] ?? '';
            $end_date             = $end_date_raw ? DateTime::createFromFormat('d/m/Y', $end_date_raw)->format('F j, Y') : '—';
            $primary_goal         = get_field( 'primary_season_goal' );
            $macrocycles          = get_field( 'macrocycles' );
            $peak_planning        = get_field( 'peak_planning' );
            $evaluation_strategy  = get_field( 'evaluation_strategy' );
            $coach_notes          = get_field( 'coach_notes' );
            
            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Yearly Plan: <?php the_title(); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-yearly-plan/' . $yearly_plan_id . '/' ) ); ?>" class="button button-primary">Update Plan</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Season Overview Card -->
                <div class="dashboard-box">
                    <h3 class="section-title" style="margin-top:0;">Season Overview</h3>
                    <div style="display: grid; grid-template-columns: max-content 1fr; gap: 1rem 2.5rem; margin-top: 1.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Dates:</strong>
                        <div><?php echo esc_html( $start_date . ' to ' . $end_date ); ?></div>
                    </div>
                    <?php if ( $primary_goal ) : ?>
                        <div class="profile-section">
                            <strong>Primary Season Goal:</strong>
                            <p style="margin-top: 0.5rem;"><?php echo esc_html( $primary_goal ); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Peak Planning Section -->
                <?php if ( $peak_planning ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Peak Planning</h2>
                    </div>
                    <div class="dashboard-box">
                        <ul class="profile-list">
                            <li><strong>Peak Type:</strong> <?php echo esc_html( $peak_planning['peak_type'] ); ?></li>
                            <?php if ( $peak_planning['primary_peak_event'] ) : ?>
                                <li><strong>Primary Peak Event:</strong> <a href="<?php echo esc_url( get_permalink( $peak_planning['primary_peak_event']->ID ) ); ?>"><?php echo esc_html( $peak_planning['primary_peak_event']->post_title ); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Macrocycles Section -->
                <?php if ( $macrocycles ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Macrocycles</h2>
                    </div>
                    <?php foreach ( $macrocycles as $cycle ) : ?>
                        <details class="macrocycle-toggle">
                            <summary><?php echo esc_html( $cycle['phase_title'] ?: 'Unnamed Phase' ); ?></summary>
                            <div class="macrocycle-content">
                                <ul class="profile-list">
                                    <li><strong>Dates:</strong> <?php echo esc_html( $cycle['phase_start'] . ' to ' . $cycle['phase_end'] ); ?></li>
                                    <li><strong>Focus:</strong> <?php echo esc_html( $cycle['phase_focus'] ); ?></li>
                                    <?php if ( ! empty( $cycle['evaluation_strategy'] ) ) : ?>
                                        <li><strong>Evaluation Strategy:</strong> <?php echo wp_kses_post( $cycle['evaluation_strategy'] ); ?></li>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $cycle['coach_notes'] ) ) : ?>
                                        <li><strong>Coach Notes:</strong> <?php echo wp_kses_post( $cycle['coach_notes'] ); ?></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Yearly plan not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
