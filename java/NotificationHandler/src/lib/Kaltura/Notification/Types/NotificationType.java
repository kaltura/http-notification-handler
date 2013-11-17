package lib.Kaltura.Notification.Types;

import lib.Kaltura.Notification.NotificationHandlerException;

/**
 * This enum represents all types which are notification based events. 
 */
public enum NotificationType implements HandleEventType
{
	// Enum values
	ENTRY_ADD("entry_add"),
	ENTRY_UPDATE("entry_update"),
	ENTRY_UPDATE_PERMISSIONS("entry_update_permissions") ,
	ENTRY_DELETE("entry_delete"),
	ENTRY_BLOCK("entry_block"),
	ENTRY_UPDATE_THUMBNAIL("entry_update_thumbnail"),
	USER_BANNED("user_banned");
	
	/**
	 * Constructor
	 * @param name - The event name as received from the server
	 */
	private NotificationType(String name) {
		this.name = name;
	}
	
	// The event name as received from the server
	private final String name;
	
	/**
	 * Gets the notification enum value by the server name.
	 * Throws an exception if the type is unknown.
	 * @param typeStr -  The event name as received from the server
	 * @return The matching notification type
	 */
	public static NotificationType getType(String typeStr) {
		for (NotificationType type : values()) {
			if(type.name.equals(typeStr))
				return type;
		}
		
		throw new NotificationHandlerException("Type " + typeStr + " is unkown", NotificationHandlerException.ERROR_INVALID_TYPE);
	}
}
