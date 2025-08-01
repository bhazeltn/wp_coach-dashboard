<?php
/**
 * The template for displaying a single Program.
 *
 * This template provides a detailed view of a skater's program, including
 * its season and other relevant details.
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
            $program_id = get_the_ID();
            
            // Get the linked skater post object.
            $skater_posts = get_field( 'skater' );
            $skater_post  = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            // Get other program details.
            $season = get_field( 'season' ) ?: '—';
            
            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Program: <?php the_title(); ?></h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-program/' . $program_id . '/' ) ); ?>" class="button button-primary">Update Program</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Program Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: max-content 1fr; gap: 1rem 2.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Season:</strong>
                        <div><?php echo esc_html( $season ); ?></div>
                    </div>
                </div>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Program not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
