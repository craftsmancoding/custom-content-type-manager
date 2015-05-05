<?php
/**
 * This class acts as an alias/pass-thru to the CCTM_DefaultPostType class or any
 * optional overrides.  We require a point of abstraction here so we can
 * allow for optional overriding of the default classes and their behaviors.
 *
 * Developers can specify programmatically behavior for each post-type by creating 
 * a file/class named after each post-type, e.g.
 * 		CCTM_my_post_type.php
 *		class CCTM_my_post_type extends CCTM_DefaultPostType { }
 *
 * OR they can globally override the default behavior globally for all post-types
 * by implementing the follwing file/class:
 *		CCTM_AllPostTypes.php
 *		class CCTM_AllPostTypes extends CCTM_DefaultPostType { }
 *
 * AND devlopers could do both: globally override all default behavior and possibly
 * further override behavior based on post-type by doing both the above.
 *		CCTM_my_post_type.php
 *		class CCTM_my_post_type extends CCTM_AllPostTypes { }
 *
 * References:
 * http://www.phppatterns.com/docs/develop/php_and_variable_references
 * http://weierophinney.net/matthew/archives/131-Overloading-arrays-in-PHP-5.2.0.html
 */
class CCTM_AbstractPostType
{

	private $_Instance;

	//------------------------------------------------------------------------------	
	public function __call($name, $args='')
	{
		return call_user_func_array (array (&$this->_Instance, $name), $args);
	}

	//------------------------------------------------------------------------------
	/**
	 * @param	string	 the name of the post-type being instantiated.
	 */
	public function __construct($post_type)
	{
		// include the parent base class (i.e. the default)
		include_once(CCTM_PATH.'/includes/CCTM_DefaultPostType.php');
		
		// Check for an override for the entire CCTM_DefaultPostType.php, named CCTM_AllPostTypes
		$override = CCTM_PATH."/addons/CCTM_AllPostTypes.php";
		if ( file_exists($override) ) 
		{
			include_once($override);
			$class = 'CCTM_AllPostTypes';
		}
		
		
		// Check for an override for the given $post_type
		$override = CCTM_PATH."/addons/$post_type.php";
		if ( file_exists($override) ) 
		{
			include_once($override);
			$class = 'CCTM_'.$post_type;  
		}
		

		$this->_Instance = new $class($post_type);
		
	}
	//------------------------------------------------------------------------------
	public function __get($name)
	{
		return $this->_Instance->$name;
	}
	//------------------------------------------------------------------------------
	public function __isset($name) 
	{
		return isset($this->_Instance->$name);
	}
	//------------------------------------------------------------------------------
	public function __set($name, $value)
	{
		$this->_Instance->$name = $value;
	}
	//------------------------------------------------------------------------------
	public function __unset($name) 
	{
		unset($this->_Instance->$name);
	}
}
/*EOF*/