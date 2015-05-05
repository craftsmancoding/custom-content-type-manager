<div class="cctm_def_preview">
	<form id="cctm_activate_def" method="post">
		
		<input type="hidden" name="def" id="def" value="<?php print $data['filename']; ?>" />
		<?php wp_nonce_field('cctm_activate_def', 'cctm_nonce'); ?>
		
		<h3><?php print $data['export_info']['title']; ?> <input type="submit" class="button-primary" style="float:right;" value="<?php _e('Activate!', CCTM_TXTDOMAIN); ?>"/></h3>
		
		<p><?php print $data['export_info']['description']; ?></p>
	
		<p>
			<strong><?php _e('Author', CCTM_TXTDOMAIN); ?>:</strong> <?php print $data['export_info']['author']; ?><br />
			<strong><?php _e('Author URL', CCTM_TXTDOMAIN); ?>:</strong> <a href="<?php print $data['export_info']['url']; ?>"><?php print $data['export_info']['url']; ?></a><br />
			<strong><?php _e('Template URL', CCTM_TXTDOMAIN); ?>:</strong> <a href="<?php print $data['export_info']['template_url']; ?>"><?php print $data['export_info']['template_url']; ?></a><br />
		</p>
	
		<!--!Post Types-->	
		<h4 style="text-decoration: underline;"><?php _e('Content Types', CCTM_TXTDOMAIN); ?></h4>
		<ul style="margin-left:20px;">
			<?php foreach($data['post_type_defs'] as $post_type => $def) {
//				print_r($def);
				$img = '';
				$desc = '';
				if ( in_array($post_type, CCTM::$built_in_post_types) ) {
					$desc  = __('Built-in post type.', CCTM_TXTDOMAIN);
					$img = '<img src="'. CCTM_URL .'/images/wp.png" height="16" width="16" alt="wp"/>';
				}
				elseif (!isset($def['post_type'])) {
					$desc = __('Foreign post-type', CCTM_TXTDOMAIN);
					$img = '<img src="'.CCTM_URL.'/images/forbidden.png" height="16" width="16" />';

				}
				else {
					$desc = $def['description'];
					if (isset($def['use_default_menu_icon']) && $def['use_default_menu_icon'] == 0) {
						$img = '<img src="'.$def['menu_icon'].'" height="16" width="16" />';
					}
					else {
						$img = '<img src="'.CCTM_URL.'/images/icons/post.png" height="16" width="16" />';
					}
	
				}
				$custom_fields = '';
				if (isset($def['custom_fields']) && is_array($def['custom_fields'])) {
					$custom_fields = '<br />&nbsp;&nbsp;'. __('Custom fields', CCTM_TXTDOMAIN) .': '. implode(', ', $def['custom_fields']);
				}
				printf('<li><strong>%s %s</strong>: %s%s</li>', $img, $post_type, $desc,$custom_fields);	
	 		} ?>
		</ul>
		
		<!--!Custom Fields -->
		<h4 style="text-decoration: underline;"><?php _e('Custom Fields', CCTM_TXTDOMAIN); ?></h4>
		<ul style="margin-left:20px;">
			<?php foreach($data['custom_field_defs'] as $fieldname => $d) {
				$img = '';
				$desc = '';
				$icon_src = CCTM::get_custom_icons_src_dir() . $d['type'].'.png';
				
				if ( !CCTM::is_valid_img($icon_src) ) {
					$icon_src = self::get_custom_icons_src_dir() . 'default.png';
				}
				
				$img = '<img src="'. $icon_src .'" height="16" width="16" alt="wp"/>';
				$desc = $d['description'];
				
				printf('<li><strong>%s %s</strong>: %s</li>', $img, $fieldname, $desc);	
	 		} ?>
		</ul>
		
		<br />
			
		<hr />
		<strong><?php _e('Source file', CCTM_TXTDOMAIN); ?>:</strong> <?php print $data['filename']; ?><br />
		<strong><?php _e('Created in CCTM Version', CCTM_TXTDOMAIN); ?>:</strong> <?php print $data['export_info']['_cctm_version']; ?><br />
		<strong><?php _e('Database Encoding', CCTM_TXTDOMAIN); ?>:</strong> <?php print $data['export_info']['_charset']; ?><br />
		<strong><?php _e('Date Created', CCTM_TXTDOMAIN); ?>:</strong> <?php print strftime('%Y-%m-%d %H:%M:%S', $data['export_info']['_timestamp_export']); ?><br />
	</form>
</div>