<?php
/**
 * Template for displaying a list of all competitions.
 *
 * This template queries all 'competition' post types and displays them
 * in two tables: upcoming and past. It also handles the deletion of competitions.
 *
 * @package Coach_Operating_System
 * @since 1.0.0
 */

// --- HANDLE DELETION ---
// Check if a delete request has been sent.
if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_competition' && isset( $_GET['competition_id'] ) ) {
    $competition_id_to_delete = intval( $_GET['competition_id'] );
    $nonce = $_GET['_wpnonce'] ?? '';

    // Verify the nonce for security and check if the user has permission to delete posts.
    if ( wp_verify_nonce( $nonce, 'delete_competition_' . $competition_id_to_delete ) && current_user_can( 'delete_post', $competition_id_to_delete ) ) {
        // Safely delete the post.
        wp_delete_post( $competition_id_to_delete, true ); // true = bypass trash and delete permanently.
        
        // Redirect back to the same page to see the updated list.
        wp_safe_redirect( site_url( '/view-all-competitions/' ) );
        exit;
    } else {
        // Handle failed security check.
        wp_die( 'Security check failed.', 'Error' );
    }
}


// Load the dashboard-specific header.
include plugin_dir_path( __FILE__ ) . '../partials/header-dashboard.php';

// Redirect user if they are not logged in.
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

// --- PREPARE DATA ---

$today = date( 'Y-m-d' );

// Query for upcoming competitions.
$upcoming_competitions_query = new WP_Query( array(
    'post_type'      => 'competition',
    'posts_per_page' => -1,
    'meta_key'       => 'competition_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => array(
        array(
            'key'     => 'competition_date',
            'value'   => $today,
            'compare' => '>=',
            'type'    => 'DATE',
        ),
    ),
) );

// Query for past competitions.
$past_competitions_query = new WP_Query( array(
    'post_type'      => 'competition',
    'posts_per_page' => -1,
    'meta_key'       => 'competition_date',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => 'competition_date',
            'value'   => $today,
            'compare' => '<',
            'type'    => 'DATE',
        ),
    ),
) );

/**
 * Helper function to render a competition table row.
 *
 * @param int $competition_id The ID of the competition post.
 */
function coachos_render_competition_row( $competition_id ) {
    $competition_date_raw = get_field( 'competition_date', $competition_id );
    $competition_date     = $competition_date_raw ? DateTime::createFromFormat('Y-m-d', $competition_date_raw)->format('F j, Y') : '—';
    $competition_location = get_field( 'competition_location', $competition_id ) ?: '—';
    
    // Create a secure URL for the delete action.
    $delete_nonce = wp_create_nonce( 'delete_competition_' . $competition_id );
    $delete_url = add_query_arg(
        array(
            'action'         => 'delete_competition',
            'competition_id' => $competition_id,
            '_wpnonce'       => $delete_nonce,
        ),
        site_url( '/view-all-competitions/' )
    );
    ?>
    <tr>
        <td><a href="<?php echo esc_url( get_permalink( $competition_id ) ); ?>"><strong><?php echo esc_html( get_the_title( $competition_id ) ); ?></strong></a></td>
        <td><?php echo esc_html( $competition_date ); ?></td>
        <td><?php echo esc_html( $competition_location ); ?></td>
        <td>
            <a href="<?php echo esc_url( get_permalink( $competition_id ) ); ?>">View</a> |
            <a href="<?php echo esc_url( site_url( '/edit-competition/' . $competition_id . '/' ) ); ?>">Edit</a> |
            <a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('Are you sure you want to permanently delete this competition?');" style="color: #c0392b;">Delete</a>
        </td>
    </tr>
    <?php
}

?>

<!-- RENDER VIEW -->
<div class="wrap coach-dashboard">
    <main class="w-full">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>All Competitions</h1>
            <a href="<?php echo esc_url( site_url( '/coach-dashboard/' ) ); ?>" class="button">&larr; Back to Dashboard</a>
        </div>

        <!-- Upcoming Competitions Section -->
        <div class="section-header">
            <h2 class="section-title">Upcoming Competitions</h2>
        </div>
        <?php if ( $upcoming_competitions_query->have_posts() ) : ?>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ( $upcoming_competitions_query->have_posts() ) :
                        $upcoming_competitions_query->the_post();
                        coachos_render_competition_row( get_the_ID() );
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No upcoming competitions found.</p>
        <?php endif; ?>

        <!-- Past Competitions Section -->
        <div class="section-header">
            <h2 class="section-title">Past Competitions</h2>
        </div>
        <?php if ( $past_competitions_query->have_posts() ) : ?>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ( $past_competitions_query->have_posts() ) :
                        $past_competitions_query->the_post();
                        coachos_render_competition_row( get_the_ID() );
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No past competitions found.</p>
        <?php endif; ?>

    </main>
</div>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
