<?php
/**
 * The template for displaying the footer.
 *
 * @package Wp_Coach_Dashboard
 */

?>

		<hr>
		<footer class="wp-coach-dashboard-footer">
			<p style="text-align: center; color: #666;">
				&copy; <?php echo date('Y'); ?> Hazelton Professional Services
			</p>
		</footer>

		</div><!-- .wrap -->
		<script type="text/javascript">
(function($) {
    // Make sure the ACF and editor objects are available
    if (typeof acf === 'undefined' || typeof tinymce === 'undefined') {
        return;
    }

    /**
     * This action is triggered by ACF when a field becomes visible.
     * This includes fields inside tabs, accordions, and repeaters.
     */
    acf.add_action('show_field', function( $field ){

        // Target only WYSIWYG fields.
        if( $field.is('.acf-field-wysiwyg') ){

            // Find the editor's unique ID
            var id = $field.find('.wp-editor-area').attr('id');

            // Get the editor instance
            var editor = tinymce.get(id);

            // If the editor exists, remove and re-add it.
            // This forces it to re-initialize correctly in the now-visible container.
            if(editor){
                editor.remove();
                editor = tinymce.init( tinymce.settings[id] );
            }
        }
    });

})(jQuery);
</script>
		<?php wp_footer(); ?>
	</body>
</html>
