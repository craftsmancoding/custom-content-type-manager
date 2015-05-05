<li class="<?php print $data['class']; ?>"><img src="<?php print $data['icon']; ?>" height="24" width="24" style="vertical-align:middle;"/> <?php print $data['label']; ?> 
	<span><?php print $data['edit_field_link']; ?></span>
	<input type="hidden" name="mapping[<?php print $data['name']; ?>]" value="<?php print $data['metabox']; ?>"/>
</li>