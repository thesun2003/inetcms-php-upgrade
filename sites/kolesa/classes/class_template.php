<?
using::add_class('fieldmanager');

/** Placeholder to use when cutting long strings. */
define('HELLIP', '&hellip;');

class Template{
    var $_htmlfile;
    var $_cssFile;
    var $_title;
    var $form;
    var $content;
    var $isSecure;
    var $overridenHeaders = array();

    function Template($fileName){
        $this->_htmlfile = $fileName;
        $this->_title = DEFAULT_TITLE;
        $this->form = new FieldManager();
        $this->content  = array();
        $this->isSecure = false;
    }

    function setTitle($title){
        $this->_title = $title;
    }

    /**
     * Sets CSS file name to use.
     *
     * @param string CSS file name.
     */
    function setCSSfile($fileName)
    {
        $this->_cssFile = $fileName;
    }

    function run(){
        // Won't use SSL for the pages not required it
        if (!$this->isSecure && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
            exit;
        }
        $this->display($this->_htmlfile);
    }

    /** @param  string */
    function display($src){
        global $LNG; // Added to allow use language constants inside of templates
    require("html/$src.php");
    }

    function add($content, $name){
        if (isset($this->content[$name])) {
            trigger_error("Template::add: content[$name] already exists");
        }
        $this->content[$name] = $content;
    }

    function showContent($name){
        if (!$this->hasContent($name)) {
            trigger_error("Template::showContent: content[$name] is empty");
            return;
        }
        if (is_object($this->content[$name])) {
            $this->content[$name]->run();
        } else {
            $this->display($this->content[$name]);
        }
    }

    /**
     * @param string ID of content block.
     * @return boolean Whether specified block exists.
     */
    function hasContent($name){
        return !empty($this->content[$name]);
    }

    /**
     * Outputs box with several choices, selects one of them.
     *
     * @param string Name of field in the form.
     * @param boolean Whether we should add third field to include both choices.
     */
    function outputChoiceBox($fieldName, $ternary=false, $disabled=false, $ext = ""){
        $disabled=($disabled)?(" disabled"):("");
        echo "<select class=\"choice_box\" name=\"$fieldName\" id=\"$fieldName\"$ext$disabled>";
        $values = array(0 => 'No', 1 => 'Yes');
        if ($ternary) {
            $values[2] = 'Either';
        }
        foreach ($values as $key => $value) {
            echo '<option value="' . $key . '"';
            if ($this->form->get($fieldName) == $key) {
                echo ' selected="selected"';
            }
            echo '>' . $value . '</option>';
        }
        echo '</select>';
    }

    /**
     * Outputs error message, if any.
     *
     * @param string Name of the field in form.
     */
    function outputErrorBox($fieldName){
        if ($this->form->hasError($fieldName)) {
            echo '<div class="error">';
            echo $this->form->getError($fieldName);
            echo '</div>';
        }
    }

    /**
     * Cuts too long unescaped string at word boundary to specified width and adds "..." if necessary.
     *
     * @param string Text to cut.
     * @param int Maximum desired string length.
     * @return string HTML encoded cut string.
     * @static
     */
    function limitString($str, $width=75, $htmlencode = true){
        $cut = substr($str, 0, $width);
        $isCut = strlen($cut) < strlen($str);
        if($htmlencode) {
            $cut = htmlspecialchars($cut);
        }
        if ($isCut) {
            $cut .= HELLIP;
        }
        return $cut;
    }

    /**
     * Outputs sort header with predefined style.
     *
     * @param string Sort order for this header.
     * @param string Header label.
     */
    function outputSortHeader($sortOrder, $label){
        $oldGet = $_GET;
        $_GET['so'] = $sortOrder;

        $overridenHeaders = self::getOverridenHeaders();

        // override headers sorting for certain headers
        if(!empty($overridenHeaders[$sortOrder])) {
            if(!empty($_GET['so']) && !empty($_GET['cur_so'])) {
                $curSo = explode(" ", $_GET['cur_so']);
                if($oldGet['so'] == $sortOrder) {
                    $_GET['cur_so'] = $this->sortOrder . ($this->sortDir == ' asc' ? ' desc' : ' asc');
                } else {
                    $_GET['cur_so'] = $sortOrder . ' ' . $overridenHeaders[$sortOrder];
                }
            } else {
                $_GET['cur_so'] = $this->sortOrder . ' ' . $overridenHeaders[$sortOrder];
            }
        } else {
            $_GET['cur_so'] = $this->sortOrder . $this->sortDir;
        }

        echo '<span class="sort-sign">';
        echo ' &nbsp;';
        echo '</span>';
        echo '<a class="sort-header" href="' . Template::makeGenericUrl() . '">' . htmlspecialchars($label) . '</a>';
        if ($sortOrder == $this->sortOrder) {
            echo '<span class="sort-sign">';
            echo $this->sortDir == ' asc' ? '&#x25B2;' : '&#x25BC;';
            echo '</span>';
        }
        $_GET = $oldGet;
    }

