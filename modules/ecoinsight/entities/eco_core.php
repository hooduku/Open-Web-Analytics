<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

require_once( OWA_BASE_CLASS_DIR . 'factTable.php');

/**
 * Fact-table for the ecoInsight core metrics capture.
 **/
class owa_eco_core extends owa_factTable
{

	/**
	 * Constructs an instance of the entity.
	 **/
	function __construct()
	{
		$this->setTableName('eco_core');

		// Retrieve the common columns
		$parent_columns = parent::__construct();

		// Configure the column de-normalized columns
		foreach ($parent_columns as $pcolumn)
		{
				$this->setProperty($pcolumn);
		}

		// Configure email
		$email = new owa_dbColumn('user_email', OWA_DTD_VARCHAR255);
		$this->setProperty($email);
		
		// Configure the tracked columns.
		$applicationName = new owa_dbColumn('app_name', OWA_DTD_VARCHAR255);
		$this->setProperty($applicationName);

		$appVersion = new owa_dbColumn('app_version', OWA_DTD_VARCHAR255);
		$this->setProperty($appVersion);

		$contentId = new owa_dbColumn('content_identifier', OWA_DTD_VARCHAR255);
		$this->setProperty($contentId);

		$navigationState = new owa_dbColumn('navstate', OWA_DTD_VARCHAR255);
		$this->setProperty($navigationState);

		$objectName = new owa_dbColumn('object_name', OWA_DTD_VARCHAR255);
		$this->setProperty($objectName);

		$stateName = new owa_dbColumn('state_name', OWA_DTD_VARCHAR255);
		$this->setProperty($stateName);

		$offline = new owa_dbColumn('offline', OWA_DTD_BOOLEAN);
		$this->setProperty($offline);

		$fullscreen = new owa_dbColumn('fullscreen', OWA_DTD_BOOLEAN);
		$this->setProperty($fullscreen);

		$installState = new owa_dbColumn('installstate', OWA_DTD_TINYINT);
		$this->setProperty($installState);

		$timecode = new owa_dbColumn('timecode', OWA_DTD_INT);
		$this->setProperty($timecode);

		$zoomFactor = new owa_dbColumn('zoomfactor', OWA_DTD_VARCHAR10);
		$this->setProperty($zoomFactor);

		$clientHeight = new owa_dbColumn('client_height', OWA_DTD_INT);
		$this->setProperty($clientHeight);

		$clientWidth = new owa_dbColumn('client_width', OWA_DTD_INT);
		$this->setProperty($clientWidth);

		$runtimeVersion = new owa_dbColumn('runtime_version', OWA_DTD_VARCHAR255);
		$this->setProperty($runtimeVersion);

		$campaignId = new owa_dbColumn('jadcampaign_id', OWA_DTD_BIGINT);
		$this->setProperty($campaignId);

		$zoneId = new owa_dbColumn('jadzone_id', OWA_DTD_BIGINT);
		$this->setProperty($zoneId);
		
		$adId = new owa_dbColumn('jad_id', OWA_DTD_BIGINT);
		$this->setProperty($adId);

		$contentSection = new owa_dbColumn('content_section', OWA_DTD_VARCHAR255);
		$this->setProperty($contentSection);
		
		$platform = new owa_dbColumn('platform', OWA_DTD_VARCHAR255);
		$this->setProperty($platform);
		
		$originalTimestamp = new owa_dbColumn('original_timestamp', OWA_DTD_INT);
		$this->setProperty($originalTimestamp);
		
		$originalTs = new owa_dbColumn('original_datetime', OWA_DTD_VARCHAR255);
		$this->setProperty($originalTs);
		
		$region = new owa_dbColumn('region', OWA_DTD_VARCHAR10);
		$this->setProperty($region);
		
		$postalCode = new owa_dbColumn('postal_code', OWA_DTD_VARCHAR10);
		$this->setProperty($postalCode);

		$projectCost = new owa_dbColumn('cost', OWA_DTD_VARCHAR255);
		$this->setProperty($projectCost);

		$costType= new owa_dbColumn('cost_type', OWA_DTD_VARCHAR255);
		$this->setProperty($costType);
		
		$costSubtype= new owa_dbColumn('cost_subtype', OWA_DTD_VARCHAR255);
		$this->setProperty($costSubtype);
		
		$persistanceAction = new owa_dbColumn('storage', OWA_DTD_VARCHAR10);
		$this->setProperty($persistanceAction);

		owa_coreAPI::debug('Initialized entity: eco_core');
	}
}

?>