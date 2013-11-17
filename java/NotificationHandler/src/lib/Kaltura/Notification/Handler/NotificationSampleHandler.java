package lib.Kaltura.Notification.Handler;

import java.util.Map;
import java.util.Set;

import lib.Kaltura.Notification.NotificationHandlerAbs;
import lib.Kaltura.Notification.NotificationHandlerException;
import lib.Kaltura.Notification.Types.HandleEventType;
import lib.Kaltura.Notification.Types.NotificationType;
import lib.Kaltura.config.SessionConfig;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.enums.KalturaSessionType;
import com.kaltura.client.types.KalturaMediaEntry;

/**
 * Notification sample handler.
 * This sample demonstrates how to handle a notification update 
 */
public class NotificationSampleHandler extends NotificationHandlerAbs
{
	/** Kaltura client */
	private static KalturaClient apiClient = null;
	
	/**
	 * Constructor
	 * @param types - The types that this handler handles
	 */
	public NotificationSampleHandler(Set<HandleEventType> types) {
		super(types);
	}

	/**
	 * @return The Kaltura client
	 * @throws Exception
	 */
	private static KalturaClient getClient() {
		if(apiClient == null) {
			// Generates the Kaltura client. The parameters can be changed according to the need
			try {
				apiClient = SessionConfig.getClient(KalturaSessionType.ADMIN, "", 86400, "");
			} catch (Exception e) {
				throw new NotificationHandlerException("Failed to generate client : " + e.getMessage(), NotificationHandlerException.ERROR_PROCESSING) ;
			}
		}
		return apiClient;
	}
	
	/**
 	 * Fetch an entry using the API
 	 *
 	 * @param String, entryId: id of the entry you want to fetch
	 * @throws KalturaApiException 
	 * @throws Exception 
 	 *
 	 */
	public KalturaMediaEntry fetchEntry(String entryId) throws KalturaApiException {
		return getClient().getMediaService().get(entryId);
	}

	/**
	 * This function handles a single notification.
	 * @param notificationData - The notification parameters are given in this map
	 * @throws KalturaApiException 
	 */
	public void execute(Map<String, String> notificationData) throws Exception {
		int notificationId = Integer.parseInt(notificationData.get("notification_id"));
		String type = notificationData.get("notification_type");
		NotificationType notificationType = NotificationType.getType(type);
		
		this.console.write("SampleHandler: Handling notification " + notificationId + " of type " + notificationType);
		String entryId = notificationData.get("entry_id");
		KalturaMediaEntry entry = fetchEntry(entryId);	
		this.console.write("\tentry id " + entry.id + " entry status " + entry.status);

		// TODO This function is a stub implementation for an entry. 
		// Add your desired functionality here.
	}

}