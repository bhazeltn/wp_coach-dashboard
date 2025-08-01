<?php
/**
 * The template for displaying a single Injury Log entry.
 *
 * This template provides a detailed view of a skater's injury, including
 * its status, description, and recovery details.
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
            $injury_log_id = get_the_ID();
            
            // Get the linked skater post object.
            $skater_posts = get_field( 'injured_skater' );
            $skater_post  = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            // Get all other injury details.
            $injury_type        = get_field( 'injury_type' ) ?: '—';
            $body_area_array    = get_field( 'body_area' );
            $body_area          = $body_area_array ? implode( ', ', $body_area_array ) : '—';
            $date_of_onset      = get_field( 'date_of_onset' ) ?: '—';
            $return_date        = get_field( 'return_to_sport_date' ) ?: '—';
            $severity_field     = get_field_object( 'severity' );
            $severity_value     = get_field( 'severity' );
            $severity           = $severity_value ? $severity_field['choices'][ $severity_value ] : '—';
            $status_field       = get_field_object( 'recovery_status' );
            $status_value       = get_field( 'recovery_status' );
            $recovery_status    = $status_value ? $status_field['choices'][ $status_value ] : '—';
            $recovery_notes     = get_field( 'recovery_notes' );
            
            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Injury Log: <?php echo esc_html( $injury_type ); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-injury-log/' . $injury_log_id . '/' ) ); ?>" class="button button-primary">Update Log</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Injury Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: repeat(2, max-content 1fr); gap: 1rem 2.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Recovery Status:</strong>
                        <div style="font-weight: 600;"><?php echo esc_html( $recovery_status ); ?></div>

                        <strong>Body Area:</strong>
                        <div><?php echo esc_html( $body_area ); ?></div>

                        <strong>Severity:</strong>
                        <div><?php echo esc_html( $severity ); ?></div>
                        
                        <strong>Date of Onset:</strong>
                        <div><?php echo esc_html( $date_of_onset ); ?></div>

                        <strong>Expected Return:</strong>
                        <div><?php echo esc_html( $return_date ); ?></div>
                    </div>
                </div>

                <!-- Recovery Notes Section -->
                <?php if ( $recovery_notes ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Recovery Notes</h2>
                    </div>
                    <div class="dashboard-box">
                        <?php echo wp_kses_post( $recovery_notes ); ?>
                    </div>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Injury log not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
