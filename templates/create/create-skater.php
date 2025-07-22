<?php
/**
 * Template: Create or Edit Skater
 *
 * Combines create and edit functionality for a skater profile,
 * styled according to the design guide.
 *
 * @package Coach_Operating_System
 * @since 1.0.0
 */

// This function must be called before any HTML is output.
// It handles form submission and enqueues necessary scripts/styles.
acf_form_head();

// Load the dashboard-specific header. This contains the navigation and opening body tags.
include plugin_dir_path( __FILE__ ) . '../partials/header-dashboard.php';

// Redirect user if they are not logged in.
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

// --- PREPARE DATA ---

// Check if we are editing an existing skater or creating a new one.
// This looks for an 'edit_skater' query variable in the URL.
$skater_id = get_query_var( 'edit_skater' );
$is_edit   = ! empty( $skater_id ) && get_post_type( $skater_id ) === 'skater';

// Set the post_id for the form. 'new_post' for creation, or the integer ID for editing.
$post_id = $is_edit ? intval( $skater_id ) : 'new_post';

// Dynamically set the page title and button text based on the action.
$page_title  = $is_edit ? 'Update Skater Profile' : 'Create New Skater';
$submit_text = $is_edit ? 'Update Skater' : 'Create Skater';

// --- ACF FORM SETTINGS ---

$skater_form_settings = array(
    'post_id'         => $post_id,
    'post_title'      => false, // The post title field is not shown on the form.
    'post_content'    => false, // The post content editor is not shown.
    'field_groups'    => array( 'group_6819871fd44c9' ), // Use the correct 'Skater Profile' field group.
    'new_post'        => array(
        'post_type'   => 'skater',
        'post_status' => 'publish',
    ),
    'submit_value'    => $submit_text,
    'uploader'        => 'wp', // Use the built-in WordPress media uploader.
    'return'          => site_url( '/coach-dashboard' ), // Return to the main dashboard on completion.
    'updated_message' => __( 'Skater profile saved successfully.', 'wp-coach' ),
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
            acf_form( $skater_form_settings ); 
            ?>
        </div>

    </div>
</main>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
