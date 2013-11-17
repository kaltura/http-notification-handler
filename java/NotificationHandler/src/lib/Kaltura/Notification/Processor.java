package lib.Kaltura.Notification;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;

import lib.Kaltura.Notification.Types.HandleEventType;
import lib.Kaltura.Notification.Types.HandlerProcessType;
import lib.Kaltura.Notification.Types.NotificationType;
import lib.Kaltura.Output.Console;

/**
 * This is the Notification processor.
 * To this class, handlers register to handle notification events. 
 * Once a notification event arrives - This class is responsible to trigger the matching handlers.
 */
public class Processor
{
	/** Mapping between triggering events to the handlers that should be fired.*/
	private Map<HandleEventType, List<NotificationHandlerAbs>> handlers = new HashMap<HandleEventType, List<NotificationHandlerAbs>>();
	/** Console for output */
	private Console console;
	
	public Processor(Console console) {
		this.console = console;
	}
	
	/**
 	 * Add a notification handler to the notification processor
 	 * @param notificationHandler The notification handler to add
 	 */
	public void addHandler(NotificationHandlerAbs notificationHandler) {
		Set<HandleEventType> notificationTypes = notificationHandler.getTypes();

		if (notificationTypes.isEmpty()) {
			addHandlerByType(notificationHandler, HandlerProcessType.ALWAYS_PROCESS);
		} else {
			for (HandleEventType handlerType : notificationTypes) {
				addHandlerByType(notificationHandler, handlerType);
			}
		}
	}
	
	/**
	 * Add a notification handler to a specific notification event
	 * @param notificationHandler The notification handler to add
	 * @param type The event type to register the handler to
	 */
	public void addHandlerByType(NotificationHandlerAbs notificationHandler, HandleEventType type) {
		this.console.write("Processor: Added handler (" + notificationHandler.getClass().getSimpleName() + ") to handle " + type);
		if(!this.handlers.containsKey(type)) 
			this.handlers.put(type, new ArrayList<NotificationHandlerAbs>());
		this.handlers.get(type).add(notificationHandler);
	}

	/**
 	 * Process the notification handlers in orders.
 	 * 
 	 * Note that if there are more than 1 handler per group type, they are executed in the
 	 * order they have been set up
 	 *
 	 * @return Array: result
	 * @throws java.lang.Exception 
 	 */
	public void execute(Notification notification) throws java.lang.Exception {
		
		Map<Integer, Map<String, String>> multiNotification = notification.getData();
		
		executeByType(multiNotification, HandlerProcessType.PRE_PROCESS);
		executeByType(multiNotification, null);
		executeByType(multiNotification, HandlerProcessType.ALWAYS_PROCESS);
		executeByType(multiNotification, HandlerProcessType.POST_PROCESS);
	}

	/**
	 * Handles a specific handlers type. 
	 * If not given, chooses by the request type
	 * @param multiNotification
	 * @param type
	 * @throws Exception
	 */
	protected void executeByType(Map<Integer, Map<String, String>> multiNotification, HandleEventType type) throws Exception {
		for (Map<String, String> request : multiNotification.values()) {
			if(type == null)
				type = NotificationType.getType(request.get("notification_type"));
			if(!handlers.containsKey(type))
				continue;
			
			for (NotificationHandlerAbs handler : handlers.get(type)) {
				handler.execute(request);
			}
		}
	}
}
