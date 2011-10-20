<?php

//
// Open Web Analytics - An Open Source Web Analytics Framework
//
// Copyright 2006 Peter Adams. All rights reserved.
//
// Licensed under GPL v2.0 http://www.gnu.org/copyleft/gpl.html
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// $Id$
//

if(!class_exists('owa_observer')) {
	require_once(OWA_BASE_DIR.'owa_observer.php');
}	

/**
 * OWA User Agent Event handlers
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_userAgentHandlers extends owa_observer {
    	
    /**
     * Notify Event Handler
     *
     * @param 	unknown_type $event
     * @access 	public
     */
    function notify($event) {
		
		$ua = owa_coreAPI::entityFactory('base.ua');
		
		$ua->getByColumn('id', owa_lib::setStringGuid($event->get('HTTP_USER_AGENT')));
		
		if (!$ua->get('id')) {
			
			$ua->setProperties($event->getProperties());
			$ua->set('ua', $event->get('HTTP_USER_AGENT'));
			$ua->set('id', owa_lib::setStringGuid($event->get('HTTP_USER_AGENT'))); 
			$ret = $ua->create();
			
			if ( $ret ) {
				return OWA_EHS_EVENT_HANDLED;
			} else {
				return OWA_EHS_EVENT_FAILED;
			}
			
		} else {
		
			owa_coreAPI::debug('not logging, user agent already exists.');
			return OWA_EHS_EVENT_HANDLED;
		}
    }
}

?>