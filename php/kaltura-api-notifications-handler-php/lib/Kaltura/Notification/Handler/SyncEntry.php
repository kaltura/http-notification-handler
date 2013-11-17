<?php

/**
 * @namespace
 */
namespace Kaltura\Notification\Handler;

/**
 * SyncEntry handler
 *
 * @package Kaltura
 * @subpackage Notification
 */
class SyncEntry extends \Kaltura\Notification\Handler
{
	/**
	 * @var \Kaltura\Client\Client
	 */
	private static $apiClient = null;
	
	/**
	 * @var Kaltura\Client\Type\MediaEntry
	 */
	public $entry;

	/**
 	 * Returns the API client
 	 *
 	 * @return \Kaltura\Client\CLient: client object
 	 */
	private static function getClient() {
		if (is_null(self::$apiClient)) {
			self::$apiClient = getClient(\Kaltura\Client\Enum\SessionType::ADMIN);
			self::$apiClient->getConfig()->setCurlTimeout(60); // set timeout to 60 seconds as we will be doing some multirequests
		}

		return self::$apiClient;
	}

	/**
 	 * Returns the metadata plugin
 	 *
 	 * @return \Kaltura\Client\Plugin\Metadata\MetadataPlugin: metadata plugin object
 	 */
	private static function getMetadataPlugin() {
		return \Kaltura\Client\Plugin\Metadata\MetadataPlugin::get(self::getClient());
	}

	/**
 	 * Fetch an entry using the API
 	 *
 	 * @param String, $entryId: id of the entry you want to fetch
 	 *
 	 * @return Kaltura\Notification\Handler\SyncEntry: the current handler
 	 */
	public function fetchEntry($entryId) {
		self::$console->log('');
		self::$console->log('Fetching entry '.$entryId.'...');
		$this->entry = self::getClient()->media->get($entryId);

		return $this;
	}

	/**
 	 * Execute handler
 	 *
 	 * @param Array, $notificationData: data from notification client
 	 *
 	 * @return Boolean: whether or not the entry has been synched
 	 */
	public function execute($notificationData) {
		$this->fetchEntry($notificationData['entry_id']);

		if ($this->entry) { 
			if ($this->entry->status != \Kaltura\Client\Enum\EntryStatus::READY) {
				self::$console->log('Entry is not ready');
				return false;
			} else {
				self::$console->log('Entry is ready');
				
				if ($this->entryNeedsSync()) {
					self::$console->log('Entry needs to be synched');
					return $this->syncEntry();
				} else {
					self::$console->log('Entry is already synched or does not need to be.');
					return false;
				}
			}
		} else {
			self::$console->log('No entry found');
			return false;
		}
	}

	/**
 	 * Check whether or not an entry needs to be synched
 	 *
 	 * @param Kaltura\Client\Type\MediaEntry, $entry: entry we want to check
 	 *
 	 * @return Boolean: whether or not the entry has to be synched
 	 */
	private function entryNeedsSync() {
		self::$console->log('');
		self::$console->log('Checking sync field status...');

		$this->addData(array(
			'metadataId' => null,
			'metadataXml' => null,
			'metadataSyncField' => ''
		));

		$customMetadata = self::loadCustomMetadata($this->entry->id, $this->data['syncMetadataProfileId']);

		if ($customMetadata) {
			// There is some custom metadata
			$this->addData(array(
					'metadataId' => $customMetadata->id,
					'metadataXml' => $customMetadata->xml
			));
			
			// Loading XML
			$sxe = new \SimpleXMLElement($this->data['metadataXml']);

			$children = $sxe->children();
			
			// We check that the sync field exists
			if (isset($children->{$this->data['syncFieldName']})) {
				// Sync field exists
				$syncStatus = (string)$children->{$this->data['syncFieldName']}[0];

				// We keep track of the current sync status for further use in the handler (functions below)
				$this->addData(array('metadataSyncField' => $syncStatus));
				
				if ($syncStatus == $this->data['syncNeededValue'] || $syncStatus == '') {
					return true;
				} else {
					return false;
				}
			} else {
				// Sync field does not exist
				return true;
			}
		} else {
			// No custom metadata found
			self::$console->log('No custom metadata found for entry '.$this->entry->id);
			return false;
		}
	}

	/**
 	 * Helper method to load custom metadata for a given entryId and metadataprofileId
 	 *
 	 * @param String, $entryId: entryId
 	 * @param int, $metadataProfileId: metadataProfileId
 	 *
 	 * @return \Kaltura\Client\Plugin\Metadata\Type\Metadata: metadata object
 	 */
	public static function loadCustomMetadata($entryId, $metadataProfileId) {
		// Getting custom metadata
		$filter = new \Kaltura\Client\Plugin\Metadata\Type\MetadataFilter();
		$filter->metadataObjectTypeEqual = \Kaltura\Client\Plugin\Metadata\Enum\MetadataObjectType::ENTRY;
		$filter->objectIdEqual = $entryId;
		$filter->metadataProfileIdEqual = $metadataProfileId;
		
		$results = self::getMetadataPlugin()->metadata->listAction($filter);

		if (isset($results->objects) && isset($results->objects[0])) {
			return $results->objects[0];
		} else {
			return null;
		}
	}

	/**
 	 * Synchronize an entry
 	 *
 	 * @return Boolean: whether or not the entry has to be synched
 	 */
	private function syncEntry() {
		
		// List available flavors for the given entry
		self::listFlavors($this->entry->id);

		// Load base metadata (name, description, tgas, etc.)
		self::loadBaseMetadata($this->entry);

		// Load custom metadata
		self::loadCustomMetadataFields($this->entry->id, $this->data['contentMetadataProfileId']);

		// Sync entry to your CMS here...

		return $this->setSyncFlag();
	}

