<?php
/**
 * The header for the dashboard templates.
 *
 * This file contains the head content and the opening body and header tags.
 * It also includes the logic for enqueuing dashboard-specific styles.
 *
 * @package Coach_Operating_System
 */

// --- Enqueue Styles ---
// The proper WordPress way to add stylesheets is to enqueue them.
// This function will be hooked into the 'wp_enqueue_scripts' action.
if ( ! function_exists( 'coachos_enqueue_dashboard_styles' ) ) {
    function coachos_enqueue_dashboard_styles() {
        // Enqueue the main dashboard stylesheet.
        // plugin_dir_url( __FILE__ ) gets the URL to the current file's directory.
        wp_enqueue_style(
            'coachos-dashboard-style', // A unique handle for the stylesheet.
            plugin_dir_url( __FILE__ ) . '../../css/dashboard-style.css', // The correct, reliable path to the file.
            array(), // Dependencies, if any.
            '1.0.0' // Version number.
        );
    }
    // Add the function to the action hook.
    add_action( 'wp_enqueue_scripts', 'coachos_enqueue_dashboard_styles' );
}


if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url() );
    exit;
}

$current_user = wp_get_current_user();
$user_roles   = (array) $current_user->roles;
$is_coach     = in_array( 'coach', $user_roles, true ) || in_array( 'administrator', $user_roles, true );
$is_skater    = in_array( 'skater', $user_roles, true );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachOS Dashboard</title>
    <?php
    // This function is crucial. It allows WordPress to add all enqueued styles (like ours) and scripts.
    wp_head();
    ?>
</head>
<body class="dashboard-body">
    <header class="dashboard-header">
        <div class="header-inner">
            <div class="site-title">
                <a href="<?php echo esc_url( site_url( '/' ) ); ?>">Coach<span>OS</span></a>
            </div>
            <nav class="dashboard-nav">
                <ul>
                    <?php if ( $is_coach ) : ?>
                        <li><a href="<?php echo esc_url( site_url( '/coach-dashboard' ) ); ?>">Coach Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="dashboard-content">
