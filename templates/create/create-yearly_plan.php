<?php
/**
 * Template: Create or Edit Yearly Plan
 *
 * Combines create and edit functionality for a yearly plan,
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

// Check if we are editing an existing yearly plan or creating a new one.
$yearly_plan_id = get_query_var( 'edit_yearly_plan' );
$is_edit        = ! empty( $yearly_plan_id ) && get_post_type( $yearly_plan_id ) === 'yearly_plan';

// Set the post_id for the form.
$post_id = $is_edit ? intval( $yearly_plan_id ) : 'new_post';

// Determine the skater ID for the return URL and for pre-populating the form.
$skater_id_for_return = null;
if ( $is_edit ) {
    $skater_post = get_field( 'skater', $post_id );
    if ( $skater_post ) { // Skater field returns an object for this CPT
        $skater_id_for_return = $skater_post->ID;
    }
} elseif ( isset( $_GET['skater_id'] ) ) {
    $skater_id_for_return = intval( $_GET['skater_id'] );
}

// Construct the return URL.
$return_url = $skater_id_for_return
    ? get_permalink( $skater_id_for_return )
    : site_url( '/coach-dashboard' );

// Dynamically set the page title and button text.
$page_title  = $is_edit ? 'Update Yearly Plan' : 'Create New Yearly Plan';
$submit_text = $is_edit ? 'Update Plan' : 'Create Plan';


// --- ACF FORM SETTINGS ---

$yearly_plan_form_settings = array(
    'post_id'         => $post_id,
    'post_title'      => true, // Yearly Plan uses the title for the season (e.g., "2024-2025").
    'post_content'    => false,
    'field_groups'    => array( 'group_681991c0d3817' ), // Field group for 'Yearly Plan Details' from your JSON file.
    'new_post'        => array(
        'post_type'   => 'yearly_plan',
        'post_status' => 'publish',
    ),
    'submit_value'    => $submit_text,
    'uploader'        => 'wp',
    'return'          => $return_url,
    'updated_message' => __( 'Yearly plan saved successfully.', 'wp-coach' ),
);

// Pre-populate the skater field if creating a new plan from a skater's page.
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
            // Render the ACF form.
            acf_form( $yearly_plan_form_settings ); 
            ?>
        </div>

    </div>
</main>

<?php
// Load the plugin's custom footer.
include plugin_dir_path( __FILE__ ) . '../partials/footer.php';
?>
