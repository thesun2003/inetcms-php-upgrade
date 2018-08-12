<?php

if (!defined('DB_AUTOQUERY_INSERT')) {
    define('DB_AUTOQUERY_INSERT', 1);
}
if (!defined('DB_AUTOQUERY_UPDATE')) {
    define('DB_AUTOQUERY_UPDATE', 2);
}

/**
 * Light-weight MySQL database manager.
 *
 * PEAR::DB interface is used with some changes.
 */
class DB
{
    /**
     * Current DB connection.
     *
     * @access private
     * @var resource
     */
    var $_link;

    /**
     * Error handling function name.
     *
     * @access private
     * @var string
     */
    var $_errorHandler;

    var $_debugMode = false;

    /**
     * Connects to the database.
     *
     * @return  object  Newly created DB object or error object on failure.
     */
    function &connect($dbhost, $dbname, $dbuser, $dbpass){
        global $LNG;
        $link = @mysql_connect($dbhost, $dbuser, $dbpass);
        $res = null;
        if ($link === false) {
            $res = new DB_Error($LNG['db_err_bad_server'], $link);
        } else {
            if (@mysql_select_db($dbname, $link)) {
                $res = new DB($link);
            } else {
                $res = new DB_Error($LNG['db_err_bad_db'], $link);
            }
        }
        return $res;
    }

    function __construct($link=false){
        $this->_link = &$link;
    }

    /**
     * Sets error handler function.
     *
     * This function should accept the only parameter - DB_Error instance.
     *
     * @param string Handler function name.
     */
    function setErrorHandling($functionName){
        $this->_errorHandler = $functionName;
    }

    /**
     * Quotes values and escaped special chars in it.
     *
     * @param string
     * @return string
     */
    function quote($value){
        return "'" . mysql_real_escape_string($value) . "'";
    }

    /**
     * Gets first result column of specified query.
     *
     * @return mixed String or NULL if no result found.
     * @param string
     */
    function &getOne($query){
        $res = $this->query($query);
        return $res->fetchOne();
    }

    /**
     * Gets all result data in array.
     *
     * @param string
     * @return array Each row will contain associative array of record fields.
     */
    function &getAll($query){
        $result = array();
        $res = $this->query($query);
        while (($row = $res->fetchRow()) !== false) {
            $result[] = $row;
        }
        return $result;
    }

    function &getPairs($query){
        $data = $this->getAll($query);
        $result = array();
        foreach ($data as $row){
            $id = array_shift($row);
            $result[$id] = array_shift($row);
        }
        return $result;
    }

    /**
     * Gets first row of result data.
     *
     * @param string
     * @return mixed Array of data or empty value.
     */
    function getRow($query){
        $res = $this->query($query);
        $row = $res->fetchRow();
        return $row?$row:array();
    }

    /**
     * Gets first column of the result data.
     * Note: if result will contain several columns what will be returned is uknown for sure. Use one column in the query.
     *
     * @param string
     * @return array
     */
    function getCol($query){
        $data = $this->getAll($query);
        $result = array();
        foreach ($data as $row){
            $result[] = array_shift($row);
        }
        return $result;
    }

    /**
     * Gets result of specified query if any.
     *
     * @param string
     * @return object DB_Result instance
     */
    function &query($query){
        global $LNG;
        if($this->_debugMode) {
            $startTime = getmicrotime();
        }
        @mysql_query("SET names UTF8");
        $result = mysql_query($query, $this->_link);
        if($this->_debugMode && session_id()) {
            $endTime = round((getmicrotime() - $startTime) * 1000, 2);
            print " [" . $query . "] : ";
            if($endTime > 5000) {
                print "<span style=\"color: #f00\">";
            } else {
                print "<span>";
            }
            if($endTime > 1000) {
                print "<b>";
            }

            print " [";
            print $endTime;
            print " ms]</b>";
            print "</span>";
            print "<br />";
        }
        if ($result === false) {
            $this->_throwError(sprintf($LNG['db_err_bad_query'], $query, mysql_error()), $query);
        }
        $res = new DB_Result($result);
        return $res;
    }

    /**
     * @access protected
     * @param string Error message.
     * @param string SQL sentence caused the error.
     * @return void
     */
    function _throwError($msg, $sql){
        if ($this->_errorHandler) {
            call_user_func($this->_errorHandler, new DB_Error($msg, $this->_link, $sql));
        } else {
            trigger_error($msg, E_USER_ERROR);
        }
    }

