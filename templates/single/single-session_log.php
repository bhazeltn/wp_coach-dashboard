<?php
/**
 * The template for displaying a single Session Log entry.
 *
 * This template provides a detailed view of a training session, including
 * element focus, program work, and wellbeing check-ins.
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
            $session_log_id = get_the_ID();
            
            // Get the linked skater post object.
            $skater_posts = get_field( 'skater' );
            $skater_post  = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            // Get all other session details.
            $session_date   = get_field( 'session_date' ) ?: '—';
            $jump_focus     = get_field( 'jump_focus' );
            $spin_focus     = get_field( 'spin_focus' );
            $program_work   = get_field( 'program_work' );
            $energy         = get_field( 'energy_stamina' ) ?: '—';
            $wellbeing      = get_field( 'wellbeing_focus_check-in' );
            $wellbeing_notes = get_field( 'wellbeing_mental_focus_notes' );
            $coach_notes    = get_field( 'coach_notes' );
            
            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Session Log: <?php echo esc_html( $session_date ); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-session-log/' . $session_log_id . '/' ) ); ?>" class="button button-primary">Update Log</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Session Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: max-content 1fr; gap: 1rem 2.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>
                    </div>
                </div>

                <!-- Jump Focus Section -->
                <?php if ( $jump_focus ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Jump Focus</h2>
                    </div>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Element</th>
                                <th>Outcome Notes</th>
                                <th>Consistency</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $jump_focus as $jump ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $jump['element'] ); ?></td>
                                    <td><?php echo wp_kses_post( $jump['outcome_notes'] ); ?></td>
                                    <td><?php echo esc_html( $jump['consistency'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Spin Focus Section -->
                <?php if ( $spin_focus ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Spin Focus</h2>
                    </div>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Element</th>
                                <th>Outcome Notes</th>
                                <th>Consistency</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $spin_focus as $spin ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $spin['element'] ); ?></td>
                                    <td><?php echo wp_kses_post( $spin['outcome_notes'] ); ?></td>
                                    <td><?php echo esc_html( $spin['consistency'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <!-- Program Work Section -->
                <?php if ( $program_work ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Program Work</h2>
                    </div>
                    <?php foreach ( $program_work as $program ) : ?>
                        <div class="dashboard-box" style="margin-bottom: 1.5rem;">
                            <h3 style="margin-top: 0;"><?php echo esc_html( $program['program'] ); ?></h3>
                            <ul class="profile-list">
                                <li><strong>Type of Work:</strong> <?php echo esc_html( is_array($program['type_of_work']) ? implode(', ', $program['type_of_work']) : $program['type_of_work'] ); ?></li>
                                <li><strong>Feedback:</strong> <?php echo wp_kses_post( $program['feedback'] ); ?></li>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Wellbeing & Focus Section -->
                <div class="section-header">
                    <h2 class="section-title">Wellbeing & Focus</h2>
                </div>
                <div class="dashboard-box">
                    <ul class="profile-list">
                        <li><strong>Energy / Stamina:</strong> <?php echo esc_html( $energy ); ?></li>
                        <li><strong>Wellbeing / Focus Check-In:</strong> <?php echo esc_html( is_array($wellbeing) ? implode(', ', $wellbeing) : '—' ); ?></li>
                        <?php if( $wellbeing_notes ): ?>
                            <li><strong>Notes:</strong> <?php echo wp_kses_post( $wellbeing_notes ); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Coach Notes Section -->
                <?php if ( $coach_notes ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Coach Notes</h2>
                    </div>
                    <div class="dashboard-box">
                        <?php echo wp_kses_post( $coach_notes ); ?>
                    </div>
                <?php endif; ?>


            </main>

            <?php
        endwhile;
    else :
        echo '<p>Session log not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
