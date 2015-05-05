<tr id="field_<?php print $data['i']; ?>">
    <td>
        <input type="text" name="fields[ <?php print $data['i']; ?> ][name]" class="<?php print $data['name_class']; ?>"
        value="<?php print $data['name']; ?>" placeholder="<?php _e('samplename', CCTM_TXTDOMAIN); ?>"/>
        <input type="hidden" name="fields[ <?php print $data['i']; ?> ][description]"
        value="" />
    </td>
    <td><input type="text" name="fields[ <?php print $data['i']; ?> ][label]"
        value="<?php print ucfirst($data['name']); ?>" placeholder="<?php _e('Sample Label', CCTM_TXTDOMAIN); ?>"/></td>
    <td>
        <select name="fields[ <?php print $data['i']; ?> ][type]">
            <?php print $data['field_types']; ?>
        </select>
    </td>
    <td><span class="linklike button" onclick="javascript:remove_html('field_<?php print $data['i']; ?>');"><?php _e('Remove', CCTM_TXTDOMAIN); ?></span></td>
</tr>