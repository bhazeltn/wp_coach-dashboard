<?php
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
$user_roles = $current_user->roles;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Skater Planning Dashboard</title>
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>../css/dashboard-style.css">
    <?php wp_head(); ?>
</head>
<body>
    <header class="dashboard-header">
        <div class="header-inner">
            <div class="site-title">
                <a href="<?php echo esc_url(site_url('/')); ?>">Coach<span style="font-weight: 300;">OS</span></a>
                <span class="site-tagline">Figure Skating Development & Performance Tracking</span>
            </div>
            <nav class="dashboard-nav">
                <ul>
                    <?php if (in_array('coach', $user_roles)) : ?>
                        <li><a href="<?php echo esc_url(site_url('/coach-dashboard')); ?>">Coach Dashboard</a></li>
                    <?php endif; ?>

                    <?php if (in_array('skater', $user_roles)) : ?>
                        <li><a href="<?php echo esc_url(site_url('/skater-dashboard')); ?>">Skater Dashboard</a></li>
                    <?php endif; ?>

                    <!-- Add other role views as needed -->

                    <li><a href="<?php echo wp_logout_url(site_url('/')); ?>">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="dashboard-content">
