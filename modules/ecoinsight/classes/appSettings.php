<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

/**
 * Represents the customized application settings.
 **/
class owa_appSettings
{
	/**
	 * Stores the configuration instance.
	 **/
	var $config;
	
	/**
	 * Stores the module name.
	 **/
	var $moduleName;

	/**
	 * Creates an instance of the settings class.
	 **/
	function __construct()
	{
		$this->config = owa_coreAPI::configSingleton();
		$this->moduleName = 'ecoinsight';
	}
	
	/**
	 * Retrieves a collection of all settings.
	 **/
	function getSettings()
	{
		if ($this->get('enable_offline_ad_notification') == '')
		{
			$this->reset();
		}
		
		return $this->config->fetch($this->moduleName);
	}
	
	/**
	 * Indicates whether notification is enabled.
	 **/
	function isNotificationEnabled()
	{
		return $this->get('enable_offline_ad_notification');	
	}
	
	/**
	 * Gets the notification URL.
	 **/
	function getNotificationUrl()
	{
		return $this->get('offline_ad_notification_url');
	}
	
	/**
	 * Gets the name of the database server containing
	 * the advertising data.
	 **/
	function getDatabaseHost()
	{
		return $this->get('ad_db_host');
	}

	/**
	 * Gets the name of the database containing the 
	 * advertising data.
	 **/
	function getDatabaseName()
	{
		return $this->get('ad_db_name');
	}

	/**
	 * Gets the name of the advertising database user.
	 **/
	function getDatabaseUser()
	{
		return $this->get('ad_db_user');
	}

	/**
	 * Gets the password for accessing the advertising
	 * database.
	 **/
	function getDatabasePassword()
	{
		return $this->get('ad_db_password');
	}	
	
	/**
	 * Gets the specified setting for the module
	 **/
	function get($name)
	{	
		return owa_coreAPI::getSetting($this->moduleName, $name);
	}
	
	/**
	 * Sets the specified setting for the module.
	 **/
	function set($name, $value)
	{
		$this->config->set($this->moduleName, $name, $value);
	}
	
	/**
	 * Resets all settings to their default values.
	 **/
	function reset()
	{
		// Cannot call the config reset function since it will
		// break the base server configuration.
		
		$this->set('enable_offline_ad_notification', true);
		$this->set('offline_ad_notification_url', 'https://app.ecoinsight.com/services/advertising/notify');
		$this->set('ad_db_host', '');
		$this->set('ad_db_name', '');
		$this->set('ad_db_user', '');
		$this->set('ad_db_password', '');
		$this->config->save();
	}
}

?>