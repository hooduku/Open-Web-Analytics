<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

require_once(OWA_BASE_DIR.'/owa_module.php');

/**
 * Module for processing core ecoInsight metrics.
 **/
class owa_ecoinsightModule extends owa_module
{
	/**
	 * Creates an instance of the ecoInsight metrics module
	 **/
	function __construct()
	{

		$this->name = 'ecoinsight';
		$this->display_name = 'ecoInsight Metrics';
		$this->group = 'ecoinsight';
		$this->author = 'ecoInsight, Inc.';
		$this->version = '1.0';
		$this->description = 'Captures and process the ecoInsight service-oriented analytics and advertising-related.';
		$this->config_required = false;
		$this->required_schema_version = 1;
		owa_coreAPI::debug("Loading ecoInsight Module");


		// Time Dimensions (Needed for trend)
		$this->registerDimension(
					'date',
					'ecoinsight.eco_core',
					'yyyymmdd',
					'Date',
					'time',
					'The full date.',
					'',
					true,
					'yyyymmdd'
		);

		// Site Id (needed for Trend)
		$this->registerDimension(
					'siteId',
					'ecoinsight.eco_core',
					'site_id',
					'Site ID',
					'site',
					'The ID of the the web site.',
					'',
					true
		);

		// Session identifier
		$this->registerDimension(
			'sessionId',
			'ecoinsight.eco_core',
			'session_id',
			'Session ID',
			'visit-special',
			'The ID of the session/visit.',
			'',
			true
		);

		return parent::__construct();
	}

	/**
	 * Registers administrative settings panels.
	 **/
	function registerAdminPanels()
	{
		$this->addAdminPanel(array( 'do' 			=> 'ecoinsight.generalSettings', 
									'priviledge' 	=> 'admin', 
									'anchortext' 	=> 'General Settings',
									'group'			=> 'ecoInsight',
									'order'			=> 1));		
		return;
	}

	/**
	 * Registers the left-hand navigation.
	 **/
	function registerNavigation()
	{
		return;
	}


	/**
	 * Registers Event Handlers with queue queue
	 **/
	function _registerEventHandlers()
	{
		$this->_addHandler('base.page_request', 'coreRequestHandler');
		$this->_addHandler('track.action', 'coreRequestHandler');
		$this->_addHandler('ecoinsight.sendAdRequest', 'sendAdRequestHandler');
		return;
	}

	/**
	 * Registers an event processor to perform the initial request handling
	 **/
	function _registerEventProcessors()
	{
			$this->addEventProcessor('base.page_request', 'ecoinsight.processRequest');
			$this->addEventProcessor('track.action', 'ecoinsight.processRequest');
	}

	/**
	 * Registers the entities.
	 **/
	function _registerEntities()
	{
		$this->registerEntity(array('eco_core'));
	}
}


?>