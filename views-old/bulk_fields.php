<style>
    <?php print $data['style']; ?>
</style>
<p><?php _e('Add fields below by clicking the icons, or enter some criteria to auto-detect fields from data in your database.', CCTM_TXTDOMAIN); ?></p>

<form action="" method="post">
    <?php
    /* Some crazy WP error: if you post data using the key "post_type", WP kacks. */
    ?>
    <label for="post_type_pad"><?php _e('Post Type', CCTM_TXTDOMAIN); ?></label>
    <select name="post_type_pad" id="post_type_pad">
        <option value=""><?php _e('Choose one', CCTM_TXTDOMAIN); ?></option>
        <?php
        foreach ($data['results'] as $i => $r):
        ?>
            <option value="<?php print $r->post_type; ?>"><?php print $r->post_type; ?></option>
        <?php		
        endforeach;
        ?>
    </select>
    &nbsp;
    &nbsp;
    <label for="post_ids">Post IDs (comma-seprated)</label>
    <input type="text" name="post_ids" id="post_ids" placeholder="1,2,3..."/>
    <?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
    <input type="submit" value="<?php _e('Lookup Fields', CCTM_TXTDOMAIN); ?>"/>
</form>
<br/>
<form action="" method="post">
<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>

<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope="col" class=""  style="width: 40px;"><?php _e('Name', CCTM_TXTDOMAIN); ?>*</th>
		<th scope="col" class=""  style="width: 40px;"><?php _e('Label', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" class=""  style="width: 40px;"><?php _e('Type', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" class=""  style="width: 10px;">&nbsp;</th>
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" class=""  style="width: 40px;"><?php _e('Name', CCTM_TXTDOMAIN); ?>*</th>
		<th scope="col" class=""  style="width: 40px;"><?php _e('Label', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" class=""  style="width: 40px;"><?php _e('Type', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" class=""  style="width: 10px;">&nbsp;</th>	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php print $data['fields']; ?>	

</tbody>
</table>

<br/>
    <input type="submit" class="button-primary" value="<?php _e('Save Fields', CCTM_TXTDOMAIN ); ?>" />

</form>