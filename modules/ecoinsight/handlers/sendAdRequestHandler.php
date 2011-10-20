<?php

//
// Copyright (c) 2011 ecoInsight, Inc. All rights reserved.
//

if(!class_exists('owa_observer')) {
	require_once(OWA_BASE_DIR.'owa_observer.php');
}

/**
 * Module for processing events and writing them to the core table.
 **/
class owa_sendAdRequestHandler extends owa_observer 
{
	/**
	 * Notification handler called when the request is being processed.
	 **/
	function notify($event)
	{
		owa_coreAPI::debug('Handling sendAdRequest');
		if (!owa_coreAPI::supportClassFactory('ecoinsight','appSettings')->isNotificationEnabled())
		{
			owa_coreAPI::debug('Notification disabled.');
			return OWA_EHS_EVENT_HANDLED;
		}
		
		if ($event->get('soa_ci') != '' && $event->get('eco_cid') != '')
		{
			$adserver= owa_coreAPI::supportClassFactory('ecoinsight','adserver');
			$advertiser = $adserver->getAdvertiserName($event->get('eco_cid'));
			if ($advertiser == '')
			{
				owa_coreAPI::error('sendAdRequest: Missing advertiser or campaign.');
				return OWA_EHS_EVENT_FAILED;
			}
			
			$targetUrl = $adserver->getTargetUrl($event->get('soa_ci'));
			if ($targetUrl == '')
			{
				owa_coreAPI::error('sendAdRequest: Missing target url for ad.');
				return OWA_EHS_EVENT_FAILED;
			}
			
			$ret = $this->sendNotificationRequest($event, $advertiser, $targetUrl);
			if ($ret)
			{
				owa_coreAPI::debug('sendAdRequest: Notification sent to user for request ' . $event->get('guid'));
				return OWA_EHS_EVENT_HANDLED;
			}

			owa_coreAPI::error('sendAdRequest: Failed to send notification for request ' . $event->get('guid'));
			return OWA_EHS_EVENT_FAILED;
		}
		else
		{
			owa_coreAPI::error('sendAdRequest: Invalid or missing advertisement parameters.');
			return OWA_EHS_EVENT_HANDLED;
		}
	}
	
	/**
	 * Sends the email request notification.
	 **/
	function sendNotificationRequest($event, $advertiser, $targetUrl)
	{
		$appSettings = owa_coreAPI::supportClassFactory('ecoinsight','appSettings');
		$url = $appSettings->getNotificationUrl();
		if ($url == '')
		{
			return false;
		}
		
		if (substr_compare($url, '/', -strlen('/'), strlen('/')) == 0)
		{
			$url = substr($url, 0, strlen($url) - 1);
		}
		
		owa_coreAPI::debug('Preparing to notify endpoint - ' . $url); 
		
		// Open the connection
		$httpSession = curl_init();
		$data = $this->serializeRequest($event->get('guid'), $advertiser, $targetUrl, $event->get('user_name'), $event->get('user_email'));
		
		// Configure curl
		curl_setopt($httpSession, CURLOPT_URL, $url);
		curl_setopt($httpSession, CURLOPT_POST, 1);
		curl_setopt($httpSession, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($httpSession, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($httpSession, CURLOPT_POSTFIELDS, $data);
		curl_setopt($httpSession, CURLOPT_FAILONERROR, true);
		curl_setopt($httpSession, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Content-length: ".strlen($data)));
		curl_setopt($httpSession, CURLOPT_SSL_VERIFYPEER, false);
 

		if (defined('ECO_HTTP_PROXY'))
		{
			curl_setopt($httpSession, CURLOPT_PROXY, ECO_HTTP_PROXY);
		}

		// Post the data
		$content = curl_exec($httpSession);
		$result = false;
		
		// If the response is 202 (Accepted), the notification has reached
		// the target server. If not, an error occured.
		// TODO: Log failures to a table for later retry or analysis.
		if (curl_getinfo($httpSession, CURLINFO_HTTP_CODE) != 202)
		{
			$msg = sprintf('Error occurred while sending notification: %s', curl_error($httpSession));
			owa_coreAPI::error($msg);
			owa_coreAPI::error(curl_getinfo($httpSession));
		}
		else
		{
			$result = true;
		}
		
		// Cleanup the connection.
		curl_close($httpSession);
		
		return $result;
	}
	
	/**
	 * Serializes the parameters into a notification request.
	 **/
	function serializeRequest($id, $advertiser, $targetUrl, $fullName, $email)
	{
		$doc = new DOMDocument("1.0");
		$doc->formatOutput = false;
		$root = $doc->createElementNS('http://schema.ecoinsight.com/integration/advertising/2011/09/service', 'AdRequestNotification');
		$root = $doc->appendChild($root);
		$childNs = $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance');
		
		$this->createElement($doc, $root, 'Advertiser', $advertiser);
		$this->createElement($doc, $root, 'Email', $email);
		$this->createElement($doc, $root, 'FullName', $fullName);
		$this->createElement($doc, $root, 'RequestId', $id);
		$this->createElement($doc, $root, 'TargetUrl', $targetUrl);
		
		return $doc->SaveXML();
	}
	
	/**
	 * Creates a serialized element.
	 **/
	function createElement($doc, $parent, $name, $value)
	{
		$element = $doc->createElement($name);
		$element = $parent->appendChild($element);
		
		$text = $doc->createTextNode($value);
		$text = $element->appendChild($text);
	}
}

?>
