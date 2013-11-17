<?php
// ===================================================================================================
/**
 * @namespace
 */
namespace Kaltura\Notification;

/**
 * Thrown notification errors
 * 
 * @package Kaltura
 * @subpackage Notification
 */
class Exception extends \RuntimeException 
{
	const ERROR_GENERIC = -1;
	const ERROR_INVALID_SIGNATURE = -2;
	const ERROR_REQUIRED_ADMIN_SECRET = -3;
	const ERROR_PROCESSING = -4;
	const ERROR_INVALID_TYPE = -5;

	/**
 	 * Instanciate a NotificationException object
 	 *
 	 * @return mixed: result of processing
 	 */
	public function __construct($message, $code) {
		$this->code = $code; // force property set here because php expect code to be integer and it cannot pass in the constructor
		parent::__construct($message);
	}
}

