<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

require_once(OWA_BASE_CLASSES_DIR.'owa_adminController.php');

/**
 * Updates the application settings.
 **/
class owa_generalSettingsUpdateController extends owa_adminController
{
	/**
	 * Creates an instance of the update controller.
	 **/
	function __construct($params)
	{
		$this->setRequiredCapability('edit_settings');
		$this->setNonceRequired();
		return parent::__construct($params);
	
	}

	/**
	 * Executes the update action.
	 **/
	function action()
	{	
		$c = owa_coreAPI::configSingleton();
		
		$config_values = $this->get('config');
	
		if (!empty($config_values))
		{
			foreach ($config_values as $k => $v)
			{
				list($module, $name) = explode('.', $k);
				
				if ( $module && $name ) {
					$c->persistSetting($module, $name, $v);	
				}
			}
			
			$c->save();
			owa_coreAPI::notice("Configuration changes saved to database.");
			$this->setStatusCode(2500);	
		}
		
		$this->setRedirectAction('ecoinsight.generalSettings');
	}
}
?>