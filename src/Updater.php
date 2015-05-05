<?php
/**
 * This handles running updates.  
 *
 * WordPress lacks an "onUpdate" event, so this is a home-rolled way I can run
 * a specific bit of code when a new version of the plugin is installed. The way
 * this works is the names of all files inside of the updates/ folder are loaded
 * into an array, e.g. 0.9.4, 0.9.5.  When the first new page request comes through
 * WP, the database is in the old state, whereas the code is new, so the database
 * will say e.g. that the plugin version is 0.1 and the code will say the plugin version
 * is 0.2.  All the available updates are included and their contents are executed
 * in order.  This ensures that all update code is run sequentially.
 *
 * Any version prior to 0.9.4 is considered "version 0" by this process.
 
 * @package CCTM
 */
namespace CCTM;

class Updater {

    public static $Log;
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(object $Log) {
        self::$Log = $Log;
    }
        
        
    public static function run($new_version) {
    
		// set the flag (any update script should test for this constant and exit if it is not set)
		define('CCTM_UPDATE_MODE', 1);
		CCTM\Selfcheck::run();
		// Load up available updates in order 
		// Older systems don't have FilesystemIterator
		// $updates = new FilesystemIterator(CCTM_PATH.'/updates', FilesystemIterator::KEY_AS_PATHNAME);
		$updates = scandir(CCTM_PATH.'/updates');
		foreach ($updates as $file) {
            // Skip the gunk
            if ($file === '.' || $file === '..') continue;
            if (is_dir(CCTM_PATH.'/updates/'.$file)) continue;
            if (substr($file, 0, 1) == '.') continue;
            // skip non-php files
            if (pathinfo(CCTM_PATH.'/updates/'.$file, PATHINFO_EXTENSION) != 'php') continue;
            // We don't want to re-run older updates
            $this_update_ver = substr($file, 0, -4);
            if ( version_compare( self::get_stored_version(), $this_update_ver, '<' ) ) {
                    // Run the update by including the file
                    include CCTM_PATH.'/updates/'.$file;
                    // timestamp the update
                    self::$data['cctm_update_timestamp'] = time(); // req's new data structure
                    // store the new version after the update
                    self::$data['cctm_version'] = $this_update_ver; // req's new data structure
                    update_option( self::db_key, self::$data );
            }
		}

		// Clear the cache and such
		unset(self::$data['cache']);
		unset(self::$data['warnings']);
		// Mark the update
		self::$data['cctm_version'] = self::get_current_version();
		update_option(self::db_key, self::$data);
		
    }

}