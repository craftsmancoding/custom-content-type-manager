	<div class="wrap">
		<form method="post" action="options.php">
		
			<?php settings_fields('cctm_license'); ?>
			
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="cctm_license_key" name="cctm_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $data['license'] ); ?>" />
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">

						</td>
					</tr>
					<?php if( false !== $data['license'] ) { ?>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $data['status'] !== false && $data['status'] == 'valid' ) { ?>
									<span style="color:green;"><?php _e('Active'); ?></span>
									<p>Thank you for purchasing a license for the CCTM!</p>
								<?php } else {
									wp_nonce_field('edd_nonce', 'edd_nonce'); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		
		</form>
