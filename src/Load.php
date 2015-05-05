<?php
/**
 * Load any file that allows for substitutions. E.g. A user may override the given 
 * file by placing a file of the same name in the same relative structure inside the 
 * wp-content/uploads/cctm/ folder.
 *
 * Also handle any instantiations using the "new" keyword that cannot be handled via
 * dependency injection.
 */
namespace CCTM;
class Load {

    public static $Log;
    public static $DIC;
        
    /**
     * @object Pimple dependency container
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$DIC = $dependencies;
        self::$Log->debug('Construct.', __CLASS__, __LINE__);
    }

    /** 
     * Instantiate a controller.
     *
     *
     */
    public function controller($name) {
        $classname = '\\CCTM\\Controllers\\'.$name;
        if (!class_exists($classname)) {
            self::$Log->error('Controller class not found: '.$name, __CLASS__,__LINE__);
            return false;
        }
        return new $classname(self::$DIC);
    }


    /** 
     * Instantiate a model.
     *
     *
     */
    public function model($name) {
        $classname = '\\CCTM\\Models\\'.$name;
        if (!class_exists($classname)) {
            self::$Log->error('Model class not found: '.$name, __CLASS__,__LINE__);
            return false;
        }
        return new $classname(self::$DIC);
    }
    
	//------------------------------------------------------------------------------
	/**
	 * When given a PHP file name relative to the CCTM_PATH, e.g. '/config/image_search_parameters.php',
	 * this function will include (or require) that file. However, if the same file exists
	 * in the same location relative to the wp-content/uploads/cctm directory, THAT version of 
	 * the file will be used. E.g. calling load_file('test.php') will include 
	 * wp-content/uploads/cctm/test.php (if it exists); if the file doesn't exist in the uploads
	 * directory, then we'll look for the file inside the CCTM_PATH, e.g.
	 * wp-content/plugins/custom-content-type-manager/test.php 
	 *
	 * The purpose of this is to let users use their own version of files by placing them in a 
	 * location *outside* of this plugin's directory so that the user-created files will be safe
	 * from any overriting or deleting that may occur if the plugin is updated.
	 *
	 * Developers of 3rd party components can supply $additional_paths if they wish to load files
	 * in their components: if the $additional_path is supplied, this directory will be searched for tpl in question.
	 *
	 * To prevent directory transversing, file names may not contain '..'!
	 *
	 * @param	array|string	$files: filename relative to the path, e.g. '/config/x.php'. Should begin with "/"
	 * @param	array|string	(optional) $additional_paths: this adds one more paths to the default locations. OMIT trailing /, e.g. called via dirname(__FILE__)
	 * @param	string			$load_type (optional) include|include_once|require|require_once -- default is 'include'
	 * @param	mixed	file name used on success, false on fail.
	 */
	public static function file($files, $additional_paths=array(), $load_type='include') {

		if (!is_array($files)){
			$files = array($files);
		}

		if (!is_array($additional_paths)){
			$additional_paths = array($additional_paths);
		}
		
		// Populate the list of directories we will search in order. 
		$upload_dir = wp_upload_dir();
		$paths = array();
		$paths[] = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir;
		$paths[] = CCTM_PATH;
		$paths = array_merge($paths, $additional_paths);

		// pull a file off the stack, then look for it
		$file = array_shift($files);
		
		if (preg_match('/\.\./', $file)) {
			die( sprintf(__('Invaid file name! %s  No directory traversing allowed!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($file).'</em>'));
		}
		
		if (!preg_match('/\.php$/', $file)) {
			die( sprintf(__('Invaid file name! %s  Name must end with .php!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($file).'</em>'));
		}		
		
		// Look through the directories in order.
		foreach ($paths as $dir) {
			if (file_exists($dir.$file)) {
				// Variable functions didn't seem to work here.
				switch ($load_type) {
					case 'include':
						include $dir.$file;
						break;
					case 'include_once':
						include_once $dir.$file;
						break;
					case 'require':
						require $dir.$file;
						break;
					case 'require_once':
						require_once $dir.$file;
						break;
				}
				
				return $dir.$file;
			}
		}
		
		// Try again with the remaining files... or fail.
		if (!empty($files)) {
			return self::file($files, $additional_paths, $load_type);
		}
		else {
			return false;
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Similar to the "view" function, this retrieves a tpl.  It allows users to
	 * override the built-in tpls (stored in the plugin's directory) with tpls stored
	 * in the wp uploads directory.
	 *
	 * If you supply an array of arguments to $name, the first tpl (in the array[0] position)
	 * will be looked for first in the customized directories, then in the built-ins.  If nothing
	 * is found, the array is shifted and the next item in the array is looked for, first in the 
	 * customized locations, then in the built-in locations.  By shifting the array, you can specify
	 * a hierarchy of "fallbacks" to look for with any tpl.
	 *
	 * Developers of 3rd party components can supply additional paths $path if they wish to use tpls
	 * in their components: if the $additional_path is supplied, this directory will be searched for tpl in question.
	 *
	 * To prevent directory transversing, tpl names may not contain '..'!
	 *
	 * @param	array|string	$name: single name or array of tpl names, each relative to the path, e.g. 'fields/date.tpl'. The first one in the list found will be used.
	 * @param	array|string	(optional) $additional_paths: this adds one more path to the default locations. OMIT trailing /, e.g. called via dirname(__FILE__)
	 * @return	string	the file contents (not parsed) OR a boolean false if nothing was found.
	 */
	public static function tpl($tpls, $additional_paths=array()) {

		if (!is_array($tpls)){
			$tpls = array($tpls);
		}
		if (!is_array($additional_paths)){
			$additional_paths = array($additional_paths);
		}
		
		// Populate the list of directories we will search in order. 
		$upload_dir = wp_upload_dir();
		$paths = array();
		$paths[] = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir.'/tpls';
		$paths[] = CCTM_PATH.'/tpls';
		$paths = array_merge($paths, $additional_paths);

		// Pull the tpl off the stack
		$tpl = array_shift($tpls);

		if (preg_match('/\.\./', $tpl)) {
			die( sprintf(__('Invaid tpl name! %s  No directory traversing allowed!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($tpl).'</em>'));
		}
		
		if (!preg_match('/\.tpl$/', $tpl)) {
			die( sprintf(__('Invaid tpl name! %s  Name must end with .tpl!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($tpl).'</em>'));
		}		
		
		// Look through the directories in order.
		foreach ($paths as $dir) {
			if (file_exists($dir.'/'.$tpl)) { 
				return file_get_contents($dir.'/'.$tpl);
			}
		}

		// Try again with the remaining tpls... or fail.
		if (!empty($tpls)) {
			return self::tpl($tpls, $additional_paths);
		}
		else {
			return false;
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the views/ directory)
	 * @param array   $data (optional) associative array of data
	 * @param string  $path (optional) pathname. Can be overridden for 3rd party fields
	 * @return string the parsed contents of that file
	 */
	public static function view($filename, $data=array(), $path=null) {
		if (empty($path)) {
			$path = CCTM_PATH . '/views/';
		}
		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			return ob_get_clean();
		}
		die('View file does not exist: ' .$path.$filename);
	}
}