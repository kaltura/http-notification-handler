<?php

/**
 * @namespace
 */
namespace Kaltura\Output;

/**
 * Log class
 *
 * @package Kaltura
 * @subpackage Output
 */
class Log extends Output
{
	/**
	 * @var String: default log directory
	 */
	const LOG_DIR = 'log';

	/**
	 * @var String: default log filename
	 */
	const DEFAULT_FILENAME = 'notification.log';

	/**
	 * @var String: log filepath
	 */
	public $filePath = null;

	/**
 	 * Constructor
 	 *
 	 * @param String, log filepath
 	 * 
 	 * @return @void
 	 */
	public function __construct($filePath = '') {
		$this->filePath = empty($filePath) ? $filePath = self::LOG_DIR.DIRECTORY_SEPARATOR.date('Ymd\_').self::DEFAULT_FILENAME : $filePath;
	}

	/**
 	 * Write into log file and go to new line
 	 *
 	 * @param String, message you want to log
 	 * 
 	 * @return @void
 	 */
	public static function writeln($msg) {
		self::write($msg);
		self::write(PHP_EOL);
	}

	/**
 	 * Write into log file
 	 *
 	 * @param String, message you want to log
 	 * 
 	 * @return @void
 	 */
	public static function write($msg) {
		$log = new Log();

		$fp = fopen($log->filePath, 'a');
		
		if ($fp === false) {
			throw new \Exception('Error while trying log.');
		}

		fwrite($fp, self::toString($msg));
		fclose($fp);
	}

	/**
 	 * Write a timestamp and a 'Start' stamp into log file
 	 *
 	 * @param String, log filename
 	 * 
 	 * @return @void
 	 */
	public function start($fileName = '') {
		self::writeln('['.date('Y\/m\/d H:m:i').'] == Start ==', $fileName);
	}

	/**
 	 * Write a timestamp and a 'End' stamp into log file
 	 *
 	 * @param String, log filename
 	 * 
 	 * @return @void
 	 */
	public function end($fileName = '') {
		self::writeln('['.date('Y\/m\/d H:m:i').'] == End ==', $fileName);
	}

	/**
 	 * Remove log file
 	 *
 	 * @return @void
 	 */
	public static function delete() {
		$log = new Log();

		if (file_exists($log->filePath)) {
			return unlink($log->filePath);
		}
		return false;
	}
}
