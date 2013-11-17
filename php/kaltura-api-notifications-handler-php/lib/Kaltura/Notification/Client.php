<?php

/**
 * @namespace
 */
namespace Kaltura\Notification;

/**
 * Kaltura Notification Client class
 *
 * @package Kaltura
 * @subpackage Notification
 */
class Client
{

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var int
	 */
	private $type;

	/**
	 * @var KalturaClient
	 */
	private $puserId;

	/**
	 * @var int
	 */
	private $partnerId;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var boolean
	 */
	private $multi = false;

	/**
	 * @var boolean
	 */
	private $validateSignature = null;
	
	/**
	 * Constructor, instanciates a new Kaltura\Notification\Client object
	 *
	 * @param Array, $params: array of parameters
	 * @param Boolean, $validateSignature: whether or not to validate the signature
	 * @param String, $adminSecret: Kaltura admin secret (required if validating signature)
	 *
	 * @return @void
	 */
	public function __construct($params, $validateSignature = true, $adminSecret = null) {
		if (!count($params)) {
			return $this;
		}
		
		if ($validateSignature) {
			if (is_null($adminSecret)) {
				
				throw new \Kaltura\Notification\Exception('Missing required admin secret.', \Kaltura\Notification\Exception::ERROR_REQUIRED_ADMIN_SECRET);
			}

			$this->validateSignature($params, $adminSecret);
			
			if (!$this->validateSignature) {
				throw new \Kaltura\Notification\Exception('Notification signature is not valid.', \Kaltura\Notification\Exception::ERROR_INVALID_SIGNATURE);
			}
		}
		
		$this->id = $params['notification_id'];
		$this->type = $params['notification_type'];
		$this->puserId = $params['puser_id'];
		$this->partnerId = $params['partner_id'];

		$data = array();
		foreach ($params as $name => $value) {
			switch ($name) {
				/*case 'partner_id':
					break;*/
				default:
					$data[$name] = $value;
					break;
			}
		}

		if (isset($data['multi_notification'] ) &&   $data['multi_notification'] === 'true') {
			$this->multi = true;
			$res = $this->splitMultiNotifications($data);
		} else {
			$res[0] = $data;
		}

		$this->data = $res;
	}

	/**
	 * Split multi notification data
	 *
	 * @return Array, $data: array of notification data sent by the notification
	 *
	 * @return Array
	 */
	private function splitMultiNotifications($data){
		$notData = array();

		foreach ($data as $name => $value) {
			$match = preg_match('/^(not[^_]*)_(.*)$/' ,$name, $parts);
			
			if (!$match) {
				continue;
			}

			$notNameParts = $parts[1];
			$notProperty = @$parts[2];
			$num = (int) str_replace('not', '', $notNameParts);
			$notData[$num][$notProperty] = $value;
		}

		return $notData;
	}
	
	/**
 	 * Validate signature
 	 *
 	 * @param String, $adminSecret: admin secret
 	 *
 	 * @return @void
 	 */
	private function validateSignature($notificationParams, $adminSecret){
		ksort($notificationParams);
		$str = '';
		$validParams = array();

		if (array_key_exists('signed_fields', $notificationParams)) {
			$validParams = explode(',', $notificationParams['signed_fields']);      
		}

		foreach ($notificationParams as $paramName => $paramValue) {
			if ($paramName == 'sig') {
				continue;
			}

			if (!in_array($paramName, $validParams) && count($validParams) > 1 && !$notificationParams['multi_notification']) {
				if (($paramName != 'multi_notification') && ($paramName != 'number_of_notifications')) {
					continue;
				}
			}
			$str .= $paramName.$paramValue;
		}

		if (isset($notificationParams['sig']) && md5($adminSecret.$str) == $notificationParams['sig']) {
			$this->validateSignature = true;
		} else {
			$this->validateSignature = false;
		}
	}

	/**
 	 * Generic getter
 	 *
 	 * @param String, $name: name of the property you want to get
 	 *
 	 * @return Mixed: proterty of the notification client object
 	 */
	public function __get($name) {
		switch ($name) {
			case 'id':
			case 'type':
			case 'puserId':
			case 'partnerId':
			case 'multi':
			case 'validateSignature':
			case 'data':
				return $this->$name;
			default:
				throw new \Kaltura\Notification\NotificationException('Property \''.$name.'\' does not exist.', \Kaltura\Notification\NotificationException::ERROR_GENERIC);
		}
	}

}