    /**
     * @return object DB_Result
     * @param string Table name.
     * @param array Associative array of values to work with.
     * @param int Whether insert or update, one of predefined constants.
     * @param mixed Optional WHERE query part.
     */
    function &autoExecute($tableName, $values, $mode, $where=false){
        $query = $mode == DB_AUTOQUERY_INSERT ? 'INSERT' : 'UPDATE';
        $query .= " $tableName";
        if ($mode == DB_AUTOQUERY_INSERT) {
            $columns = '';
            foreach (array_keys($values) as $col) {
                if ($columns) {
                    $columns .= ', ';
                }
                $columns .= "`$col`";
            }
            $query .= " (" . $columns . ")";
            $escapedValues = array();
            foreach ($values as $value) {
                $escapedValues[] = $this->quote($value);
            }
            $query .= " VALUES(" . join(', ', $escapedValues) . ")";
        } else {
            $query .= " SET ";
            $subquery = '';
            foreach ($values as $column => $value) {
                if ($subquery) {
                    $subquery .= ', ';
                }
                $subquery .= "`$column`=" . $this->quote($value);
            }
            $query .= $subquery;
            if ($where) {
                $query .= " WHERE $where";
            }
        }
        return $this->query($query);
    }

    /**
     * @return int ID for AUTO_INCREMENT column by SELECT LAST_INSERT_ID() query
     */
    function insertedId(){
        return mysql_insert_id($this->_link);
    }

    /**
     * Returns DB connection resource, which can be used in native SQL functions.
     *
     * @return resource
     */
    function getLink(){
        return $this->_link;
    }

    /**
     * Returns datetime in MySQL format Y-m-d 00:00:00
     * If $dayAdd is integer it supplied to date
     * If $dayAdd is string date parameter sets to this value
     * You also can set $dayAdd to "last" in order to use last day of the given month
     *
     * Examples:
     * mkdate(-1)                  will return date to 1 day before current (yesterday 00:00:00)
     * mkdate(0, -1)               will return date to 1 month before current
     * mkdate("1")                 will return date at the beginning of current month
     * mkdate("1", "1", "1")       will return 01-01-01 00:00:00 (January 01, 2001)
     * mkdate("last", "2", "2007") will return 2007-02-28 00:00:00 (February's last day)
     *
     */
    function mkdate($dayAdd = 0, $monthAdd = 0, $yearAdd = 0) {
        if($dayAdd === "last") {
         $dayAdd = date("t", mktime(0,0,0, is_int($monthAdd)?(date("m") + $monthAdd):(intval($monthAdd)), 1, is_int($yearAdd)?(date("Y") + $yearAdd):(intval($yearAdd))));
        }
        return date("Y-m-d 00:00:00", mktime(0,0,0, is_int($monthAdd)?(date("m") + $monthAdd):(intval($monthAdd)), is_int($dayAdd)?(date("d") + $dayAdd):(intval($dayAdd)), is_int($yearAdd)?(date("Y") + $yearAdd):(intval($yearAdd))));
    }

    function changedate($date, $dayAdd = 0) {
        if(!$date) {
            return false;
        }
        return strftime(MYSQL_TIME, strtotime($date) + 86400 * $dayAdd);
    }

    function setdebugMode($mode=true) {
        $this->_debugMode = $mode;
    }
}

/**
 * DB error class.
 */
class DB_Error{
    /**
     * @public
     * @var string
     */
    var $message = '';

    /**
     * @public
     * @var string
     */
    var $userinfo = '';

    /**
     * @public
     * @var string
     */
    var $sql = '';

    /**
     * @param string Error message.
     * @param mixed DB connection resource of false if no connection was created yet.
     * @param string SQL sentence caused the error.
     */
    function __construct($message, $link, $sql=''){
        $this->message = $message;
        $this->userinfo = mysql_error($link);
        $this->sql = $sql;
    }
}

/**
 * DB result class.
 */
class DB_Result{
    var $_result;

    /**
     * @param mixed Result of executed query.
     */
    function __construct($result){
        $this->_result = $result;
    }

    /**
     * Gets first result column of specified query.
     *
     * @return mixed
     */
    function &fetchOne(){
        if (is_resource($this->_result) || is_object($this->_result)) {
            $res = @mysql_result($this->_result, 0);
            $this->_free();
        } else {
            $res = NULL;
        }
        return $res;
    }

    /**
     * Frees all object resources.
     *
     * @access protected
     */
    function _free(){
        mysql_free_result($this->_result);
    }

    /**
     * Fetchs next data row.
     *
     * @return mixed Next data row or false.
     */
    function &fetchRow(){
        if (is_resource($this->_result) || is_object($this->_result)) {
            $res = mysql_fetch_assoc($this->_result);
            if ($res === false) {
                $this->_free();
            }
        } else {
            $res = false;
            return $res;
        }
        return $res;
    }

    /**
     * Returns number of the rows in query result.
     *
     * @return int
     */
    function getRowsCount() {
        return mysql_num_rows($this->_result);
    }

    /**
     * Moves internal pointer in the query results to specified position.
     * Next call of fetchRow() will return element from this position.
     *
     * @param int Row number, counting from zero.
     * @return boolean Whether seeking was successful.
     */
    function seekData($rowNum) {
        return mysql_data_seek($this->_result, $rowNum);
    }
}
