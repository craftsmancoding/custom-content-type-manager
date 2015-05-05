<?php
class SummarizePosts_Widget extends WP_Widget {

	public $name;
	public $description;
	public $control_options = array(
		'title' => 'Posts'
	);
	
	public function __construct() {
		$this->name = __('Summarize Posts', CCTM_TXTDOMAIN);
		$this->description = __('List posts according to flexible search criteria.', CCTM_TXTDOMAIN);
		$widget_options = array(
			'classname' => __CLASS__,
			'description' => $this->description,
		);
		
		parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);

		// We only need the additional functionality for the back-end.
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=331
		//if( is_admin() && is_active_widget( false, false, $this->id_base, true )) {	
		if( is_admin() && 'widgets.php' == substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1)) {
			wp_enqueue_script('thickbox');
			wp_register_script('summarizeposts_widget', CCTM_URL.'/js/summarizeposts.js', array('jquery', 'media-upload', 'thickbox'));
			wp_enqueue_script('summarizeposts_widget');
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Create only form elements.
	 */
	public function form($instance) {
		
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$args_str = ''; // formatted args for the user to look at so they remember what they searched for.
		
		if (!isset($instance['title'])) {
			$instance['title'] = ''; 	// default value
		}
		if (!isset($instance['parameters'])) {
			$instance['parameters'] = ''; 	// default value
		}
		if (!isset($instance['formatting_string'])) {
			$instance['formatting_string'] = '<li><a href="[+permalink+]">[+post_title+]</a></li>'; 	// default value
		}

		$args = array();
		$search_parameters_str = $instance['parameters'];
		parse_str($search_parameters_str, $args);
		$Q = new GetPostsQuery($args);
		$args_str = $Q->get_args();

		
		print '<p>'.$this->description
			. '<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Widget"><img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></a></p>
			<label class="cctm_label" for="'.$this->get_field_id('title').'">'.__('Title', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.$instance['title'].'" />
			
			<p style="margin-top:10px;"><strong>'.__('Search Criteria', CCTM_TXTDOMAIN).'</strong> <span class="button" onclick="javascript:widget_summarize_posts(\''.$this->get_field_id('parameters') . '\');">'.__('Define Search', CCTM_TXTDOMAIN).'</span></p>
			
			<!-- also target for Ajax writes -->
			<div id="existing_'.$this->get_field_id('parameters').'" style="padding-left:10px;">'.
			$args_str
			.'</div>
			<input type="hidden" name="'.$this->get_field_name('parameters').'" id="'.$this->get_field_id('parameters').'" value="'.$instance['parameters'].'" />
			

			<div id="target_'.$this->get_field_id('selector').'"></div>
			
			<label class="cctm_label" for="'.$this->get_field_id('formatting_string').'">'.__('Formatting String', CCTM_TXTDOMAIN).'</label>
			<textarea name="'.$this->get_field_name('formatting_string').'" id="'.$this->get_field_id('formatting_string').'" rows="3" cols="30">'.$instance['formatting_string'].'</textarea>
			';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Process the $args to something GetPostsQuery. 
	 */
	function widget($args, $instance) {
	
		if (!isset($instance['parameters']) || empty($instance['parameters'])) {
			return; // don't do anything until the search is defined.
		}
		
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$q_args = array();
		$search_parameters_str = $instance['parameters'];
		parse_str($search_parameters_str, $q_args);
		
		//print_r($q_args); exit;
		if (isset($q_args['include']) && empty($q_args['include'])) {
			unset($q_args['include']);
		}
		
		$Q = new GetPostsQuery();
		
		$results = $Q->get_posts($q_args);
		//print $Q->debug(); return;

		$output = $args['before_widget']
			.$args['before_title'].$instance['title'].$args['after_title']
			.'<ul>';
		foreach ($results as $r) {
			$output .= CCTM::parse($instance['formatting_string'], $r);
		}
		$output .= '</ul>'
		
		. $args['after_widget'];
		
		print $output;
	}

	
	//! Static
	public static function register_this_widget() {
		register_widget(__CLASS__);
	}

}

/*EOF*/