<?php
/**
 * The template for displaying a single Goal.
 *
 * This template provides a detailed view of a skater's goal, including
 * its status, description, and other relevant details.
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
            $goal_id      = get_the_ID();
            
            // Get the linked skater post object.
            $skater_posts = get_field( 'skater' );
            $skater_post  = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            // Get all other goal details.
            $goal_type        = get_field( 'goal_type' ) ?: '—';
            $goal_timeframe   = get_field( 'goal_timeframe' ) ? get_field_object('goal_timeframe')['choices'][get_field('goal_timeframe')] : '—';
            $current_status   = get_field( 'current_status' ) ?: '—';
            $target_date      = get_field( 'target_date' ) ?: '—';
            $smart_description = get_field( 'smart_description' );
            $progress_notes   = get_field( 'progress_notes' );
            
            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1><?php the_title(); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-goal/' . $goal_id . '/' ) ); ?>" class="button button-primary">Update Goal</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Goal Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: repeat(2, max-content 1fr); gap: 1rem 2.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Status:</strong>
                        <div style="font-weight: 600;"><?php echo esc_html( $current_status ); ?></div>

                        <strong>Type:</strong>
                        <div><?php echo esc_html( $goal_type ); ?></div>

                        <strong>Target Date:</strong>
                        <div><?php echo esc_html( $target_date ); ?></div>
                        
                        <strong>Timeframe:</strong>
                        <div><?php echo esc_html( $goal_timeframe ); ?></div>
                    </div>
                </div>

                <!-- SMART Description Section -->
                <?php if ( $smart_description ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">SMART Description</h2>
                    </div>
                    <div class="dashboard-box">
                        <?php echo wp_kses_post( $smart_description ); ?>
                    </div>
                <?php endif; ?>

                <!-- Progress Notes Section -->
                <?php if ( $progress_notes ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Progress Notes</h2>
                    </div>
                    <div class="dashboard-box">
                        <?php echo wp_kses_post( $progress_notes ); ?>
                    </div>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Goal not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
