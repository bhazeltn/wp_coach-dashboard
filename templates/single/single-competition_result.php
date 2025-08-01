<?php
/**
 * The template for displaying a single competition result.
 *
 * This template provides a detailed breakdown of a skater's scores,
 * placements, and any associated media for a single competition.
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
            $result_id = get_the_ID();

            // Get linked skater and competition post objects.
            $skater_posts = get_field( 'skater' );
            $skater_post = ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) ? $skater_posts[0] : null;

            $competition_posts = get_field( 'linked_competition' );
            $competition_post = ( ! empty( $competition_posts ) && is_array( $competition_posts ) ) ? $competition_posts[0] : null;

            // Get basic result details.
            $level      = get_field( 'level' ) ?: '—';
            $discipline = get_field( 'discipline' ) ?: '—';
            $notes      = get_field( 'notes' );
            
            // Get score and placement data from the group field.
            $comp_score  = get_field( 'comp_score' );
            $placement   = ! empty( $comp_score['placement'] ) ? $comp_score['placement'] : '—';
            $total_score = isset( $comp_score['total_competition_score'] ) ? number_format( (float) $comp_score['total_competition_score'], 2 ) : '—';

            // Get segment scores
            $sp_scores = get_field('sp_score_place');
            $fs_scores = get_field('fs_score');

            // Get file uploads and video links.
            $detail_sheets = get_field( 'detail_sheets' );
            $video_links   = get_field( 'video_link' );

            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Competition Result</h1>
                    <div class="actions" style="display: flex; gap: 1rem;">
                        <a href="<?php echo esc_url( site_url( '/edit-competition-result/' . $result_id . '/' ) ); ?>" class="button button-primary">Update Result</a>
                        <?php if ( $skater_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>" class="button">&larr; Back to Skater</a>
                        <?php endif; ?>
                        <?php if ( $competition_post ) : ?>
                             <a href="<?php echo esc_url( get_permalink( $competition_post->ID ) ); ?>" class="button">&larr; Back to Competition</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Result Details Card -->
                <div class="dashboard-box">
                    <div style="display: grid; grid-template-columns: repeat(2, max-content 1fr); gap: 1rem 2.5rem;">
                        <strong>Skater:</strong>
                        <div><?php if ( $skater_post ) : ?><a href="<?php echo esc_url( get_permalink( $skater_post->ID ) ); ?>"><?php echo esc_html( $skater_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Level:</strong>
                        <div><?php echo esc_html( $level ); ?></div>

                        <strong>Competition:</strong>
                        <div><?php if ( $competition_post ) : ?><a href="<?php echo esc_url( get_permalink( $competition_post->ID ) ); ?>"><?php echo esc_html( $competition_post->post_title ); ?></a><?php else: ?>—<?php endif; ?></div>

                        <strong>Discipline:</strong>
                        <div><?php echo esc_html( $discipline ); ?></div>

                        <strong>Overall Placement:</strong>
                        <div><?php echo esc_html( $placement ); ?></div>

                        <strong>Total Score:</strong>
                        <div><?php echo esc_html( $total_score ); ?></div>
                    </div>
                </div>

                <!-- Scores Section -->
                <div class="section-header">
                    <h2 class="section-title">Scores</h2>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Segment</th>
                            <th>Placement</th>
                            <th>Total Segment Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $sp_scores['short_program_score'] ) ) : ?>
                        <tr>
                            <td>Short Program</td>
                            <td><?php echo esc_html( $sp_scores['sp_placement'] ?: '—' ); ?></td>
                            <td><?php echo esc_html( number_format( (float) $sp_scores['short_program_score'], 2 ) ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( ! empty( $fs_scores['free_program_score'] ) ) : ?>
                        <tr>
                            <td>Free Program</td>
                            <td><?php echo esc_html( $fs_scores['fs_placement'] ?: '—' ); ?></td>
                            <td><?php echo esc_html( number_format( (float) $fs_scores['free_program_score'], 2 ) ); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Detail Sheets Section -->
                <?php if ( $detail_sheets ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Detail Sheets</h2>
                    </div>
                    <ul class="profile-list">
                        <?php foreach ( $detail_sheets as $sheet ) : ?>
                            <li>
                                <strong><?php echo esc_html( $sheet['segment'] ); ?>:</strong>
                                <a href="<?php echo esc_url( $sheet['upload']['url'] ); ?>" target="_blank">Download File</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Video Links Section -->
                <?php if ( $video_links ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Videos</h2>
                    </div>
                    <ul class="profile-list">
                        <?php foreach ( $video_links as $video ) : ?>
                            <li>
                                <strong><?php echo esc_html( $video['segment'] ); ?>:</strong>
                                <a href="<?php echo esc_url( $video['link'] ); ?>" target="_blank">Watch Video</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                 <!-- Notes Section -->
                <?php if ( $notes ) : ?>
                    <div class="section-header">
                        <h2 class="section-title">Notes</h2>
                    </div>
                    <div class="dashboard-box">
                        <?php echo wp_kses_post( $notes ); ?>
                    </div>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Competition result not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
