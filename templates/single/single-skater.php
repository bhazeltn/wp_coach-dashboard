<?php
// Redirect default skater single pages to custom Skater View
$skater = get_post();
if ($skater) {
    wp_redirect(site_url('/skater/' . $skater->post_name), 301);
    exit;
}
