<?php
/**
 * This implements the standard behavior for all custom post-types
 *
 *
 *
 *
 */
class CCTM_DefaultPostType {
	
	public $post_type;
	
	//------------------------------------------------------------------------------
	/**
	 * @param	string	name of post-type
	 */
	public function __construct($post_type) {
		$this->post_type = $post_type;
	}
	
	//------------------------------------------------------------------------------
	//! Public
	//------------------------------------------------------------------------------
	public function print_custom_fields() {
	
	}

	public function get_definition() {
	
	}

	public function draw_meta_boxes() {
	
	}
	
	public function define_columns() {
	
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Called when ____________???
	 */
	public function print_admin_header() {
		// Show the big icon: http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=136
		if ( isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
			&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0 ) { 
			$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
			// die($baseimg); 
			if ( file_exists(CCTM_PATH . '/images/icons/32x32/'. $baseimg) ) {
				printf('
				<style>
					#icon-edit, #icon-post {
					  background-image:url(%s);
					  background-position: 0px 0px;
					}
				</style>'
				, CCTM_URL . '/images/icons/32x32/'. $baseimg);
			}
		}
	}
}
/*EOF*/