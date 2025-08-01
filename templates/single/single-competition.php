<?php
/**
 * The template for displaying a single competition and its results.
 *
 * This template displays the main details of a competition and a table
 * of all linked skater results.
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
            $competition_id = get_the_ID();

            // Get competition details from ACF fields.
            $competition_type     = get_field( 'competition_type' );
            $competition_date_raw = get_field( 'competition_date' );
            $competition_date     = $competition_date_raw ? DateTime::createFromFormat('Y-m-d', $competition_date_raw)->format('F j, Y') : '—';
            $competition_location = get_field( 'competition_location' );

            // Query for all competition results linked to this competition.
            $results_query = new WP_Query( array(
                'post_type'      => 'competition_result',
                'posts_per_page' => -1,
                'meta_key'       => 'skater', // A field that should exist on all results.
                'orderby'        => 'title',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'     => 'linked_competition',
                        'value'   => '"' . $competition_id . '"',
                        'compare' => 'LIKE',
                    ),
                ),
            ) );

            ?>

            <!-- RENDER VIEW -->
            <main class="w-full">
                
                <!-- Page Header -->
                <div class="page-header">
                    <h1><?php the_title(); ?></h1>
                    <a href="<?php echo esc_url( site_url( '/coach-dashboard' ) ); ?>" class="button">&larr; Back to Dashboard</a>
                </div>

                <!-- Competition Details Card -->
                <div class="dashboard-box">
                    <h3 class="section-title">Competition Details</h3>
                    <ul class="profile-list" style="margin-top: 1.5rem;">
                        <li><strong>Date:</strong> <?php echo esc_html( $competition_date ); ?></li>
                        <li><strong>Location:</strong> <?php echo esc_html( $competition_location ); ?></li>
                        <li><strong>Type:</strong> <?php echo esc_html( $competition_type ); ?></li>
                    </ul>
                    <a href="<?php echo esc_url( site_url( '/edit-competition/' . $competition_id . '/' ) ); ?>" class="button-small">Edit Competition Details</a>
                </div>

                <!-- Results Section -->
                <div class="section-header">
                    <h2 class="section-title">Skater Results</h2>
                </div>

                <?php if ( $results_query->have_posts() ) : ?>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Skater</th>
                                <th>Level</th>
                                <th>Placement</th>
                                <th>Total Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ( $results_query->have_posts() ) :
                                $results_query->the_post();
                                $result_id = get_the_ID();

                                // Get result details.
                                $skater_posts = get_field( 'skater', $result_id );
                                $skater_name = '—'; // Default value
                                if ( ! empty( $skater_posts ) && is_array( $skater_posts ) ) {
                                    $skater_post = $skater_posts[0]; // Get the first post object from the array.
                                    if ( $skater_post instanceof WP_Post ) {
                                        $skater_name = $skater_post->post_title;
                                    }
                                }

                                $level = get_field( 'level', $result_id ) ?: '—';
                                
                                $comp_score = get_field( 'comp_score', $result_id );
                                $placement = ! empty( $comp_score['placement'] ) ? $comp_score['placement'] : '—';
                                $total_score = isset($comp_score['total_competition_score']) ? number_format((float)$comp_score['total_competition_score'], 2) : '—';
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $skater_name ); ?></td>
                                    <td><?php echo esc_html( $level ); ?></td>
                                    <td><?php echo esc_html( $placement ); ?></td>
                                    <td><?php echo esc_html( $total_score ); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url( get_permalink( $result_id ) ); ?>">View</a> | 
                                        <a href="<?php echo esc_url( site_url( '/edit-competition-result/' . $result_id . '/' ) ); ?>">Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>No results have been logged for this competition yet.</p>
                <?php endif; ?>

            </main>

            <?php
        endwhile;
    else :
        echo '<p>Competition not found.</p>';
    endif;
    ?>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
