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


define('OWA_DTD_BIGINT', 'BIGINT');
define('OWA_DTD_INT', 'INT');
define('OWA_DTD_TINYINT', 'TINYINT');
define('OWA_DTD_TINYINT2', 'SMALLINT');
define('OWA_DTD_TINYINT4', 'INT'); //can't specify column width for tinyint in sqlserver
define('OWA_DTD_SERIAL', 'numeric');
define('OWA_DTD_PRIMARY_KEY', 'PRIMARY KEY');
define('OWA_DTD_VARCHAR10', 'VARCHAR(10)');
define('OWA_DTD_VARCHAR255', 'VARCHAR(255)');
define('OWA_DTD_VARCHAR', 'VARCHAR(%s)');
define('OWA_DTD_TEXT', 'NVARCHAR(MAX)');
define('OWA_DTD_BOOLEAN', 'BIT');
define('OWA_DTD_TIMESTAMP', 'TIMESTAMP');
define('OWA_DTD_BLOB', 'NVARCAHR(MAX)');
define('OWA_DTD_INDEX', 'KEY');
define('OWA_DTD_AUTO_INCREMENT', ' IDENTITY(1,1) ');
define('OWA_DTD_NOT_NULL', 'NOT NULL');
define('OWA_DTD_UNIQUE', 'PRIMARY KEY(%s)');
define('OWA_SQL_ADD_COLUMN', 'ALTER TABLE %s ADD %s %s');
define('OWA_SQL_DROP_COLUMN', 'ALTER TABLE %s DROP %s');
define('OWA_SQL_RENAME_COLUMN', 'ALTER TABLE %s CHANGE %s %s %s');
define('OWA_SQL_MODIFY_COLUMN', 'ALTER TABLE %s MODIFY %s %s');
define('OWA_SQL_RENAME_TABLE', 'ALTER TABLE %s RENAME %s');
define('OWA_SQL_CREATE_TABLE', 'CREATE TABLE IF NOT EXISTS %s (%s) %s');
define('OWA_SQL_DROP_TABLE', 'DROP TABLE IF EXISTS %s');
define('OWA_SQL_INSERT_ROW', 'INSERT into %s (%s) VALUES (%s)');
define('OWA_SQL_UPDATE_ROW', 'UPDATE %s SET %s %s');
define('OWA_SQL_DELETE_ROW', "DELETE from %s %s");
define('OWA_SQL_CREATE_INDEX', 'CREATE INDEX %s ON %s (%s)');
define('OWA_SQL_DROP_INDEX', 'DROP INDEX %s ON %s');
define('OWA_SQL_INDEX', 'INDEX (%s)');
define('OWA_SQL_BEGIN_TRANSACTION', 'BEGIN');
define('OWA_SQL_END_TRANSACTION', 'COMMIT');
define('OWA_DTD_TABLE_TYPE', 'ENGINE = %s');
define('OWA_DTD_TABLE_TYPE_DEFAULT', 'INNODB');
define('OWA_DTD_TABLE_TYPE_DISK', 'INNODB');
define('OWA_DTD_TABLE_TYPE_MEMORY', 'MEMORY');
define('OWA_SQL_ALTER_TABLE_TYPE', 'ALTER TABLE %s ENGINE = %s');
define('OWA_SQL_JOIN_LEFT_OUTER', 'LEFT OUTER JOIN');
define('OWA_SQL_JOIN_LEFT_INNER', 'LEFT INNER JOIN');
define('OWA_SQL_JOIN_RIGHT_OUTER', 'RIGHT OUTER JOIN');
define('OWA_SQL_JOIN_RIGHT_INNER', 'RIGHT INNER JOIN');
define('OWA_SQL_JOIN', 'JOIN');
define('OWA_SQL_DESCENDING', 'DESC');
define('OWA_SQL_ASCENDING', 'ASC');
define('OWA_SQL_REGEXP', 'REGEXP');
define('OWA_SQL_NOTREGEXP', 'NOT REGEXP');
define('OWA_SQL_LIKE', 'LIKE');
define('OWA_SQL_ADD_INDEX', 'ALTER TABLE %s ADD INDEX (%s) %s');
define('OWA_SQL_COUNT', 'COUNT(%s)');
define('OWA_SQL_ROUND', 'ROUND(%s,0)');
define('OWA_SQL_AVERAGE', 'AVG(%s)');
define('OWA_SQL_DISTINCT', 'DISTINCT %s');
define('OWA_SQL_DIVISION', '(%s / %s)');
define('OWA_DTD_CHARACTER_ENCODING_UTF8', 'utf8');
define('OWA_DTD_TABLE_CHARACTER_ENCODING', 'CHARACTER SET = %s');


