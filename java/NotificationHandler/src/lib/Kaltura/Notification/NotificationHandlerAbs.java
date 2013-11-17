package lib.Kaltura.Notification;

import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import lib.Kaltura.Notification.Types.HandleEventType;
import lib.Kaltura.Output.Console;

/**
 * This class defines the basics for notification handling
 */
public abstract class NotificationHandlerAbs {
	
	/** All the events under which the notification handler should be fired */
	protected Set<HandleEventType> types = new HashSet<HandleEventType>();
	/** The console handling the output */
	protected Console console = null;
	
	/**
	 * Constructor
	 * @param types - list of events that should trigger the notification handler
	 */
	public NotificationHandlerAbs(Set<HandleEventType> types) {
		setTypes(types);
	}
	
	/**
	 * This function is responsible for handling a received notification
	 * @param data The request parameters as map
	 * @throws Exception If something wrong happened while handling the request
	 */
	public abstract void execute(Map<String, String> data) throws Exception;

	/* Getters / Setters*/
	
	public final void setTypes(Set<HandleEventType> types) {
		
		this.types.addAll(types);
	}

	public Set<HandleEventType> getTypes() {
		return this.types;
	}

	public final void setConsole(Console console) {
		this.console = console;
	}
	
}
