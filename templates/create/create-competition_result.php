<?php
/**
 * Template: Create or Edit Competition Result
 *
 * Combines create and edit functionality for a competition result,
 * styled according to the design guide.
 *
 * @package Coach_Operating_System
 * @since 1.0.0
 */

// This function must be called before any HTML is output.
acf_form_head();

// Load the dashboard-specific header.
include plugin_dir_path( __FILE__ ) . '../partials/header-dashboard.php';

// Redirect user if they are not logged in.
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

// --- PREPARE DATA (using logic from your working file) ---

// Check if we are editing an existing competition result.
$result_id = get_query_var( 'edit_competition_result' );
$is_edit   = ! empty( $result_id ) && get_post_type( $result_id ) === 'competition_result';

// Set the post_id for the form. 'new_post' for creation, or the integer ID for editing.
$post_id = $is_edit ? intval( $result_id ) : 'new_post';

// Determine the skater ID for the return URL.
$skater_id_for_return = null;
if ( $is_edit ) {
    // If editing, get the skater from the result's 'skater' field.
    $skater_post = get_field( 'skater', $post_id );
    if ( $skater_post instanceof WP_Post ) {
        $skater_id_for_return = $skater_post->ID;
    }
} elseif ( isset( $_GET['skater_id'] ) ) {
    // If creating, get the skater from the URL parameter.
    $skater_id_for_return = intval( $_GET['skater_id'] );
}

// Construct the return URL.
$return_url = $skater_id_for_return
    ? get_permalink( $skater_id_for_return )
    : site_url( '/coach-dashboard' );

// Dynamically set the page title and button text based on the action.
$page_title  = $is_edit ? 'Update Competition Result' : 'Create New Competition Result';
$submit_text = $is_edit ? 'Update Result' : 'Create Result';


// --- ACF FORM SETTINGS ---

$result_form_settings = array(
    'post_id'         => $post_id,
    'post_title'      => false,
    'post_content'    => false,
    'field_groups'    => array( 'group_681c30ea05053' ), // 'Competition Result Details' field group.
    'new_post'        => array(
        'post_type'   => 'competition_result',
        'post_status' => 'publish',
    ),
    'submit_value'    => $submit_text,
    'uploader'        => 'wp',
    'return'          => $return_url,
    'updated_message' => __( 'Competition result saved successfully.', 'wp-coach' ),
);

// Pre-populate the skater field if a skater_id is passed in the URL.
if ( $skater_id_for_return && ! $is_edit ) {
    add_filter( 'acf/load_value/name=skater', function( $value ) use ( $skater_id_for_return ) {
        if ( ! $value ) {
            $value = $skater_id_for_return;
        }
        return $value;
    }, 10, 1 );
}

?>

<!-- RENDER VIEW -->
<main class="w-full bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 md:p-8">
        
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo esc_html( $page_title ); ?></h1>
            <a href="<?php echo esc_url( $return_url ); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Cancel
            </a>
        </div>

        <!-- Form Card -->
        <div class="w-full bg-white rounded-lg shadow-lg p-6 md:p-8">
            <?php 
            // Render the ACF form with our settings.
            acf_form( $result_form_settings ); 
            ?>
        </div>

    </div>
</main>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