/**
 * MySQL Data Access Class
 *
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$
 * @since		owa 1.0.0
 */
class owa_db_sqlserver extends owa_db {

	function connect() {

		$server_name = $this->getConnectionParam('host');

	 if (!$this->connection) {
			if ($this->getConnectionParam('persistant')) {
				$connectioninfo = array(

					'UID'=>$this->getConnectionParam('user'),
					'PWD'=>$this->getConnectionParam('password'),
					'Database'=>$this->getConnectionParam('name'),
					"MultipleActiveResultSets"=>true
				);

				$this->connection = sqlsrv_connect($server_name,$connectioninfo);

			} else {

                $connectioninfo = array(

					'UID'=>$this->getConnectionParam('user'),
					'PWD'=>$this->getConnectionParam('password'),
					'Database'=>$this->getConnectionParam('name'),
					"MultipleActiveResultSets"=>true

					);


				$this->connection = sqlsrv_connect($server_name,$connectioninfo);
			}
			$db_name = $this->getConnectionParam('name');

			$this->database_selection = sqlsrv_query($this->connection,"USE $db_name"); //no select db in sql server


			sqlsrv_query($this->connection,"SET NAMES 'utf8'");



		}


		if (!$this->connection || !$this->database_selection) {

			$this->e->alert('Could not connect to database.');
			$this->connection_status = false;
			return false;
		} else {
                 //echo "connected to database<br/>";
			$this->connection_status = true;
			return true;
		}
	}


	/**
	 * Database Query
	 *
	 * @param 	string $sql
	 * @access 	public
	 *
	 */
	function query($sql) {
             //echo "Inside query <br/>";

      $sql = str_replace("false","0",$sql); //for sql server test
	   $sql = str_replace("true","1",$sql);


  		if ($this->connection_status == false):
  		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
  			$this->connect();
  		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
  		endif;

  		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
		$this->e->debug(sprintf('Query: %s', $sql));

		$this->result = '';
		$this->new_result = '';

		if (!empty($this->new_result)):
			sqlsrv_free_stmt($this->new_result);
		endif;
		owa_coreAPI::profile($this, __FUNCTION__, __LINE__, $sql);
		$result = @sqlsrv_query($this->connection,$sql);
		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
		// Log Errors only the first row
		$errors = sqlsrv_errors();
		if ($errors !== null):
			$this->e->notice(sprintf('A SQL error occured. Error: (%s) %s %s. Query: %s',
								$errors[0]['SQLSTATE'],
								$errors[0]['code'],
								htmlspecialchars($errors[0]['message']),
								$sql));
		endif;
		owa_coreAPI::profile($this, __FUNCTION__, __LINE__);
		$this->new_result = $result;

		return $this->new_result;

	}

	function close() {

		@sqlsrv_close($this->connection);
		return;

	}

	/**
	 * Fetch result set array
	 *
	 * @param 	string $sql
	 * @return 	array
	 * @access  public
	 */
	function get_results($sql) {

		if ($sql):
			$this->query($sql);
		endif;

		$num_rows = 0;

		while ( $row = @sqlsrv_fetch_array($this->new_result,SQLSRV_FETCH_ASSOC) ) {
			$this->result[$num_rows] = $row;
			$num_rows++;
		}

		if ($this->result):

			return $this->result;

		else:
			return null;
		endif;
	}

	/**
	 * Fetch Single Row
	 *
	 * @param string $sql
	 * @return array
	 */
	function get_row($sql) {

		$this->query($sql);

		//print_r($this->result);
		$row = @sqlsrv_fetch_array($this->new_result,SQLSRV_FETCH_ASSOC); //no single row equivalent in sql server if this does not work then return element [0] as row

		return $row;
	}

	/**
	 * Prepares and escapes string
	 *
	 * @param string $string
	 * @return string
	 */
	function prepare($string) {
             return($string); //no equivalent real escape in sqlserver if required need to build our own function

		if ($this->connection_status == false):
  			$this->connect();
  		endif;

		//return mysql_real_escape_string($string, $this->connection);


	}

	function getAffectedRows() {

		return sqlsrv_rows_affected($this->new_result);

	}
}

?>
