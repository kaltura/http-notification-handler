<?php

/**
 * @namespace
 */
namespace Kaltura\Output;

/**
 * StandardOutput class
 *
 * @package Kaltura
 * @subpackage Output
 */
class StandardOutput extends Output
{

	/**
 	 * Write message into stdOut and go to new line
 	 *
 	 * @param Mixed, message you want to output
 	 * 
 	 * @return @void
 	 */
	public static function writeln($msg) {
		self::write($msg);
		self::write(PHP_EOL);
	}

	/**
 	 * Write message into stdOut
 	 *
 	 * @param Mixed, message you want to output
 	 * 
 	 * @return @void
 	 */
	public static function write($msg) {
		echo self::toString($msg);
	}
}
