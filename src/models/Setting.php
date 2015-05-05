<?php
/**
 * 
 * 
 * @package CCTM
 */
namespace CCTM\Models;
use CCTM;
class Setting extends Model{

    public static $option_name = 'cctm_settings';
    
	public static $default_settings = array(
		'delete_posts' => 0
		, 'delete_custom_fields' => 0
		, 'show_custom_fields_menu' => 1
		, 'show_settings_menu' => 1
		, 'show_foreign_post_types' => 1
		, 'cache_directory_scans' => 1
		, 'cache_thumbnail_images' => 0
		, 'save_empty_fields' => 1
		, 'summarizeposts_tinymce' => 1
		, 'custom_fields_tinymce' => 1
		, 'pages_in_rss_feed'	=> 0
		, 'enable_right_now'	=> 1
		, 'hide_posts'	=> 0
		, 'hide_pages'	=> 0
		, 'hide_links'	=> 0
		, 'hide_comments' => 0
	);
    
    public function getAll($criteria=array()) {
        self::$Log->debug(__FUNCTION__, __CLASS__, __LINE__);
        return call_user_func(self::$get_option_function, self::$option_name, self::$default_settings);
            // fromArray?
//        return self::$Data;
    }
    

    public function save($args) {
        call_user_func(self::$update_option_function, self::$option_name, self::$Data);
        return false;
    }
}