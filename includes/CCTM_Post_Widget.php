<?php
/**
 * This widget is designed to allow users to display content from a single post in a widget.
 * Functionality inspired by the Custom Post Widget plugin: 
 * 	http://wordpress.org/extend/plugins/custom-post-widget/
 */
class CCTM_Post_Widget extends WP_Widget {

	public $name;
	public $description;
	public $control_options = array(
		'title' => 'Post'
	);
	
	public function __construct() {
		$this->name = __('Post Content', CCTM_TXTDOMAIN);
		$this->description = __("Show a post's content inside of a widget.", CCTM_TXTDOMAIN);
		$widget_options = array(
			'classname' => __CLASS__,
			'description' => $this->description,
		);
		
		parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);

		// We only need the additional functionality for the back-end.
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=331
//		if( is_admin() && is_active_widget( false, false, $this->id_base, true )) {	
		if( is_admin() && 'widgets.php' == substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1)) {			
			wp_enqueue_script('thickbox');
			wp_register_script('cctm_post_widget', CCTM_URL.'/js/post_widget.js', array('jquery', 'media-upload', 'thickbox'));
			wp_enqueue_script('cctm_post_widget');	
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Create only form elements.
	 */
	public function form($instance) {
				
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$formatted_post = ''; // Formatted post
		
		if (!isset($instance['title'])) {
			$instance['title'] = ''; 	// default value
		}
		if (isset($instance['post_id']) && !empty($instance['post_id'])) {
			$Q = new GetPostsQuery();
			$post = $Q->get_post($instance['post_id']);
			$tpl = CCTM::load_tpl('widgets/post_item.tpl');
			$post['edit_selected_post_label'] = __('Edit Selected Post', CCTM_TXTDOMAIN);
			$post['post_icon'] = CCTM::get_thumbnail($instance['post_id']);
			if ($post['post_type'] == 'attachment') {
				$post['edit_url'] = get_admin_url('','media.php')."?attachment_id={$post['ID']}&action=edit";
			}
			else {
				$post['edit_url'] = get_admin_url('','post.php')."?post={$post['ID']}&action=edit";
			}
			
			$post['target_id'] = $this->get_field_id('target_id');

			$formatted_post = CCTM::parse($tpl, $post);
		}
		else {
			$instance['post_id'] = '';
		}
		if (!isset($instance['formatting_string'])) {
			$instance['formatting_string'] = '[+post_content+]'; 	// default value
		}
		if (!isset($instance['post_type'])) {
			$instance['post_type'] = 'post'; 	// default value
		}
		
		$post_types = get_post_types(array('public'=>1));
		
		$post_type_options = '';

		foreach ($post_types as $k => $v) {
			$is_selected = '';
			if ($k == $instance['post_type']) {
				$is_selected = ' selected="selected"';	
			}
			$post_type_options .= sprintf('<option value="%s" %s>%s</option>', $k, $is_selected, $v);
		}

		$is_checked = '';
		if (isset($instance['override_title']) && $instance['override_title'] ==1) {
			$is_checked = ' checked="checked"';	
		}
		print '<p>'.$this->description
			. '<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Post_Widget"><img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></a></p>
			<label class="cctm_label" for="'.$this->get_field_id('post_type').'">Post Type</label>
			<input type="hidden" id="'.$this->get_field_id('post_id').'" name="'.$this->get_field_name('post_id').'" value="'.$instance['post_id'].'" />
			<select name="'.$this->get_field_name('post_type').'" id="'.$this->get_field_id('post_type').'">
				'.$post_type_options.'
			</select><br/><br/>
			<span class="button" onclick="javascript:select_post(\''.$this->get_field_id('post_id').'\',\''.$this->get_field_id('target_id').'\',\''.$this->get_field_id('post_type').'\');">'. __('Choose Post', CCTM_TXTDOMAIN).'</span>

			<br/><br/>
			<strong>Selected Post</strong><br/>
			<!-- This is where we wrote the preview HTML -->
			<div id="'.$this->get_field_id('target_id').'">'.$formatted_post.'</div>
			<!-- Thickbox ID -->
			<div id="thickbox_'.$this->get_field_id('target_id').'"></div>
			<br/><br/>
			
			<input type="checkbox" name="'.$this->get_field_name('override_title').'" id="'.$this->get_field_id('override_title').'" value="1" '.$is_checked.'/> <label class="" for="'.$this->get_field_id('override_title').'">'.__('Override Post Title',CCTM_TXTDOMAIN).'</label><br/><br/>
			<label class="cctm_label" for="'.$this->get_field_id('title').'">'.__('Title', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.$instance['title'].'" />
			
			
			<label class="cctm_label" for="'.$this->get_field_id('formatting_string').'">'.__('Formatting String', CCTM_TXTDOMAIN).'</label>
			<textarea name="'.$this->get_field_name('formatting_string').'" id="'.$this->get_field_id('formatting_string').'" rows="3" cols="30">'.$instance['formatting_string'].'</textarea>
			';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Process the $args to something GetPostsQuery. 
	 */
	function widget($args, $instance) {
		
		// Avoid placing empty widgets
		if (!isset($instance['post_id']) || empty($instance['post_id'])) {
			return;
		} 
		
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$post_id = (int) $instance['post_id'];
		
		$Q = new GetPostsQuery();
		
		$post = $Q->get_post($post_id);
		$post['post_content'] = do_shortcode(wpautop($post['post_content']));
		
		$output = $args['before_widget'];
				
		if (isset($instance['override_title']) && $instance['override_title'] == 1) {
			$title = $instance['title'];
		}

		else {
			$title = $post['post_title']; // default is to use the post's title
		}
		
		if (!empty($title)) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}
				
		if (!empty($instance['formatting_string'])) {
			$output .= CCTM::parse($instance['formatting_string'], $post);
		}
		else {
			$output .= $post['post_content'];
		}	
		$output .= $args['after_widget'];
		
		print $output;
	}

	
	//! Static
	public static function register_this_widget() {
		register_widget(__CLASS__);
	}

}

/*EOF*/