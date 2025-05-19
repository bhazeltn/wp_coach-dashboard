<?php
// Template: yearly-plan/actions.php

$post_id = $post_id ?? get_the_ID();
?>

<hr>
<p><a class="button" href="<?= esc_url(site_url('/edit-yearly-plan/' . $post_id)) ?>">Update This Plan</a></p>
<p><a class="button" href="<?= esc_url(site_url('/coach-dashboard')) ?>">Back to Dashboard</a></p>
</div> <!-- Close .wrap.coach-dashboard -->
