<?php

// Adding the lib directory to the include path 
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/lib/');

/**
 * Loads the class file for a given class name
 *
 * @param String, class name
 * 
 * @return @void
 */
function _autoload_application($class) {
	$file = __DIR__.'/'.str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	
	if (file_exists($file)) {
		require_once $file;
	} else {
		throw new Exception('Cannot load file: '.$file);	
	}
}

//Setting up the autoload
spl_autoload_register('_autoload_application');
