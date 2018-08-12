<?
using::add_class('template');

define('LINKS_NUM', 10);
define('PAGE_SIZE', 10);
define('NO_LIMIT', '0, 18446744073709551615');

class DBPager
{
    var $select;
    var $className;
    var $tableName;
    var $subquery;
    var $countQuery;
    var $order;
    var $limit;
    var $origLimit;
    var $page;
    var $fetchedItem;
    var $pageSize;
    var $query;
    var $_base;

    /**
     * @param string
     * @param string SQL subquery (e.g. "WHERE where GROUP BY gr").
     * @param mixed String containg sort order or empty value when no order is required.
     */
    function __construct($className, $tableName, $subquery, $order, $limit = false) {
        $this->className    = $className;
        $this->tableName    = $tableName;
        $this->select   = "$this->tableName.*";
        $this->subquery = $subquery;
        $this->countQuery   = $this->subquery;
        $this->order    = $order;
        if ($limit == false) {
            $this->limit = NO_LIMIT;
        } else {
            $this->limit    = $limit;
        }
        $this->origLimit = trim($limit);
        $this->page = 1;
        $this->fetchedItem = false;
        $this->pageSize = PAGE_SIZE;
        $this->query = false;
    }

    function setBase($base) {
        $this->_base = $base;
    }

    function addSelect($name){
        $this->select   = "$this->select,$name";
    }

    function setSelect($name){
        $this->select = $name;
    }

    function setPage(){
        if (isset($_GET['p']) && is_numeric($_GET['p'])) {
            $this->page = $_GET['p'];
        }
        $this->limit    = $this->_calcLimit();
    }

    function setPageSize($newPageSize) {
        $this->pageSize = $newPageSize;
    }

    function _calcLimit(){
        return max(($this->page - 1) * $this->pageSize, 0) . ',' . $this->pageSize;
    }

    function _formQuery($limit){
        $sql = "SELECT $this->select FROM $this->tableName $this->subquery";
        if ($this->order) {
            $sql .= " ORDER BY $this->order";
        }
        if ($limit != NO_LIMIT) {
            $sql .= " LIMIT $limit";
        }
        return $sql;
    }

    /** @return mixed Object or false. */
    function &next(){
        if ($this->hasNext()) {
            if (!class_exists($this->className)) {
                require_once("classes/".strtolower($this->className).".php");
            }
            $res    = new $this->className($this->fetchedItem);
            $this->fetchedItem = false;
        } else {
            $res    = false;
        }
        return $res;
    }

    /** @return boolean */
    function hasNext(){
        global $DB;
        if (!$this->query) {
            if ($this->limit) {
                $limit  = $this->limit;
            } else {
                $limit  = $this->_calcLimit();
            }
            $this->query = $DB->query($this->_formQuery($limit));
        }
        if ($this->query && !$this->fetchedItem) {
            $this->fetchedItem = $this->query->fetchRow();
        }
        return $this->fetchedItem != false;
    }

    function getSize() {
        global $DB;
        if (!isset($this->_size)) {
            $res = $DB->getAll("SELECT COUNT(*) AS amount FROM $this->tableName $this->countQuery");
            if (stristr($this->countQuery, ' group by ') === false) {
                $this->_size = $res[0]['amount'];
            } else {
                $this->_size = count($res);
            }
            /* Count(*) always return number of records without taking into account LIMIT.  We have to work around it. */
            if (preg_match('/^\d+$/', $this->origLimit)) {
                $this->_size = min($this->_size, $this->origLimit);
            }
        }
        return $this->_size;
    }

    function setSize($size) {
        $this->_size = $size;
    }

    function setCountQuery($query){
        $this->countQuery   = $query;
    }

    function &getAt($pos){
        global $DB;
        if ($pos < 0 || $pos >= $this->getSize()) {
            $res = false;
            return $res;
        }
        require_once("classes/".strtolower($this->className).".php");
        $res = new $this->className($DB->getRow($this->_formQuery("$pos, 1")));
        return $res;
    }

    function run($do_not_print_total = false, $customUrl = false){
        global $LNG;
        $total  = ceil($this->getSize() / $this->pageSize);
        if ($this->page >= $total) {
            $after = 0;
        } else {
            $after = $total - $this->page;
        $wing   = ceil(LINKS_NUM / 2);
        if ($after > $wing)
            $after = $wing;
        }
        $before = LINKS_NUM - $after;
        if ($before >= $this->page) {
            $before = $this->page - 1;
        }
        print '<div class="page-list">';
        if ($total > 1) {
            if ($this->page > 1) {
                print "<a class=\"orange\" href=\"".$this->makeUrl($this->page - 1, $customUrl)."\">{$LNG['prev_page']}</a> &bull; ";
            }
            for ($i = $this->page - $before; $i <= $this->page + $after; $i++) {
                if ($i == $this->page) {
                    print " $i ";
                } else {
                    print "<a  class=\"orange\" href=\"".$this->makeUrl($i, $customUrl)."\">$i</a>";
                }
                if ($i < $total && $i < $this->page + $after) print " &bull; ";
            }
            if ($this->page < $total) {
                print " &bull; <a class=\"orange\" href=\"".$this->makeUrl($this->page + 1, $customUrl)."\">{$LNG['next_page']}</a>";
            }
        }
        if(!$do_not_print_total) {
            print " <i>{$LNG['pager_total']} " . number_format($this->getSize()) . "</i>";
        }
        print "</div>";
    }

    /**
    * Generates URL to use as pager link.
    *
    * @param mixed URL to use instead of default or false.
    *              If URL is set it should contain "%s" placeholder to insert page number.
    * @return string
    */
    function makeUrl($page, $customUrl = false){
        if($customUrl) {
            return sprintf($customUrl, Template::urlReplace(array('p' => $page)));
        } else {
            $oldPage = isset($_GET['p']) ? $_GET['p'] : '';
            $_GET['p']  = $page;
            $res = Template::makeGenericUrl();
            $_GET['p'] = $oldPage;
            return $res;
        }
    }
}
?>
