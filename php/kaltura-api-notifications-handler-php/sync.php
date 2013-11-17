<?php 

require_once('./session_config.php');
require_once('./script_config.php');
require_once('./lib/autoload.php');

// Create console in order to log activity
$console = new \Kaltura\Output\Console();
$console->startLog();

$params = $_POST;

try {
	if (!count($params)) {
		$console->log('No notification params');
		$console->log('');
		$console->log('=============');
		$console->log('  ALL DONE!');
		$console->endLog('=============');
	} else {
		// Instanciate a nofication processor object
		// First param is the notification parameters
		// Second param is validateSignature of the notification (make sure it comes from Kaltura)
		// Third param is the Kaltura admin secret

		// This will set up the notification client and check the notification signature
		$notificationProcessor = new \Kaltura\Notification\Processor($params, false, KALTURA_ADMIN_SECRET);

		// Setting up handler for 'entry_update' notification type
		$synchEntryHandler = new \Kaltura\Notification\Handler\SyncEntry(\Kaltura\Notification\Type::NOTIFICATION_TYPE_ENTRY_UPDATE);
		// Passing console to the handler so it can log as well
		$synchEntryHandler->setConsole($console);
		// Passing extra parameters from the config to the handler
		$synchEntryHandler->addData($handlerParams);

		// Adding handler to the notification processor
		// Note that you can set up multiple handlers, the processor will execute them in order
		$notificationProcessor->addHandler($synchEntryHandler);

		// Processing notification handler(s)
		$notificationProcessor->execute();

		$console->log('');
		$console->log('=============');
		$console->log('  ALL DONE!');
		$console->endLog('=============');
	}
} catch (Exception $e) {
	$console->log('');
	$console->timeLog("\n");
	$console->log('  /!\\ An error occurred!');
	$console->log('  '.$e->getCode().': '.$e->getMessage());
	$console->log('  '.$e->getTraceAsString());
	$console->log('');
	$console->log('  ======================================');
	$console->log('  END WITH ERRORS');
	$console->endLog('  ======================================');
}

?>