<tr id="cctm_custom_field_<?php print $data['name']; ?>" class="active<?php print $data['class'];?>">
	<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><br />
		<input name="custom_fields[]" id="custom_field_<?php print $data['name']; ?>" value="<?php print $data['name']; ?>" type="checkbox" <?php print $data['is_checked']; ?>/>
	</td>
	<td><label for="custom_field_<?php print $data['name']; ?>"><?php print $data['icon']; ?></label></td>
	<td class="plugin-title">
		<strong><?php print $data['label']; ?></strong> (<?php print $data['name']; ?>)
	</td>
	<td class="column-description desc">
		<div class="plugin-description">
			<p><?php print $data['description']; ?></p>
		</div>
		<div class="active second plugin-version-author-uri">
			<p><?php print $data['edit_field_link']; ?></p>
		</div>

	</td>
</tr>