<?php

/**
 * @namespace
 */
namespace Kaltura\Notification;

/**
 * Kaltura Notification Processor class
 *
 * @package Kaltura
 * @subpackage Notification
 */
class Processor
{

	/**
	 * @var int
	 */
	public $handlers = array();
	
	/**
	 * @var \Kaltura\Notification\Client
	 */
	private $notificationClient = null;

	/**
	 * @var \Kaltura\Client\Client
	 */
	private $client = null;

	/**
 	 * Constructor, instanciate a \Kaltura\Notification\Processor object
 	 *
 	 * @param Array, $params: array of parameters. Could be POST, GET or any array from user input
 	 * @param Boolean, $validateClientSignature: whether or not validate the notification signature
 	 * @param String, $adminSecret: Kaltura admin secret (required if validating the notification signature)
	 *
	 * @see \Kaltura\Notification\Client
	 * 
	 * @return @void
 	 */
	public function __construct($params, $validateClientSignature = true, $adminSecret = null) {
		$this->notificationClient = new Client($params, $validateClientSignature, $adminSecret);
	}

	/**
 	 * Add a notification handler to the notification processor
 	 *
 	 * @param \Kaltura\Notification\Handler, $notificationHandler: notification handler to add
 	 * 
 	 * @return \Kaltura\Notification\Handler: handler added
 	 */
	public function addHandler(\Kaltura\Notification\Handler $notificationHandler) {
		$notificationTypes = $notificationHandler->getTypes();

		if ($notificationTypes) {
			foreach ($notificationTypes as $notificationType) {
				$this->handlers[$notificationType][] = $notificationHandler;
			}
		} else {
			$this->handlers[\Kaltura\Notification\Handler::HANDLER_TYPE_ALWAYS][] = $notificationHandler;
		}

		return $notificationHandler;
	}

	/**
 	 * Add a notification handler to the notification processor
 	 *
 	 * @param Array, $notificationHandlers: array of \Kaltura\Notification\Handler objects
 	 * 
 	 * @return \Kaltura\Notification\Handler: handler added
 	 */
	public function addHandlers($notificationHandlers) {
		if (count($notificationHandlers)) {
			foreach ($notificationHandlers as $notificationHandler) {
				$this->addHandler($notificationHandler);
			}
		}

		return $notificationHandlers;
	}

	/**
 	 * Process the notification handlers in orders:
 	 *		- pre-process handlers
 	 *		- handler corresponding to the notification type currently triggered
 	 * 	- handler that should be always executed regardless of the notification type
 	 * 	- post-process handlers
 	 * 
 	 * Note that if there are more than 1 handler per group type, they are executed in the
 	 * order they have been set up
 	 * @see executeHandlersByType
 	 *
 	 * @return Array: result
 	 */
	public function execute() {
		$sequence = array(
			\Kaltura\Notification\Handler::HANDLER_TYPE_PRE_PROCESS,
			$this->notificationClient->type,
			\Kaltura\Notification\Handler::HANDLER_TYPE_ALWAYS_PROCESS,
			\Kaltura\Notification\Handler::HANDLER_TYPE_POST_PROCESS,
		);

		$result = array();

		foreach ($sequence as $handlerType) {
			$result[$handlerType] = $this->executeHandlersByType($handlerType);
		}

		return $result;
	}

	/**
 	 * Process a given notification handler group in the order they have been set up
 	 *
 	 * @return Strin|Array, $notificationType: notification type(s) that you want to process
 	 *
 	 * @return Array: result of handler executions
 	 */
	public function executeHandlersByType($type) {
		$result = array();

		if (isset($this->handlers[$type])) {
			foreach ($this->notificationClient->data as $notificationData) {
				foreach ($this->handlers[$type] as $handler) {
					$result[] = $handler->execute($notificationData);
				}
			}
		}

		return $result;
	}
}
