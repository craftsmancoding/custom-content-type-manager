<?php
/*
Used when populating metaboxes with custom-fields
*/
?>
<div class="metabox-holder" style="width:250px; padding-right:9px;">
	<div class="postbox">
		<h3 class="hndle"><span><?php print $data['title']; ?></span> <a class="linklike" href="<?php print $data['edit_metabox_link']; ?>"><?php _e('Edit', CCTM_TXTDOMAIN); ?></a></h3>
		<div class="inside">

			<ul metabox="<?php print $data['metabox']; ?>" class="connectedSortable sortable" style="min-height:30px;">
				<?php print $data['items']; ?>
			</ul>

		</div><!-- /inside -->
	</div><!-- /postbox-->
</div><!-- /metabox-holder -->