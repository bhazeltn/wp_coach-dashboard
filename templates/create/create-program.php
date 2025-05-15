<?php
/* Plugin-based Create Program Template */
get_header();

if (!is_user_logged_in()) {
    echo '<p>You must be logged in to create a program.</p>';
    get_footer();
    exit;
}

// Set up ACF form configuration
acf_form_head();
?>

<div class="wrap">
    <h1>Create New Program</h1>

    <?php
    // Create a blank post in memory, ACF will populate it
    $new_post = array(
        'post_id' => 'new_post',
        'post_title' => true,
        'post_content' => false,
        'new_post' => array(
            'post_type' => 'program',
            'post_status' => 'publish'
        ),
        'submit_value' => 'Create Program',
        'return' => home_url('/program') // redirect after save
    );

    acf_form($new_post);
    ?>
</div>

<?php get_footer(); ?>
