<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

require_once(OWA_BASE_DIR.'/owa_lib.php');
require_once(OWA_BASE_DIR.'/owa_controller.php');
require_once(OWA_BASE_MODULE_DIR.'processEvent.php');

/**
 * Processing handler for the customized ecoInsight metrics gathering.
 **/
class owa_processRequestController extends owa_processEventController 
{
	/**
	 * Creates the process request handler.
	 **/
	function __construct($params) 
	{
		return parent::__construct($params);
	}

	/**
	 * Performs any pre-processing activities, such as creating and gathering custom variables.
	 **/
	function pre()
	{
		// Capture the values which will be lost during processing.
		$userEmail = $this->event->get('user_email');
		$timestamp = $this->event->get('timestamp');
		$pageTitle = $this->event->get('page_title');
		if (!$pageTitle || $pageTitle = '(Not Set)')
		{
			$this->event->set('page_title', $this->event->get('page_url'));
		}
		
		$contentSection = $this->event->get('eco_cs');
		$pageType = $this->event->get('page_type');
		if ($contentSection && (!$pageType || $pageType == '(Not Set)'))
		{
			$this->event->set('page_type', 'Content');
		}
		
		parent::pre();
		
		// Re-include the removed values.
		if ($this->event->get('event_type') == 'track.action' && $this->event->get('soa_c') == 1)
		{
			// Core OWA filters the email address from all track.action events
			// If this is an offline click, we need to preserve the email address.
			// Add it back to the event.
			$this->event->set('user_email', $userEmail);
		}
		
		$this->event->set('original_timestamp', $timestamp);
	}

	/**
	 * Performs core actions
	 **/
	function action()
	{
		parent::action();
	}

	/**
	 * Performs event post-processing.
	 **/
	function post()
	{
		return parent::post();
	}
}

?>