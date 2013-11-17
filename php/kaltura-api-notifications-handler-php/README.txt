Usage
======
This package is an example of using Kaltura notifications
You may implement your own handler classes following that example


Unpack the zip file on a "public" web server accessible from the internet.

Set up an instance Kaltura PHP 5.3 client library - Place contents of the 'Client' sub-folder found in the PHP 5.3 client library, under the <your handler web folder>\lib\Kaltura\Client

Make sure the configuration settings are properly populated:
	- in session_config.php:
	  Set up your partner id, admin secret and Kaltura service url 

	- in script_config.php:
	  Set up additional configuration parameters. See comments in file.
	  You can add more parameters if you want to use them in your notification handlers


Setting up notifications on Kaltura:
	- Access the KMC and set up notifications under Settings/Integration Settings/Notifications
	  Refer to the knowledge center for more details
		http://knowledge.kaltura.com/faq/what-types-notifications-are-there-kmc
	- Select the type of notification you want to be triggered and add the path the public server that host the nofication script

To test your notification handler, open an entry in the KMC and update it (if you set up notification as "Update Entry" for instance). Kaltura should trigger a notification and execute your script on the public server where it has been installed

To check if your notification handler went through, check the log in the "log" directory.



--------------------------
See sample log file below:
--------------------------

Entry is ready

Checking sync field status...
Entry needs to be synched

Listing entry flavors...
=============================================
  Available flavors for entry 1_82h0a7l2 (8)
=============================================
  Mobile (3GP)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_pqicg5lq/name/a.3gp
  Basic/Small - WEB/MBL (H264/400)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_iscqagra/name/a.mp4
  Basic/Small - WEB/MBL (H264/600)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_ijvg9b1s/name/a.mp4
  SD/Small - WEB/MBL (H264/900)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_vfj73ldm/name/a.mp4
  SD/Large - WEB/MBL (H264/1500)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_nrozjdyb/name/a.mp4
  HD/720 - WEB (H264/2500)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_bp9rko9r/name/a.mp4
  HD/1080 - WEB (H264/4000)
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_n2dt6ppn/name/a.mp4
  Source
     http://cdnbakmi.kaltura.com/p/1019881/sp/101988100/serveFlavor/entryId/1_82h0a7l2/flavo                                  rId/1_ijkbcqdq/name/a.mov
=============================================

Showing base metadata...
=============================================
  Base metadata for entry 1_82h0a7l2
=============================================
  Name: Some video
  Description: Some description
  Tags: hahaha, ohoho, heyheyhey
=============================================

Showing custom metadata (93091)...
=============================================
  Custom metadata (93091) for entry 1_82h0a7l2 (2)
=============================================
  LinkURL: http://corp.kaltura.com
  LinkText: Kaltura rocks!
=============================================

Synchronizing entry1_82h0a7l2...
Entry 1_82h0a7l2 synchronized.

=============
  ALL DONE!
=============

[2012/11/17 13:42:41] ==> END


--------------------------
End sample log file
--------------------------