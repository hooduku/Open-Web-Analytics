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

/**
 * Unique Visitors metric
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.3.0
 */

class owa_repeatVisitors extends owa_metric {

	function __construct() {
	
		$this->setName('repeatVisitors');
		$this->setLabel('Repeat Visitors');
		$this->setEntity('base.session');
		$this->setSelect("(count(distinct(session.visitor_id)) - sum(CASE session.is_new_visitor WHEN CONVERT(bit,'true') THEN 1 ELSE 0 END))");
		$this->setDataType('integer');
		
		return parent::__construct();
	}
}

?>
