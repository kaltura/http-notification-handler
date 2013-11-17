<?php

/**
 * @namespace
 */
namespace Kaltura\Output;

/**
 * OutpuInterface interface
 *
 * @package Kaltura
 * @subpackage Output
 */
interface OutputInterface
{
	/**
 	 * Write message and go to new line
 	 *
 	 * @param String, message you want to write
 	 */
	public static function writeln($msg);

	/**
 	 * Write message
 	 *
 	 * @param String, message you want to write
 	 */
	public  static function write($msg);
}
