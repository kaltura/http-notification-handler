package lib.Kaltura.Notification.Types;

/**
 * This enum indicates all the time-based events 
 */
public enum HandlerProcessType implements HandleEventType {

	// Handler is always executed whatever the notification type is
	ALWAYS_PROCESS, 
	// handler is executed prior to other handlers
	PRE_PROCESS,
	// handler is executed after other handlers
	POST_PROCESS;
	
}
