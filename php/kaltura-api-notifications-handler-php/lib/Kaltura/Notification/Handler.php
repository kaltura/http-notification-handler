<?php

/**
 * @namespace
 */
namespace Kaltura\Notification;

/**
 * Kaltura Notification Handler class
 *
 * @package Kaltura
 * @subpackage Notification
 */
abstract class Handler
{
	/**
	 * @var Type of handler that is always executed whatever the notification type is
	 */
	const HANDLER_TYPE_ALWAYS_PROCESS = 'always';
	
	/**
	 * @var Type of handler that is executed prior to other handlers
	 */
	const HANDLER_TYPE_PRE_PROCESS = 'pre';
	
	/**
	 * @var Type of handler that is executed after other handlers
	 */
	const HANDLER_TYPE_POST_PROCESS = 'post';

	/**
	 * @var Array
	 */
	protected $data = array();

	/**
	 * @var String
	 */
	protected $types = null;
	
	/**
	 * @var \Kaltura\Output\Console
	 */
	protected static $console = null;
	
	/**
 	 * Instanciate a new notification handler
 	 *
 	 * @return String: type of the notification handler
 	 */
	public function __construct($types = self::HANDLER_TYPE_ALWAYS_PROCESS) {
		$this->setTypes($types);
	}

	/**
 	 * Set handler type(s)
 	 *
 	 * @param array|string, handler type(s)
 	 * 
 	 * @return \Kaltura\Notification\Handler: notification handler
 	 */
	public final function setTypes($types) {
		
		if (is_string($types)) {
			$types = array($types);
		}

		if (!is_array($types)) {
			throw new \Kaltura\Notification\Exception('Invalid type passed in: '.gettype($types).'. Type must be a string or an array', \Kaltura\Notification\Exception::ERROR_INVALID_TYPE);
		}

		$validTypes = self::getAllTypes();

		foreach($types as $type) {
			if (in_array($type, $validTypes)) {
				$this->types[] = $type;
			} else {
				throw new \Kaltura\Notification\Exception('Invalid type \''.$type.'\' passed in. Type must be at least one of the following: \''.implode('\', \'', $validTypes).'\'.', \Kaltura\Notification\Exception::ERROR_INVALID_TYPE);
			}
		}

		return $this;
	}

	/**
 	 * Return handler types if any
 	 *
 	 * @return Array: types of the notification handler
 	 */
	public function getTypes() {
		return $this->types;
	}

	/**
 	 * Return all valid handler types
 	 *
 	 * @return Array: valid types of the notification handler
 	 */
	public static function getAllTypes() {
		return array_merge(self::getProcessTypes(), \Kaltura\Notification\Type::getTypes());
	}

	/**
 	 * Get process handler types
 	 *
 	 * @return Array: process handler types
 	 */
	public static function getProcessTypes() {
		$reflectHandlerClass = new \ReflectionClass(__CLASS__);
		return $reflectHandlerClass->getConstants();
	}

	/**
 	 * Set handler data
 	 *
 	 * @return \Kaltura\Notification\Handler: notification handler
 	 */
	public final function setData($data) {
		$this->data = $data;

		return $this;
	}

	/**
 	 * Add data
 	 *
 	 * @return \Kaltura\Notification\Handler: notification handler
 	 */
	public final function addData($data) {
		$this->data = array_merge($this->data, $data);

		return $this;
	}

	/**
 	 * Set console
 	 *
 	 * @param \Kaltura\Output\Console
 	 *
 	 * @return \Kaltura\Notification\Handler: notification handler
 	 */
	public final function setConsole($console) {
		self::$console = $console;
	}

	/**
 	 * Execute the notification handler
 	 *
 	 * @param Array, $data: data from notification client
 	 *
 	 * @return mixed: result of execution
 	 */
	public abstract function execute($data);

}
