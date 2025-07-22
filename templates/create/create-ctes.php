<?php
/**
 * Template: Create or Edit CTES Requirement
 *
 * Combines create and edit functionality for a CTES requirement,
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

// --- PREPARE DATA ---

// Check if we are editing an existing CTES requirement or creating a new one.
$ctes_id = get_query_var( 'edit_ctes' );
$is_edit = ! empty( $ctes_id ) && get_post_type( $ctes_id ) === 'ctes_requirement';

// Set the post_id for the form.
$post_id = $is_edit ? intval( $ctes_id ) : 'new_post';

// Construct the return URL. CTES is not tied to a skater, so we return to the dashboard.
$return_url = site_url( '/coach-dashboard' );

// Dynamically set the page title and button text.
$page_title  = $is_edit ? 'Update CTES Requirement' : 'Create New CTES Requirement';
$submit_text = $is_edit ? 'Update Requirement' : 'Create Requirement';


// --- ACF FORM SETTINGS ---

$ctes_form_settings = array(
    'post_id'         => $post_id,
    'post_title'      => true, // CTES uses the title for the season/event (e.g., "2024-2025 ISU CTES").
    'post_content'    => false,
    'field_groups'    => array( 'group_687c840672df9' ), // Field group for 'CTES' from your JSON file.
    'new_post'        => array(
        'post_type'   => 'ctes_requirement',
        'post_status' => 'publish',
    ),
    'submit_value'    => $submit_text,
    'uploader'        => 'wp',
    'return'          => $return_url,
    'updated_message' => __( 'CTES requirement saved successfully.', 'wp-coach' ),
);

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
            // Render the ACF form.
            acf_form( $ctes_form_settings ); 
            ?>
        </div>

    </div>
</main>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
