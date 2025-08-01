<?php
/**
 * The template for displaying a single Weekly Plan.
 *
 * This template provides a detailed view of a skater's weekly training plan.
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
            $weekly_plan_id = get_the_ID();
            
            // Get the linked skater post object.
            $skater_posts = get_field( 'skater' );
            $skater_post  = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            // Get all other weekly plan details.
            $week_start_raw = get_field( 'week_start' );
            $week_start     = $week_start_raw ? DateTime::createFromFormat('d/m/Y', $week_start_raw)->format('F j, Y') : '—';
            $theme          = get_field( 'theme' ) ?: '—';
            $off_ice_plan   = get_field( 'planned_off_ice_activities' );
            $session_focus  = get_field( 'session_breakdown' );
            
            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Weekly Plan: <?php echo esc_html( $week_start ); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-weekly-plan/' . $weekly_plan_id . '/' ) ); ?>" class="button button-primary">Update Plan</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Plan Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: max-content 1fr; gap: 1rem 2.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Theme:</strong>
                        <div><?php echo esc_html( $theme ); ?></div>
                    </div>
                </div>

                <!-- Off-Ice Plan Section -->
                <?php if ( $off_ice_plan ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Off-Ice Plan</h2>
                    </div>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Type</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $off_ice_plan as $activity ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $activity['day'] ); ?></td>
                                    <td><?php echo esc_html( is_array($activity['type']) ? implode(', ', $activity['type']) : '—' ); ?></td>
                                    <td><?php echo wp_kses_post( $activity['activity'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Session Focus Section -->
                <?php if ( $session_focus ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Daily Session Focus</h2>
                    </div>
                    <?php foreach ( $session_focus as $session ) : ?>
                        <div class="dashboard-box" style="margin-bottom: 1.5rem;">
                            <h3 style="margin-top: 0;"><?php echo esc_html( $session['day'] ); ?></h3>
                            <ul class="profile-list">
                                <li><strong>Primary Focus:</strong> <?php echo esc_html( $session['primary_focus'] ); ?></li>
                                <?php if( !empty($session['program_run_thru']) ): ?>
                                    <li><strong>Program Run Throughs:</strong> <?php echo wp_kses_post( $session['program_run_thru'] ); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Weekly plan not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
