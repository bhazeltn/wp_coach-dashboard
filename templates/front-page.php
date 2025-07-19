<?php
/**
 * Template Name: CoachOS Landing Page
 */

include plugin_dir_path(__FILE__) . 'partials/header-landing.php';
?>

<main class="landing-main">
    <section class="landing-hero">
        <div class="section-inner">
            <h1>CoachOS</h1>
            <p class="tagline">The all-in-one platform for Figure Skating Development & Performance Tracking.</p>
            <p>A private, portfolio-driven tool for dedicated coaches and their skaters.</p>
            <a class="button button-primary" href="<?php echo esc_url(wp_login_url()); ?>">Coach & Skater Login</a>
        </div>
    </section>

    <section class="landing-features">
        <div class="section-inner">
            <h2>Key Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>&#128197; Yearly & Weekly Plans</h3>
                    <p>Build comprehensive, long-term training plans and break them down into actionable weekly schedules. Track macrocycles, peak planning, and goals with ease.</p>
                </div>
                <div class="feature-card">
                    <h3>&#127941; Competition Tracking</h3>
                    <p>Log competition results with detailed score breakdowns (TES, PCS, and total). Upload detail sheets and analyze performance over time.</p>
                </div>
                <div class="feature-card">
                    <h3>&#129657; Health & Injury Log</h3>
                    <p>Monitor skater well-being with a dedicated injury log. Track recovery status, severity, and return-to-skate timelines to ensure athlete health.</p>
                </div>
                 <div class="feature-card">
                    <h3>&#128202; Gap Analysis</h3>
                    <p>Systematically compare a skater's current skills against target standards in technical, mental, and physical areas to identify opportunities for growth.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-cta">
        <div class="section-inner">
            <h2>Already Have Access?</h2>
            <p>Sign in to view your dashboard and manage your training plans.</p>
            <a class="button" href="<?php echo esc_url(wp_login_url()); ?>">Sign In</a>
        </div>
    </section>
</main>

<?php
include plugin_dir_path(__FILE__) . 'partials/footer.php';
?>