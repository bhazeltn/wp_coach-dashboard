<?php
/**
 * Template: Create or Edit Competition
 *
 * Combines create and edit functionality for a competition,
 * styled according to the design guide.
 *
 * @package Coach_Operating_System
 * @since 1.0.0
 */

// This function must be called before any HTML is output.
// It handles form submission and enqueues necessary scripts/styles for ACF.
acf_form_head();

// Load the dashboard-specific header.
include plugin_dir_path( __FILE__ ) . '../partials/header-dashboard.php';

// Redirect user if they are not logged in.
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

// --- PREPARE DATA ---

// Check if we are editing an existing competition or creating a new one.
$competition_id = get_query_var( 'edit_competition' );
$is_edit        = ! empty( $competition_id ) && get_post_type( $competition_id ) === 'competition';

// Set the post_id for the form. 'new_post' for creation, or the integer ID for editing.
$post_id = $is_edit ? intval( $competition_id ) : 'new_post';

// Dynamically set the page title and button text based on the action.
$page_title  = $is_edit ? 'Update Competition' : 'Create New Competition';
$submit_text = $is_edit ? 'Update Competition' : 'Create Competition';

// --- ACF FORM SETTINGS ---

$competition_form_settings = array(
    'post_id'         => $post_id,
    'post_title'      => true, // Show the title field for the competition name.
    'post_content'    => false,
    
    // The correct field group ID for 'Competition Details' from your JSON file.
    'field_groups'    => array( 'group_681c237c3ab7d' ), 
    
    'new_post'        => array(
        'post_type'   => 'competition',
        'post_status' => 'publish',
    ),
    'submit_value'    => $submit_text,
    'uploader'        => 'wp',
    'return'          => site_url( '/coach-dashboard' ), // Or a dedicated competitions page.
    'updated_message' => __( 'Competition saved successfully.', 'wp-coach' ),
);

?>

<!-- RENDER VIEW -->
<main class="w-full bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 md:p-8">
        
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo esc_html( $page_title ); ?></h1>
            <a href="<?php echo esc_url( site_url( '/coach-dashboard' ) ); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Cancel
            </a>
        </div>

        <!-- Form Card -->
        <div class="w-full bg-white rounded-lg shadow-lg p-6 md:p-8">
            <?php 
            // Render the ACF form with our settings.
            acf_form( $competition_form_settings ); 
            ?>
        </div>

    </div>
</main>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
