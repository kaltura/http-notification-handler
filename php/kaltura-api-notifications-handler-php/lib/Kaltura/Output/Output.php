<?php

/**
 * @namespace
 */
namespace Kaltura\Output;

/**
 * Output abstract class
 *
 * @package Kaltura
 * @subpackage Output
 */
abstract class Output implements OutputInterface
{
	/**
 	 * Convert passed in argument into a string
 	 *
 	 * @param Mixed, message you want to convert into a string
 	 * 
 	 * @return String: converted message
 	 */
	public static function toString($msg) {
		if (!is_string($msg)) {
			return print_r($msg, true);
		}

		return $msg;
	}

	// Note that this class being abstract does not have to
	// implement the OutputInterface interface perse. However
	// the derived classes must do so
}
