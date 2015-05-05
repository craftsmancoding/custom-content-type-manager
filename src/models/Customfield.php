<?php
/**
 * A CRUD-ish container for managing custom fields and their definitions.
 * 
	protected $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'output_filter' => '',
		'validator' => '',
		'validator_options' => '',
	) 
 
 * @package CCTM
 */
namespace CCTM\Models;
use CCTM;
class Customfield extends Model{
    
    public static function create() {
    
    }
    
    public static function destroy($name) {
    
    }
    
    public static function duplicate($name,$newname='') {
    
    }
    
    public static function getOne($name) {
    
    }
    
    public static function getAll($criteria=array()) {
        self::$Log->debug(__FUNCTION__, __CLASS__, __LINE__);
        return array();
    }
    
    public static function getTypes() {
    
    }
    
    public static function rename($name,$newname) {
    
    }

}