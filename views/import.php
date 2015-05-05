<?php
/*------------------------------------------------------------------------------
This page lists the contents of the definition library 
(wp-content/uploads/cctm/defs)

name="_cctm_nonce" value="'. wp_create_nonce('cctm_create_update_post')
------------------------------------------------------------------------------*/
?>
<script type="text/javascript" src="<?php print CCTM_URL; ?>/js/preview_def.js"></script>

<table>
	<tr>
		<td width="400" style="vertical-align: top;">
<!-- Column 1 -->
<h2><?php _e('Manage Library', CCTM_TXTDOMAIN); ?></h2>


<p><?php 
$upload_dir = wp_upload_dir();
$upload_dir_str = 'wp-content/uploads';
// it might come back something like 
// Array ( [error] => Unable to create directory /path/to/wp-content/uploads/2011/10. Is its parent directory writable by the server? )
if (isset($upload_dir['basedir'])) {
	$upload_dir_str = $upload_dir['basedir'];
}


printf( __('You can import an existing %s definition file from your computer or choose one from your uploads directory: %s. You probably will only use this when you are first setting up your site.', CCTM_TXTDOMAIN)
	, '<code>.cctm.json</code>'
	, '<code>'.$upload_dir_str.'/'.self::base_storage_dir .'/'.self::def_dir.'</code>'
	); ?></p>

<h3><?php _e('Definitions on File', CCTM_TXTDOMAIN); ?></h3>
<div id="cctm_library">

	<form id="manage_defs" method="post">
	
	<table>		
<?php
//------------------------------------------------------------------------------
// Loop over definitions
//------------------------------------------------------------------------------
		
		$i = 0;
		$class = '';
		foreach ($data['defs_array'] as $file) {
			if ( $i & 1) {
				$class = 'cctm_evenrow';	
			}
			else {
				$class = 'cctm_oddrow';						
			}
			printf('
				<tr class="%s">
					<td class="cctm_data">
						<input type="checkbox" name="defs[]" id="cctm_def_%s" value="%s" /> <label for="cctm_def_%s">%s</label>
					</td>
					<td>
						<span class="button" onclick="javascript:preview_def(\'%s\');">%s</span>
					</td>
				</tr>
			'
				, $class
				, $i
				, $file
				, $i
				, $file
				, $file
				, __('Preview')
			);
			$i = $i + 1;
		}

		// Library empty.
		if (!$i) {
			print '<tr><td class="cctm_msg" colspan="2">'.__('Library empty.', CCTM_TXTDOMAIN) . '</td></tr>';
		}

//------------------------------------------------------------------------------		
?>
			<?php wp_nonce_field('cctm_delete_defs', 'cctm_nonce'); ?>
		</table>
		
		<br />
		<input type="submit" class="button" value="<?php _e('Delete Selected', CCTM_TXTDOMAIN); ?>" />
		</form>

	</div>
	
<br />

<h3><?php _e('Upload Definitions', CCTM_TXTDOMAIN); ?></h3>

<form id="cctm_import_form"  method="post" enctype="multipart/form-data">
	<!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php print CCTM::max_def_file_size; ?>" />
    
	<?php wp_nonce_field('cctm_upload_def', 'cctm_nonce'); ?>

	<label for="cctm_settings_file" class="cctm_file_label"><?php _e('Upload New File', CCTM_TXTDOMAIN); ?></label><br/>
	<input type="file" id="cctm_settings_file" name="cctm_settings_file" />

	<input type="submit" name="submit" class="button" value="<?php _e('Upload'); ?>"/>
</form>


		</td>
		<td width="300" style="vertical-align: top;">
		
<!-- Column 2 -->
<h2><?php _e('Preview', CCTM_TXTDOMAIN); ?></h2>
	<div id="cctm_def_preview_target">

	</div>
		</td>
	</tr>
</table>