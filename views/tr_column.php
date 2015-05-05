<tr id="cctm_custom_field_<?php print $data['name']; ?>" class="active <?php print $data['class']; ?>">
	<td>
		<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
	</td>
	<td><input name="cctm_custom_columns[]" id="cctm_custom_column_<?php print $data['name']; ?>" value="<?php print $data['name']; ?>" type="checkbox" <?php print $data['is_checked']; ?>/></td>
	</td>
	<td class="plugin-title">
		<label for="cctm_custom_column_<?php print $data['name']; ?>"><?php print $data['label']; ?></label>
	</td>
	<td class="column-description desc">
		<div class="plugin-description">
			<p><?php print $data['description']; ?></p>
		</div>
	</td>
</tr>