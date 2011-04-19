<?php
/*
Plugin Name: Arid
Plugin URI: http://www.davewidmer.net
Description: The Arid plugin allows for rapid DRY theme development.
Version: 1.0
Author: Dave Widmer
Author URI: http://www.davewidmer.net
License: BSD (http://creativecommons.org/licenses/BSD/)
*/

/**
 * Most of the functionality for this plugin is taken from the Kohana Framework
 * so please give credit to them.
 *
 * @see http://www.kohanaframework.org
 */

// Define the ARID constant for script security
define('ARID', true);

// Run the init
Arid::init();

/**
 * The Arid class is the main class for the plugin.
 * It basically mimics a very small subset of the Kohana Framework
 *
 * @package Arid
 * @author	Dave Widmer
 * @copyright	2011 © Dave Widmer
 */
class Arid
{
	/** A list of files that have been loaded. */
	protected static $_files = array();
	
	/** A list of classes to search when autoloading a class. */
	protected static $_paths = array();

	/**
	 * Initialization function
	 */
	public static function init()
	{
		// Add in the search paths
		// By default it is this active theme then the arid plugin dir
		Arid::$_paths = array(
			get_template_directory().DIRECTORY_SEPARATOR,
			WP_PLUGIN_DIR.DIRECTORY_SEPARATOR."arid".DIRECTORY_SEPARATOR,
		);

		// Setup the autoloader
		spl_autoload_register(array('Arid', 'auto_load'));
	}

	/**
	 * Auto-loads classes based on the Kohana's class naming conventions.
	 *
	 * @param	{string}	$class	The name of the class to autoload
	 * @return	{boolean}			Successful load of the class?
	 */
	public static function auto_load($class)
	{
		// Transform the class name into a path
		$file = str_replace('_', '/', strtolower($class));

		$path = Arid::find_file('classes', $file);

		if ($path)
		{
			// Load the class file
			require $path;

			// Class has been found
			return TRUE;
		}

		// Class is not in the filesystem
		return FALSE;
	}

	/**
	 * Searches for a file in the [Cascading Filesystem](kohana/files), and
	 * returns the path to the file that has the highest precedence, so that it
	 * can be included.
	 *
	 * When searching the "config", "messages", or "i18n" directories, or when
	 * the `$array` flag is set to true, an array of all the files that match
	 * that path in the [Cascading Filesystem](kohana/files) will be returned.
	 * These files will return arrays which must be merged together.
	 *
	 * If no extension is given, the default extension (`EXT` set in
	 * `index.php`) will be used.
	 *
	 *     // Returns an absolute path to views/template.php
	 *     Kohana::find_file('views', 'template');
	 *
	 *     // Returns an absolute path to media/css/style.css
	 *     Kohana::find_file('media', 'css/style', 'css');
	 *
	 *     // Returns an array of all the "mimes" configuration files
	 *     Kohana::find_file('config', 'mimes');
	 *
	 * @param   string   directory name (views, i18n, classes, extensions, etc.)
	 * @param   string   filename with subdirectory
	 * @param   string   extension to search for
	 * @param   boolean  return an array of files?
	 * @param	boolean		Cache the file paths?
	 * @return  array    a list of files when $array is TRUE
	 * @return  string   single file path
	 */
	public static function find_file($dir, $file, $ext = "php", $array = FALSE, $caching = TRUE)
	{
		if (strpos($ext, ".") === FALSE)
		{
			// Prefix the extension with a period
			$ext = ".{$ext}";
		}

		// Create a partial path of the filename
		$path = $dir.DIRECTORY_SEPARATOR.$file.$ext;

		if ($caching === TRUE AND isset(Arid::$_files[$path.($array ? '_array' : '_path')]))
		{
			// This path has been cached
			return Arid::$_files[$path.($array ? '_array' : '_path')];
		}

		// The file has not been found yet
		$found = FALSE;

		foreach (Arid::$_paths as $dir)
		{
			if (is_file($dir.$path))
			{
				// A path has been found
				$found = $dir.$path;

				// Stop searching
				break;
			}
		}

		// If caching is setup, then cache the path
		if ($caching === TRUE)
		{
			// Add the path to the cache
			Arid::$_files[$path.($array ? '_array' : '_path')] = $found;
		}

		return $found;
	}

} // End Arid