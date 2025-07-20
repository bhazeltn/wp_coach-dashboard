<?php
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
$user_roles = (array) $current_user->roles;
$is_coach = in_array('coach', $user_roles) || in_array('administrator', $user_roles);
$is_skater = in_array('skater', $user_roles);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachOS Dashboard</title>
    <?php 
    // This function is crucial. It allows WordPress to add all enqueued styles and scripts.
    wp_head(); 
    ?>
</head>
<body class="dashboard-body">
    <header class="dashboard-header">
        <div class="header-inner">
            <div class="site-title">
                <a href="<?php echo esc_url(site_url('/')); ?>">Coach<span>OS</span></a>
            </div>
            <nav class="dashboard-nav">
                <ul>
                    <?php if ($is_coach) : ?>
                        <li><a href="<?php echo esc_url(site_url('/coach-dashboard')); ?>">Coach Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo wp_logout_url(home_url()); ?>">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="dashboard-content">
