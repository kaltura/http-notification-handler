<?php

/**
 * @namespace
 */
namespace Kaltura\Notification;

/**
 * Kaltura Notification Type class
 *
 * @package Kaltura
 * @subpackage Notification
 */
class Type
{
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_ENTRY_ADD = 'entry_add';
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_ENTRY_UPDATE = 'entry_update';
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_ENTRY_UPDATE_PERMISSIONS = 'entry_update_permissions';
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_ENTRY_DELETE = 'entry_delete';
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_ENTRY_BLOCK = 'entry_block';
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL = 'entry_update_thumbnail';
	/**
	 * @var String
	 */
	const NOTIFICATION_TYPE_USER_BANNED = 'user_banned';

	/**
 	 * Get notification types
 	 *
 	 * @return Array: notification types
 	 */
	public static function getTypes() {
		$reflectTypeClass = new \ReflectionClass(__CLASS__);
		return $reflectTypeClass->getConstants();
	}
}
