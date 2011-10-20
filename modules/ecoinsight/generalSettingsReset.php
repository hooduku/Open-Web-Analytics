<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

require_once(OWA_BASE_CLASSES_DIR.'owa_adminController.php');

/**
 * A controller for resetting the ecoInsight settings.
 **/
class owa_generalSettingsResetController extends owa_adminController
{
	/**
	 * Creates an instance of the controller class.
	 **/
	function __construct($params)
	{	
		$this->setRequiredCapability('edit_settings');
		return parent::__construct($params);	
	}

	/**
	 * Invokes the action for the reset controller.
	 **/
	function action()
	{	
		$appSettings = owa_coreAPI::supportClassFactory('ecoinsight','appSettings');
		$appSettings->reset();
		
		$this->e->notice($this->getMsg(2503));
		$this->setStatusCode(2503);
		
		$this->setRedirectAction('ecoinsight.generalSettings');
	}
}

?>