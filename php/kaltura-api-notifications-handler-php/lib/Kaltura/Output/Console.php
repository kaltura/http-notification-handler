<?php

/**
 * @namespace
 */
namespace Kaltura\Output;

/**
 * Console class
 *
 * The console logs stuf as well as outputting it to the stdOut (optional)
 * 
 * @package Kaltura
 * @subpackage Output
 */
class Console
{
	/**
	 * @var String: file name used by the console
	 */
	public $fileName = '';

	/**
	 * @var Boolean: whether or not to ouput on the stdOut
	 */
	public $stdOut = false;
	
	/**
 	 * Constructor
 	 *
 	 * @param Boolean, whether or not to ouptut to the stdOut
 	 * 
 	 * @return @void
 	 */
	public function __construct($stdOut = false) {
		$this->stdOut = $stdOut;
	}

	/**
 	 * Log (and output) the passed in string 
 	 *
 	 * @param String, stuff you want to log
 	 * 
 	 * @return @void
 	 */
	public function log($msg = '') {
		Log::writeln($msg);

		if ($this->stdOut) {
			$this->write($msg);
		}
	}

	/**
 	 * Prepend a timestamp with a 'START' stamp and log (and output) the passed in string 
 	 *
 	 * @param String, stuff you want to log
 	 * 
 	 * @return @void
 	 */
	public function startLog($msg = '') {
		$this->timeLog('==> START');
		
		if ($msg) {
			$this->log($msg);
		}

		$this->log('');
	}

	/**
 	 * Prepend a timestamp and log (and output) the passed in string 
 	 *
 	 * @param String, stuff you want to log
 	 * 
 	 * @return @void
 	 */
	public function timeLog($msg = '') {
		$this->log('['.date('Y\/m\/d H\:i\:s').'] '.$msg);
	}

	/**
 	 * Log (and output) the passed in string and append a timestamp with a 'END' stamp and  
 	 *
 	 * @param String, stuff you want to log
 	 * 
 	 * @return @void
 	 */
	public function endLog($msg = '') {
		if ($msg) {
			$this->log($msg);
		}
		$this->log('');
		$this->timeLog('==> END');
	}

	/**
 	 * Clear the log file
 	 *
 	 * @param String, stuff you want to log
 	 * 
 	 * @return @void
 	 */
	public function clearLog() {
		Log::delete();
	}

	/**
	* Write to the Standard Output
	* 
	* @param String, stuff you want to log
	* 
	* @return @void
	*/
	public function write($msg = '') {
		StandardOutput::writeln($msg);
	}

	/**
	* Write to the Standard Output prefixed by a timestamp
	* 
	* @param String, stuff you want to log
	* 
	* @return @void
	*/
	public function timeWrite($msg = '') {
		$this->write('['.date('Y\/m\/d H\:i\:s').'] '.$msg);
	}

	/**
	* Write to the Standard Output prefixed by a timestamp and a 'START' stamp
	* 
	* @param String, stuff you want to log
	* 
	* @return @void
	*/
	public function startWrite($msg = '') {
		$this->timeWrite('==> START');
		
		if ($msg) {
			$this->write($msg);
		}
	}

	/**
	* Write to the Standard Output postfixed by a timestamp and a 'END' stamp
	* 
	* @param String, stuff you want to log
	* 
	* @return @void
	*/
	public function endWrite($msg = '') {
		if ($msg) {
			$this->write($msg);
		}

		$this->timeWrite('==> END');
	}
}
