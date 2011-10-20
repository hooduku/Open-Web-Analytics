<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

if (!class_exists('owa_db'))
{
	require_once(OWA_BASE_DIR.'/owa_db.php');
}

/**
 * Class for retrieving details from the advertisement server.
 **/
class owa_adserver
{
	/**
	 * Stores the settings
	 **/
	var $settings;
	
	/**
	 * Stores the database connection.
	 **/
	var $connection;

	/**
	 * Creates an instance of owa_adserver.
	 **/
	function __construct()
	{
		$this->settings = owa_coreAPI::supportClassFactory('ecoinsight','appSettings');
	}
	
	/**
	 * Gets the name of the advertiser associated
	 * with the specified campaign identifier.
	 **/
	function getAdvertiserName($jcampaignId)
	{
		$db = $this->_getDatabaseConnection();
		$db->selectFrom('dbo.jos_ad_agency_campaign', 'campaign');
		$db->join(OWA_SQL_JOIN_LEFT_OUTER, 'dbo.jos_ad_agency_advertis', 'advertiser', 'campaign.aid', 'advertiser.aid');
		$db->join(OWA_SQL_JOIN_LEFT_OUTER, 'dbo.jos_users', 'usr', 'advertiser.user_id', 'usr.id');
		$db->selectColumn('usr.Name', 'AdvertiserName');
		$db->where('campaign.id', $jcampaignId);
		$rows = $db->getOneRow();
		return $rows['AdvertiserName'];
	}
	
	/**
	 * Gets the target URL for the advertisement
	 * specified by the advertisement identifier.
	 **/
	function getTargetUrl($jadId)
	{
		$db = $this->_getDatabaseConnection();		
		$db->selectFrom('dbo.jos_ad_agency_banners');
		$db->selectColumn('target_url');
		$db->where('id', $jadId);
		$row = $db->getOneRow();
		return $row['target_url'];
	}
	
	/**
	 * Creates a database connection for retrieving the requested data.
	 **/
	function _getDatabaseConnection()
	{
		$db_type = owa_coreAPI::getSetting('base', 'db_type');
		$ret = owa_coreAPI::setupStorageEngine($db_type);

		if ($this->connection == null)
		{
			$connection_class = 'owa_db_'.$db_type;
			$this->connection = new $connection_class(
						$this->settings->getDatabaseHost(),
						$this->settings->getDatabaseName(),
						$this->settings->getDatabaseUser(),
						$this->settings->getDatabasePassword());
		}
		
		return $this->connection;
	}
}

?>