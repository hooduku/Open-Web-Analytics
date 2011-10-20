<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

require_once(OWA_DIR.'owa_lib.php');
require_once(OWA_DIR.'owa_view.php');
require_once(OWA_DIR.'owa_adminController.php');

/**
 * Controller class for the general settings.
 **/
class owa_generalSettingsController extends owa_adminController
{
	/**
	 * Creates an instance of owa_generalSettingsController.
	 **/	
	function __construct($params)
	{
		parent::__construct($params);
		$this->type = 'options';
		$this->setRequiredCapability('edit_settings');
	}
	
	/**
	 * Executes the controller behavior.
	 **/
	function action()
	{	
		$appSettings = owa_coreAPI::supportClassFactory('ecoinsight','appSettings');
		$this->data['configuration'] = $appSettings->getSettings();
	
		// add data to container
		$this->setView('base.options');
		$this->setSubview('ecoinsight.generalSettings');
	}
}

/**
 * Represents a view for displaying settings.
 **/
class owa_generalSettingsView extends owa_view
{
	/**
	 * Creates an instance of owa_generalSettingsView
	 **/
	function __construct($params)
	{
		//set page type
		$this->_setPageType('Administration Page');		
		return parent::__construct($params);
	}
	
	/**
	 * Renders the view.
	 **/
	function render($data)
	{
		// load template
		$this->body->setTemplateFile('ecoinsight', 'generalSettings.tpl');

		// fetch admin links from all modules
		$this->body->set('headline', 'General Settings');
		
		$this->body->set('config', $data['configuration']);		
	}
}

?>