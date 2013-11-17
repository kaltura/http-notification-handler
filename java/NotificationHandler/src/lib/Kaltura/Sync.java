package lib.Kaltura;

import java.io.PrintWriter;
import java.io.StringWriter;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import lib.Kaltura.Notification.Notification;
import lib.Kaltura.Notification.Processor;
import lib.Kaltura.Notification.Handler.NotificationSampleHandler;
import lib.Kaltura.Notification.Types.HandleEventType;
import lib.Kaltura.Notification.Types.NotificationType;
import lib.Kaltura.Output.Console;
import lib.Kaltura.Output.OutputInterface;
import lib.Kaltura.Output.StandaradOutput;
import lib.Kaltura.config.SessionConfig;

public class Sync {
	
	public static void sync(Map<String, String> params) throws Exception {
		
		// Create console in order to log activity
		List<OutputInterface> output = new ArrayList<OutputInterface>();
		output.add(new StandaradOutput());
		Console console = new Console(output);
		console.start();
		
		try {
			if (params.isEmpty()) {
				console.write("No notification params");
			} else {
				
				// Accept a single notification
				Notification notification = new Notification(params, true, SessionConfig.KALTURA_ADMIN_SECRET);
				
				// Create handler
				Set<HandleEventType> types = new HashSet<HandleEventType>();
				types.add(NotificationType.ENTRY_UPDATE);
				NotificationSampleHandler sampleHandler = new NotificationSampleHandler(types);
				sampleHandler.setConsole(console);
				
				// Create processor and assign the handler to it
				Processor notificationProcessor = new Processor(console);
				notificationProcessor.addHandler(sampleHandler);
		
				// Handle single notification
				notificationProcessor.execute(notification);
			}
			
			console.write("");
			console.write("=============");
			console.write("  ALL DONE!");
			console.write("=============");
			console.end();
			
		} catch (Exception e) {
			StringWriter sw = new StringWriter();
			e.printStackTrace(new PrintWriter(sw));
			String exceptionAsString = sw.toString();
			
			console.write("");
			console.write("  An error occurred!");
			console.write("  " + e.getCause() + ": " + e.getMessage());
			console.write("  " + exceptionAsString);
			console.write("");
			console.write("  ======================================");
			console.write("  END WITH ERRORS");
			console.write("  ======================================");
			console.end();
		}
	}
	
}