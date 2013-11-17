
1. Update session config:
	at NotificationHandler/src/lib/Kaltura/config/SessionConfig.java
	Please update the partner_id, admin_secret & service_url

2. Add the required jars
	under 'NotificationHandler\WebContent\WEB-INF\lib' add all the required jars.
	- KalturaClientLibrary
	- commons-httpclient-3.1
	- commons-codec-1.4
	- commons-logging-1.1.1
	- log4j-1.2.15
	
3 & optional. Update the handler to handle the notification.
	update 'execute' function at NotificationHandler/src/lib/Kaltura/Notification/Handler/NotificationSampleHandler.java
	
4. Deploy on tomcat:
	- Extract a WAR file and put it under <TOMCAT>/webapps

5. On KMC, set the notification URL to be http://<your tomcat server path>/NotificationHandler/sync.jsp
	