<?php
/*
We do some magic with JavaScript here to handle the mappings that define the metaboxes' content.
When an item is dropped into a list (each list representing a metabox), we read the "metabox"
attribute in the parent list, e.g. metabox="default".  Then we use that name to overwrite the 
value of the hidden field for that item.  In this way, we can always know the metabox that 
will contain the given field.  

Here's what our hidden fields look like:

	<input name="mapping[my_field]" value="my_metabox" />

This gives us a convenient way to map the field to the metabox. An empty value means that the field
is not used (i.e. it should not appear on this post-type). 

$metabox = $_POST['mapping'][$field];

*/
?>
<style>
	.sortable li { 
		margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; width: 220px; 
		cursor: move;
		top:50%;
	}
	.linklike {
		float:right;
	}
</style>

<script>
    jQuery(function() {
        jQuery(".sortable").sortable({
            connectWith: ".connectedSortable",
        }).disableSelection();

		// We need to dynamically fiddle with the values to establish the mapping
		jQuery(".sortable").on("sortstop", function( event, ui ) {
			var containing_metabox = ui.item.parent().attr("metabox");
			var mapping = ui.item.find('input');
			mapping.val(containing_metabox);
			//console.log(containing_metabox);
		} );
    });
    
	function set_continue_editing() {
		jQuery('#continue_editing').val(1);
		return true;
	}    
</script>

<span class="cctm_description"><?php _e('Drag unused fields from the left into the metabox where you want them to appear. You can also arrange them into the order you want.', CCTM_TXTDOMAIN); ?></span>

<form id="cctm_associations" method="post">

	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>

	<input type="hidden" name="continue_editing" id="continue_editing" value="0" />


<?php /* Ye olde table layout: 3 columns */ ?>
<table>
	<tr>
		<?php // LEFT COLUMN ?>
		<td style="vertical-align:top; width:270px; margin-right:40px;">
			<div class="metabox-holder" style="width:250px;">
				<div class="postbox">
						<h3 class="hndle" style="cursor:default;"><span><?php _e('Unused Fields', CCTM_TXTDOMAIN); ?></span></h3>
						<div class="inside">
							<span class="cctm_description"><?php _e('These fields are not used for this post type.', CCTM_TXTDOMAIN); ?></span>
							<ul metabox="" class="connectedSortable sortable">
								<?php
								print $data['unused']; 
								?>
							</ul>
			
						</div><!-- /inside -->
				</div><!-- /postbox-->
			</div><!-- /metabox-holder -->
		
		</td>
		
		<?php // CENTER COLUMN ?>
		<td style="vertical-align:top; width:270px;">

			<?php print $data['advanced_boxes']; ?>

			<?php print $data['normal_boxes']; ?>
					
		</td>
		
		<?php // RIGHT COLUMN ?>
		<td style="vertical-align:top; width:270px;">
		
			<?php print $data['side_boxes']; ?>
		
		</td>
	</tr>
</table>


	<br />
	<input type="submit" class="button-primary" id="submit" value="<?php _e('Save', CCTM_TXTDOMAIN); ?>" />
	<input type="submit" class="button" onclick="javascript:set_continue_editing();" value="<?php _e('Save and Continue Editing', CCTM_TXTDOMAIN ); ?>" />	
	<?php printf('<a href="?page=cctm&a=list_post_types" class="button">%s</a>', __('Cancel', CCTM_TXTDOMAIN) );?>

</form>