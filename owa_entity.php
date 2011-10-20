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

if (!class_exists('owa_dbColumn')):
	require_once(OWA_BASE_CLASS_DIR.'column.php');
endif;

/**
 * Abstract Entity Class
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_entity {

	var $name;
	var $properties = array();
	var $_tableProperties = array();
	var $wasPersisted;
	var $cache;
	
	function __construct($cache = '', $db = '') {
		
	}
		
	
	function _getProperties() {
		
		$properties = array();
		
		if (!empty($this->properties)) {
			$vars = $this->properties;
		}
		
		foreach ($vars as $k => $v) {
			
			$properties[$k] = $v->getValue();
				
		}

		return $properties;	
	}
	
	function getColumns($return_as_string = false, $as_namespace = '', $table_namespace = false) {
		
		if (!empty($this->properties)) {
			$all_cols = array_keys($this->properties);
			$all_cols = array_flip($all_cols);
		}
		
		//print_r($all_cols);
		
		$table = $this->getTableName();
		$new_cols = array();
		$ns = '';
		$as = '';
		
		if (!empty($table_namespace)):	
			$ns = $table.'.';
		endif;
				
		foreach ($all_cols as $k => $v) {
			
			if (!empty($as_namespace)):	 
				$as =  ' AS '.$as_namespace.$k;
			endif;
			
			$new_cols[] = $ns.$k.$as;
		}
		
		// add implode as string here
		
		if ($return_as_string == true):
			$new_cols = implode(', ', $new_cols);	
		endif;
		
		//print_r($new_cols);
		return $new_cols; 
		
	}
	
	function getColumnsSql($as_namespace = '', $table_namespace = true) {
	
		return $this->getColumns(true, $as_namespace, $table_namespace);
	}
	
	/**
	 * Sets object attributes
	 *
	 * @param unknown_type $array
	 */
	function setProperties($array, $apply_filters = false) {
		
		$properties = $this->getColumns();
		
		foreach ($properties as $k => $v) {
				
			if ( ! empty( $array[$v] ) ) {
				if ( ! empty( $this->properties ) ) {
					$this->set($v, $array[$v], $apply_filters);
				}
			}
		}
	}
	
	function setGuid($string) {
		
		return owa_lib::setStringGuid($string);
		
	}
	
	function set($name, $value, $filter = true) {
		
		if ( array_key_exists( $name, $this->properties ) ) {
			$method = $name.'SetFilter';
			if ( $filter && method_exists( $this, $method ) ) {
				$this->properties[$name]->setValue( $this->$method( $value ) );
			} else {
				$this->properties[$name]->setValue( $value );
			}
		}
	}
	
	// depricated
	function setValues($values) {
		
		return $this->setProperties($values);
	}
	
	function get($name, $filter = true) {
		
		if (array_key_exists($name, $this->properties)) {
			$method = $name.'GetFilter';
			if ( $filter && method_exists($this, $method) ) {
				return $this->$method( $this->properties[$name]->getValue() );
			} else {
				return $this->properties[$name]->getValue();
			}
		}
	}
	
	function getTableOptions() {
		
		if ($this->_tableProperties) {
			if (array_key_exists('table_type', $this->_tableProperties)) {
				return $this->_tableProperties['table_type'];
			}
		}
		
		return array('table_type' => 'disk');		
	
	}
	
	/**
	 * Persist new object
	 *
	 */ 
	function create() {	
		
		$db = owa_coreAPI::dbSingleton();		
		$all_cols = $this->getColumns();
		$tab_name = $this->getTableName();
		$db->insertInto($this->getTableName());
		
		// Control loop
		foreach ($all_cols as $k => $v){
		
			// drop column is it is marked as auto-incement as DB will take care of that.
			if ($this->properties[$v]->auto_increment === true):
				;
			else:
				$db->set($v, $this->get($v, false));
			endif;
				
		}
	
		// Persist object
		$status = $db->executeQuery();
		
		// Add to Cache
		if ($status == true) {
			$this->addToCache();
		}
		
		return $status;
	}
	
	function save() {
		
		if ( $this->wasPersisted ) {
			return $this->update();
		} else {
			return $this->create();
		}
	}
	
	function addToCache($col = 'id') {
		
		if($this->isCachable()) {
			$cache = &owa_coreAPI::cacheSingleton();
			$cache->setCollectionExpirationPeriod($this->getTableName(), $this->getCacheExpirationPeriod());
			$cache->set($this->getTableName(), $col.$this->get('id'), $this, $this->getCacheExpirationPeriod());
		}
	}
	
	/**
	 * Update all properties of an Existing object
	 *
	 */
	function update($where = '') {	
		
		$db = owa_coreAPI::dbSingleton();	
		$db->updateTable($this->getTableName());
		
		// get column list
		$all_cols = $this->getColumns();
		
		$db_name = $this->getTableName();
		
		// Control loop
		foreach ($all_cols as $k => $v){
		
			// drop column is it is marked as auto-incement as DB will take care of that.
			
			if ($this->get($v, false)) {
				$db->set($v, $this->get($v, false));
			}	
		}
		
		if(empty($where)):
			$id = $this->get('id');
			$db->where('id', $id);
			
		else:
			$db->where($where, $this->get($where));
		endif;
		
		// Persist object
		$status = $db->executeQuery();
		// Add to Cache
		if ($status !== false) {
			$this->addToCache();
		}
		
		return $status;
		
	}
	
	/**
	 * Update named list of properties of an existing object
	 *
	 * @param array $named_properties
	 * @param array $where
	 * @return boolean
	 */
	function partialUpdate($named_properties, $where) {
		
		$db = &owa_coreAPI::dbSingleton();		
		$db->updateTable($this->getTableName());
		
		foreach ($named_properties as $v) {
			
			if ($this->get($v)){
				$db->set($v, $this->get($v));
			}
		}
		owa_coreAPI::debug('Inside Partial update function of owa_entity.php:'. $where );
		if(empty($where)):
			$db->where('id', $this->get('id'));
		else:
			$db->where($where, $this->get($where));
		endif;
		
		// Persist object
		$status = $db->executeQuery();
		// Add to Cache
		if ($status != false) {
			$this->addToCache();
		}
		
		return $status;
	}
	
	
	/**
	 * Delete Object
	 *
	 */
	function delete($value = '', $col = 'id') {	
		
		$db = owa_coreAPI::dbSingleton();	
		$db->deleteFrom($this->getTableName());
		
		if (empty($value)) {
			$value = $this->get('id');
		}
		
		$db->where($col, $value);	

		$status = $db->executeQuery();
	
		// Delete from Cache
		if ( $status ){
			if ($this->isCachable()) {
				owa_coreAPI::debug('about to remove from cache');
				$cache =  &owa_coreAPI::cacheSingleton();
				$cache->remove($this->getTableName(), $col.$value);
			}			
		}
		
		return $status;
		
	}
	
	function load($value, $col = 'id') {
		
		return $this->getByColumn($col, $value);
		
	}
	
	function getByPk($col, $value) {
		
		return $this->getByColumn($col, $value);
		
	}
	
	function getByColumn($col, $value) {
		
		if ( ! $col ) {
			owa_coreAPI::debug('No column name passed to getByColumn in entity:'. getName() );
			return;
		}
		
		$cache_obj = '';
		
		if ($this->isCachable()) {
			$cache =  &owa_coreAPI::cacheSingleton();
			$cache->setCollectionExpirationPeriod($this->getTableName(), $this->getCacheExpirationPeriod());
			$cache_obj = $cache->get($this->getTableName(), $col.$value);
		}		
			
		if (!empty($cache_obj)) {
		
			$cache_obj_properties = $cache_obj->_getProperties();
			$this->setProperties($cache_obj_properties);
			$this->wasPersisted = true;
					
		} else {
		
			$db = owa_coreAPI::dbSingleton();
			$db->selectFrom($this->getTableName());
			$db->selectColumn('*');
			$db->where($col, $value);
			$properties = $db->getOneRow();
			
			if (!empty($properties)) {
				
				$this->setProperties($properties);
				$this->wasPersisted = true;
				// add to cache			
				$this->addToCache($col);
				owa_coreAPI::debug('entity loaded from db');		
			}
		} 
	}

	function getTableName() {
		
		if ($this->_tableProperties) {
			return $this->_tableProperties['name'];
		} else {
			return get_class($this);
		}
		
	}
	
	function getTableAlias() {
		
		if ($this->_tableProperties) {
			return $this->_tableProperties['alias'];
		}
	}
	
	function setTableAlias( $alias ) {
	
		$this->_tableProperties['alias'] = $alias;
	}
	
	function setTableName($name, $namespace = 'owa_') {

		$this->_tableProperties['alias'] = $name;
		$this->_tableProperties['name'] = $namespace.$name;
	}	
	
	/**
	 * Sets the entity as cachable for some period of time
	 *
	 * @todo	make this use the getSetting method but that requires a refactoring of
	 *			the entity abstract class to not use an entity in it's constructor
	 */
	function setCachable($seconds = '') {
	
		$this->_tableProperties['cacheable'] = true;
		
		// set cache expiration period
		if (!$seconds) {
			// remove hard coded value. fix this see note above.
			//$seconds = owa_coreAPI::getSetting('base', 'default_cache_expiration_period');
			$seconds = 604800;
		}
		
		$this->setCacheExpirationPeriod($seconds);
	}
	
	function isCachable() {
		
		if (owa_coreAPI::getSetting('base', 'cache_objects')) {
			if (array_key_exists('cacheable', $this->_tableProperties)) {
				return $this->_tableProperties['cacheable'];
			}
		} else {
			return false;
		}
		
	}
	
	function setPrimaryKey($col) {
		//backwards compatability
		$this->properties[$col]->setPrimaryKey();
		$this->_tableProperties['primary_key'] = $col;
		
	}
		
	function getForeignKeyColumn($entity) {
		if (array_key_exists('relatedEntities', $this->_tableProperties)) {
			if (array_key_exists($entity, $this->_tableProperties['relatedEntities'])) {
				return $this->_tableProperties['relatedEntities'][$entity];
			}
		}
	}
	
	function isForeignKeyColumn($col) {
	
		if (array_key_exists($col, $this->properties)) {
			return $this->properties[$col]->isForeignKey();
		}
	}
	
	function getAllForeignKeys() {
		
		return;
	}
	
	/**
	 * Create Table
	 *
	 * Handled by DB abstraction layer because the SQL associated with this is way too DB specific
	 */
	function createTable() {
		
		$db = owa_coreAPI::dbSingleton();
		// Persist table
		$status = $db->createTable($this);
		
		if ($status == true):
			owa_coreAPI::notice(sprintf("%s Table Created.", $this->getTableName()));
			return true;
		else:
			owa_coreAPI::notice(sprintf("%s Table Creation Failed.", $this->getTableName()));
			return false;
		endif;
	
	}
	
	/**
	 * DROP Table
	 *
	 * Drops a table. will throw error is table does not exist
	 */
	function dropTable() {
		
		$db = owa_coreAPI::dbSingleton();
		// Persist table
		$status = $db->dropTable($this->getTableName());
		
		if ($status == true):
			return true;
		else:
			return false;
		endif;
	
	}
	
	function addColumn($column_name) {
		
		$def = $this->getColumnDefinition($column_name);
		// Persist table
		$db = owa_coreAPI::dbSingleton();
		$status = $db->addColumn($this->getTableName(), $column_name, $def);
		
		if ($status == true):
			return true;
		else:
			return false;
		endif;
		
	}
	
	function dropColumn($column_name) {
		
		$db = owa_coreAPI::dbSingleton();
		$status = $db->dropColumn($this->getTableName(), $column_name);
		
		if ($status == true):
			return true;
		else:
			return false;
		endif;		
		
	}
	
	function modifyColumn($column_name) {
	
		$def = $this->getColumnDefinition($column_name);		
		$db = owa_coreAPI::dbSingleton();
		$status = $db->modifyColumn($this->getTableName(), $column_name, $def);
		
		if ($status == true):
			return true;
		else:
			return false;
		endif;		
	
	
	}
	
	function renameColumn($old_column_name, $column_name, $use_old_column_for_defs = false) {
	
		if ($use_old_column_for_defs) {
			$def = $this->getColumnDefinition($old_column_name);
		} else {
			$def = $this->getColumnDefinition($column_name);
		}
		
		$db = owa_coreAPI::dbSingleton();
		$status = $db->renameColumn($this->getTableName(), $old_column_name, $column_name, $def);
		
		if ($status == true):
			return true;
		else:
			return false;
		endif;		
		
	}
	
	function renameTable($new_table_name) {
		
		$db = owa_coreAPI::dbSingleton();
		$status = $db->renameTable($this->getTableName(), $new_table_name);
		
		if ($status == true):
			return true;
		else:
			return false;
		endif;		
		return;
	}
	
	function getColumnDefinition($column_name) {
	   $tab_name = $this->getTableName();
		if (empty($this->properties)) {
			return $this->$column_name->getDefinition($tab_name);
		} else {
			return $this->properties[$column_name]->getDefinition($tab_name);
		}
	}
	
	function setProperty($obj) {
		
		$this->properties[$obj->get('name')] = $obj;
		
		if ($obj->isForeignKey()) {
			$fk = $obj->getForeignKey();
			
			$this->_tableProperties['relatedEntities'][$fk[0]] = $obj->getName();
			$this->_tableProperties['foreign_keys'][$obj->getName()] = $fk[0];
		}
		
	}
	
	function getProperty($name) {
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}
	}
	
	function generateRandomUid($seed = '') {
		
		return crc32($_SERVER['SERVER_ADDR'].$_SERVER['SERVER_NAME'].getmypid().$this->getTableName().microtime().$seed.rand());
	}
	
	/**
	 * Create guid from string
	 *
	 * @param 	string $string
	 * @return 	integer
	 */
	function generateId($string) {
		//require_once(OWA_DIR.'owa_lib.php');
		return owa_lib::setStringGuid($string);
	}
	
	function setCacheExpirationPeriod($seconds) {
		
		$this->_tableProperties['cache_expiration_period'] = $seconds;
	}
	
	function getCacheExpirationPeriod() {
		
		if (array_key_exists('cache_expiration_period', $this->_tableProperties)) {
			return $this->_tableProperties['cache_expiration_period'];
		} else {
			// default of thirty days
			return (3600);
		}
	}
	
	function getName() {
		
		return $this->name;
	}
	
	function setSummaryLevel($num) {
		
		$this->_tableProperties['summary_level'] = $num;
	}
	
	function getSummaryLevel() {
		
		if (array_key_exists('summary_level', $this->_tableProperties)) {
			
			return $this->_tableProperties['summary_level'];
		
		} else {
		
			return 0;
		}
	}
	
	function setCharacterEncoding($encoding) {
		
		$this->_tableProperties['character_encoding'] = $encoding;
	}
	
	function wasPersisted() {
		return $this->wasPersisted;
	}
}

?>