	/**
 	 * List entry flavors
 	 *
 	 * @return @void
 	 */
	private static function listFlavors($entryId) {
		self::$console->log('');
		self::$console->log('Listing entry flavors...');

		$results = self::getClient()->media->getmrss($entryId);

		if ($results) {
			if (isset($results->error)) {
				throw \Kaltura\Notification\Exception('An error occured while loading flavors. '.$results->error->message, \Kaltura\Notification\Exception::ERROR_GENERIC);
			} else {

				$sxe = new \SimpleXMLElement($results);

				$children = $sxe->children();
				
				$nbFlavors = count($children->content);
				
				self::$console->log('=============================================');
				self::$console->log('  Available flavors for entry '.$entryId.' ('.$nbFlavors.')');
				self::$console->log('=============================================');

				if ($nbFlavors) {
					foreach ($children->content as $node) {
						self::$console->log('  '.(String) $node['flavorParamsName']);
						self::$console->log('     '.$node['url']);
					}

					self::$console->log('=============================================');
				}
			}
		}
	}

	/**
 	 * Helper method that loads base metadata for a given entry and write it into the log
 	 *
 	 * @param \Kaltura\Client\Type\MediaEntry, $entry: entry
 	 *
 	 * @return @void
 	 */
	private static function loadBaseMetadata($entry) {
		self::$console->log('');
		self::$console->log('Showing base metadata...');

		$fields = array('name', 'description', 'tags');
		
		self::$console->log('=============================================');
		self::$console->log('  Base metadata for entry '.$entry->id);
		self::$console->log('=============================================');

		foreach ($fields as $fieldName) {
			self::$console->log('  '.ucfirst($fieldName).': '.$entry->$fieldName);
		}
		self::$console->log('=============================================');
	}

	/**
 	 * Helper method that loads custom metadata for a given entryId and metadataprofileId
 	 * and write it into the log
 	 *
 	 * @param String, $entryId: entryId
 	 * @param int, $metadataProfileId: metadataProfileId
 	 *
 	 * @return @void
 	 */
	private static function loadCustomMetadataFields($entryId, $metadataProfileId) {
		self::$console->log('');
		self::$console->log('Showing custom metadata ('.$metadataProfileId.')...');
		
		$customMetadata = self::loadCustomMetadata($entryId, $metadataProfileId);

		if ($customMetadata) {
			$xml = $customMetadata->xml;

			$sxe = new \SimpleXMLElement($xml);

			$children = $sxe->children();
			
			$nbFields = count($children);
			
			self::$console->log('=============================================');
			self::$console->log('  Custom metadata ('.$metadataProfileId.') for entry '.$entryId.' ('.$nbFields.')');
			self::$console->log('=============================================');

			if ($nbFields) {
				foreach ($children as $name => $node) {
					self::$console->log('  '.$name.': '.(String) $node);
				}

				self::$console->log('=============================================');
			}
		} else {
			self::$console->log('No custom metadata to show for metadataprofile '.$metadataProfileId);
		}
	}

	/**
 	 * Set sync flag in custom metadata
 	 *
 	 * @return @void
 	 */
	private function setSyncFlag() {
		self::$console->log('');
		self::$console->log('Synchronizing entry'.$this->entry->id.'...');

		if ($this->data['metadataId']) {
			$result = $this->updateCustomMetadata();
		} else {
			$result = $this->addCustomMetadata();
		}
		
		if ($result) {
			self::$console->log('Entry '.$this->entry->id.' synchronized.');
		}
	}

	/**
 	 * Update the custom metadata
 	 *
 	 * @return \Kaltura\Client\Plugin\Metadata\Type\Metadata: metadata object
 	 */
	private function updateCustomMetadata() {
		
		// Building xml
		$dom = new \DOMDocument();
		$syncNode = $dom->createElement($this->data['syncFieldName'], $this->data['syncDoneValue']);
		$dom->appendChild($syncNode);
		
		$xmlMetadata = trim($dom->saveHTML());
		
		if ($this->data['metadataSyncField']) {
			$newXml = str_replace('<'.$this->data['syncFieldName'].'>'.$this->data['metadataSyncField'].'</'.$this->data['syncFieldName'].'>', $xmlMetadata, $this->data['metadataXml']);
		} else {
			$newXml = str_replace(array('</metadata>', '<metadata/>'), array($xmlMetadata.'</metadata>', '<metadata>'.$xmlMetadata.'</metadata>'), $this->data['metadataXml']);
		}
		
		return self::getMetadataPlugin()->metadata->update($this->data['metadataId'], $newXml);
	}

	/**
 	 * Add custom metadata
 	 *
 	 * @return \Kaltura\Client\Plugin\Metadata\Type\Metadata: metadata object
 	 */
	private function addCustomMetadata() {
		// Building xml
		$dom = new \DOMDocument();
		$metadataNode = $dom->createElement('metadata');
		$syncNode = $dom->createElement($this->data['syncFieldName'], $this->data['syncDoneValue']);
		$metadataNode->appendChild($syncNode);
		$dom->appendChild($metadataNode);
		
		$xmlMetadata = trim($dom->saveHTML());

		return self::getMetadataPlugin()->metadata->add($this->data['syncMetadataProfileId'], \Kaltura\Client\Plugin\Metadata\Enum\MetadataObjectType::ENTRY, $this->entry->id, $xmlMetadata);
	}

}