<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

if(!class_exists('owa_observer')) {
	require_once(OWA_BASE_DIR.'owa_observer.php');
}

define('ECO_PLATFORM_IPAD', 'iPad');
define('ECO_PLATFORM_SILVERLIGHT', 'Silverlight');

/**
 * Module for processing events and writing them to the core table.
 **/
class owa_coreRequestHandler extends owa_observer
{
	/**
	 * Notification handler called when the request is being processed.
	 * @param event $event the event
	 **/
	function notify($event)
	{
		if ($event->get('soa_an'))
		{
	    	$entity = owa_coreAPI::entityFactory('ecoinsight.eco_core');
			$entity->load($event->get('guid'));
			if (!$entity->wasPersisted() )
			{
				$ret = $this->createEntity($event, $entity);
				$this->processOfflineClickEvent($event);
				$this->updateVisitor($event);
				
				if ( $ret )
				{
					return OWA_EHS_EVENT_HANDLED;
				}
				else
				{
					owa_coreAPI::debug('Event failed to persist.');
					return OWA_EHS_EVENT_FAILED;
				}
			}
			else
			{
				owa_coreAPI::debug('Event previously persisted.');
					return OWA_EHS_EVENT_HANDLED;
			}
		}
		else
		{
			owa_coreAPI::debug('No application name parameter.');
			return OWA_EHS_EVENT_HANDLED;
		}
	}
	
	/**
	 * Creates an entity to log the request.
	 * @param mixed $event the current event instance
	 * @param mixed $entity the core entity to be created.
	 * @return bool true if the entity is created, otherwise false.
	 **/
	function createEntity($event, $entity)
	{
		$entity->setProperties($event->getProperties());

		// Set Primary Key
		$entity->set('id', $event->get('guid'));

		$entity->set('jadcampaign_id', '');
		$entity->set('jadzone_id', null);		

		// Set table properties
		$this->assignProperty($event, $entity, 'soa_an', 'app_name');
		$this->assignProperty($event, $entity, 'soa_vs', 'app_version');
		$this->assignProperty($event, $entity, 'soa_ci', 'content_identifier');
		$this->assignProperty($event, $entity, 'soa_ns', 'navstate');
		$this->assignProperty($event, $entity, 'soa_on', 'object_name');
		$this->assignProperty($event, $entity, 'soa_sn', 'state_name');
		$this->assignProperty($event, $entity, 'soa_c', 'offline');
		$this->assignProperty($event, $entity, 'soa_fs', 'fullscreen');
		$this->assignProperty($event, $entity, 'soa_is', 'installstate');
		$this->assignProperty($event, $entity, 'soa_tc', 'timecode');
		$this->assignProperty($event, $entity, 'soa_z', 'zoomfactor');
		$this->assignProperty($event, $entity, 'soa_sl', 'runtime_version');
		$this->assignProperty($event, $entity, 'eco_cid', 'jadcampaign_id');
		$this->assignProperty($event, $entity, 'eco_zid', 'jadzone_id');
		$this->assignProperty($event, $entity, 'eco_cs', 'content_section');
		$this->assignProperty($event, $entity, 'user_email', 'user_email');
		
		if ($event->get('original_timestamp'))
		{
			$ts = $event->get('original_timestamp');
			
			// Store a timestamp representing when the event actually occurred.
			$entity->set('original_timestamp', $event->get('original_timestamp'));
			
			// Store an ISO-8601 timestamp representing when the event actually occurred.
			$entity->set('original_datetime', gmdate("Y-m-d\TH:i:s", $ts));
		}
		
		if($event->get('eco_cid') != null &&  $event->get('eco_zid') != null)
		{
			// Need to track our ad campaign/zone/ad
			$entity->set('jad_id', $event->get('soa_ci'));
		}
							
		$res = $event->get('soa_rs');
		if ($res != null)
		{
			$resolution = explode('x', $res);
			if ($resolution != null && count($resolution) == 2)
			{
				$entity->set('client_height', $resolution[0]);
				$entity->set('client_width', $resolution[1]);
			}
		}

		$entity->set('platform', $this->getClientPlatform($event));
		
		if ($event->get('eco_projcost'))
		{
			$entity->set('cost', $event->get('eco_projcost'));
			$entity->set('region', $event->get('eco_state'));
			$entity->set('postal_code', $event->get('eco_zip'));
			$actgrp = mb_strtolower($event->get('action_group'), 'UTF-8');
			$actname = mb_strtolower($event->get('action_name'), 'UTF-8');
			$actlabel = mb_strtolower($event->get('action_label'), 'UTF-8');
			$entity->set('cost_type', $actgrp);
			$entity->set('cost_subtype', $actname);
			$entity->set('storage', $actlabel);
		}
		
		$ret = $entity->create();		
		return $ret;
	}
	
	/**
	 * Assigns the specified property if it exists
	 * @param mixed $event the event instance
	 * @param mixed $entity the entity being created
	 * @param string $source the name of the event property
	 * @param string $dest the name of the entity property
	 **/
	function assignProperty($event, $entity, $source, $dest)
	{
		$value = $event->get($source);
		if ($value)
		{
			$entity->set($dest, $value);
		}
	}
	
	/**
	 * Retrieves the name of the client application platform.
	 * @param mixed $event the event instance.
	 * @return string the name of the client platform.
	 **/
	function getClientPlatform($event)
	{
		if (strpos($event->get('soa_an'), 'Mobile Audit') > 0)
		{
			return ECO_PLATFORM_IPAD;
		}
		else
		{
			return ECO_PLATFORM_SILVERLIGHT;
		}
	}
	
	/**
	 * Updates the visitor record with the email address.
	 * @param mixed $event the event instance.
	 **/
	function updateVisitor($event)
	{
		$visitor = owa_coreAPI::entityFactory('base.visitor');
		$visitor->load($event->get('visitor_id'));
		if ($visitor->wasPersisted())
		{
			$shouldUpdateVisitor = false;
			if ($visitor->get('user_email') == '' && $event->get('user_email') != '')
			{
				$visitor->set('user_email', $event->get('user_email'));
				$shouldUpdateVisitor = true;
			}
			
			if ($visitor->get('user_name') == '' && $event->get('user_name') != '')
			{
				$visitor->set('user_name', $event->get('user_name'));
				$shouldUpdateVisitor = true;
			}

			if ($shouldUpdateVisitor)
			{
				$visitor->save();
			}
		}
	}
	
	/**
	 * Processes an offline click and creates a request
	 * for an appropriate notification if necessary.
	 * @param mixed $event the event instance.
	 **/
	function processOfflineClickEvent($event)
	{
		if ($event->get('soa_c') == '1' 
			&& $event->get('user_email') != ''
			&& $event->get('user_name') != ''
			&& $event->get('event_type') == 'track.action'
			&& $this->getClientPlatform($event) ==	ECO_PLATFORM_IPAD)
			{
				$this->createOfflineNotification($event);
			}
	}

	/**
	 * Creates a notification for an offline event.
	 * @param mixed $event the event instance.
	 **/	
	function createOfflineNotification($event)
	{
		owa_coreAPI::debug('Creating event notification: ecoinsight.sendAdRequest');
		$dispatch = owa_coreAPI::getEventDispatch();
		$notification = $dispatch->makeEvent('ecoinsight.sendAdRequest');
		$notification->setProperties($event->getProperties());
		$dispatch->asyncNotify($notification);
	}
}
?>
