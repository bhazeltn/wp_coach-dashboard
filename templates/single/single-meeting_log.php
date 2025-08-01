<?php
/**
 * The template for displaying a single Meeting Log entry.
 *
 * This template provides a detailed view of a meeting, including
 * its participants, date, type, and summary notes.
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
            $meeting_log_id = get_the_ID();
            
            // Get the linked skater post objects.
            $skater_posts = get_field( 'skater' );
            
            // Get all other meeting details.
            $meeting_date   = get_field( 'meeting_date' ) ?: '—';
            $meeting_types  = get_field( 'meeting_type' );
            $meeting_type   = $meeting_types ? implode( ', ', $meeting_types ) : '—';
            $participants   = get_field( 'participants' ) ?: '—';
            $summary_notes  = get_field( 'summary__notes' );

            // Determine the primary skater for the "Back" button link.
            $primary_skater_post = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Meeting Log: <?php echo esc_html( $meeting_date ); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-meeting-log/' . $meeting_log_id . '/' ) ); ?>" class="button button-primary">Update Log</a>
                        <?php if ( $primary_skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $primary_skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Meeting Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: max-content 1fr; gap: 1rem 2.5rem;">
                        <strong>Skater(s):</strong>
                        <div>
                            <?php
                            if ( ! empty( $skater_posts ) ) {
                                $skater_links = array();
                                foreach ( $skater_posts as $skater_post ) {
                                    $skater_links[] = '<a href="' . esc_url( get_permalink( $skater_post->ID ) ) . '">' . esc_html( $skater_post->post_title ) . '</a>';
                                }
                                echo implode( ', ', $skater_links );
                            } else {
                                echo '—';
                            }
                            ?>
                        </div>

                        <strong>Meeting Type:</strong>
                        <div><?php echo esc_html( $meeting_type ); ?></div>

                        <strong>Participants:</strong>
                        <div><?php echo esc_html( $participants ); ?></div>
                    </div>
                </div>

                <!-- Summary / Notes Section -->
                <?php if ( $summary_notes ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Summary / Notes</h2>
                    </div>
                    <div class="dashboard-box">
                        <?php echo wp_kses_post( $summary_notes ); ?>
                    </div>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Meeting log not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