    /**
     * Outputs sort header with predefined style.
     *
     * @param string Sort order for this header.
     * @param string Header label.
     */
    function outputJsSortHeader($element, $sortOrder, $label){
        $oldGet = $_GET;
        $_GET['so'] = $sortOrder;
        $_GET['cur_so'] = $this->sortOrder . $this->sortDir;
        echo '<span class="sort-sign">';
        echo ' &nbsp;';
        echo '</span>';
        echo '<a class="sort-header" href="javascript:doLoadURL(\'' . $element . '\', ' . '\'' . Template::makeGenericUrl() . '\')">' . htmlspecialchars($label) . '</a>';
        if ($sortOrder == $this->sortOrder) {
            echo '<span class="sort-sign">';
            echo $this->sortDir == ' asc' ? '&#x25B2;' : '&#x25BC;';
            echo '</span>';
        }
        else {
            echo '<span class="sort-sign">';
            echo ' &nbsp;&nbsp;&nbsp;';
            echo '</span>';
        }
        $_GET = $oldGet;
    }

    /**
     * Detects default sort order and direction.
     *
     * @param string Default sort order to fall back.
     * @param boolean Default sort direction is ascending.
     * @param mixed Array with possible sort orders or false to skip this check.
     * @return array Sort order and direction.
     * @static
     */
    function detectSortOrder($defaultSortOrder, $defaultAsc=true, $sortOrders=false){
        $sortOrder = $defaultSortOrder;
        $sortDir = ' asc';
        if (!empty($_GET['so'])) {
            $sortOrder = $_GET['so'];
        } elseif (!$defaultAsc) {
            $sortDir = ' desc';
        }
        if (isset($_GET['cur_so']) && $_GET['cur_so'] == $sortOrder . $sortDir) {
            $sortDir = ' desc';
        }
        if (is_array($sortOrders) && !in_array($sortOrder, $sortOrders)) {
            $sortOrder = $defaultSortOrder;
        }
        return array(mysql_real_escape_string($sortOrder), mysql_real_escape_string($sortDir));
    }

    /**
     * Escapes string so it can be used as JavaScript string expression.
     *
     * @param string
     * @param boolean Optional parameter to detect whether we should add HTML line breaks.
     * @return string
     */
    function escapeJSText($string, $htmlLineBreaks = false, $escapeApostrophes = false){
        if ($htmlLineBreaks) {
            $string = nl2br($string);
        }
        // we must replace '\' before we do the other replacements because otherwise, the '\' will be
        // double escaped:
        $string = str_replace('\\', '\\\\', $string);

        $from = array("\r", "\n", '"', '/');
        $to = array('\\r', '\\n', '\"', '\/');
        if ($escapeApostrophes) {
            $from[] = "'";
            $to[] = "\\'";
        }
        return str_replace($from, $to, $string);
    }

    /**
     * Makes up current script URL including all (possibly modified) GET variables.
     *
     * @param boolean Whether we should use HTML encode for making URL.
     * @return string
     * @static
     */
    function makeGenericUrl($htmlencode=true){
        $q  = array();
        foreach ($_GET as $k=>$v) {
            $q[] = urlencode($k)."=".urlencode($v);
        }
        $separator = $htmlencode ? '&amp;' : '&';
        $q  = join ($separator, $q);
        if ($q) {
            $q = '?' . $q;
        }
        return $_SERVER['PHP_SELF'] . $q;
    }

    /*
    *  Format text saved using <textarea>
    *  Return text with HTML if HTML exists and nl2br() otherwise
    *
    */
    function formatTextarea($text) {
        $html = html_entity_decode($text);
        if(strpos($html, '<') !== false && strpos($html, '>') !== false) {
           return $html;
        }
        else {
            return nl2br($text);
        }
    }


    function saveNote($text) {
        $_SESSION['notification'] = $text;
    }

    function getNote() {
        if(!empty($_SESSION['notification'])) {
            $text = $_SESSION['notification'];
            unset($_SESSION['notification']);
            return $text;
        }
        return '';
    }

    function urlReplace($change, $delete = false) {
        $url = '';
        foreach($_GET as $param => $value) {
            if(is_array($delete) && in_array($param, $delete)) {
                continue;
            }
            $value = isset($change[$param])?$change[$param]:$value;
            $url = !empty($url)?("$url&$param=$value"):("?$param=$value");
        }
        foreach($change as $param => $value) {
            if(isset($_GET[$param])) {
                continue;
            }
            $url = !empty($url)?("$url&$param=$value"):("?$param=$value");
        }
        $url = $_SERVER['PHP_SELF'] . $url;
        return $url;
    }

    function getLocation() {
        return isset($this->content['body'])?$this->content['body']:"";
    }

    function mkdate($dayAdd = 0, $monthAdd = 0, $yearAdd = 0) {
        global $DB;

        return $DB->mkdate($dayAdd, $monthAdd, $yearAdd);
    }

    /**
     * Means that current page should be accessed through SSL only.
     */
    function requireSecureMode(){
        if (!defined('REQUIRE_HTTPS_ACCESS') || !REQUIRE_HTTPS_ACCESS) {
            return;
        }
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
            if (defined('HTTPS_HOST')) {
                $host = HTTPS_HOST;
            } else {
                $host = $_SERVER['HTTP_HOST'];
            }
            $uri = $host . $_SERVER['REQUEST_URI'];
            header("Location: https://$uri");
            exit;
        }
        $this->isSecure = true;
    }
}
?>
