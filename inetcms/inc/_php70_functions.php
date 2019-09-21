<?php

// php7.0 additional code
if (!function_exists('mysql_connect')) {

    function mysql_connect($dbhost, $dbuser, $dbpass) {
        return mysqli_connect($dbhost, $dbuser, $dbpass);
    }

    function mysql_select_db($dbname, $conn) {
        return mysqli_select_db($conn, $dbname);
    }

    function mysql_close($conn)
    {
        return mysqli_close($conn);
    }

    function mysql_error($conn)
    {
        return mysqli_error($conn);
    }

    function mysql_real_escape_string($value)
    {
        global $DB;

        return mysqli_real_escape_string($DB->getLink(), $value);
    }

    function mysql_query($query, $link = null)
    {
        global $DB;
        $conn = isset($link) ? $link : $DB->getLink();

        return mysqli_query($conn, $query);
    }

    function mysql_fetch_assoc($result)
    {
        $result = mysqli_fetch_assoc($result);

        return is_null($result) ? false : $result;
    }

    function mysql_insert_id($link = null)
    {
        global $DB;
        $conn = isset($link) ? $link : $DB->getLink();

        return mysqli_insert_id($conn);
    }

    function mysql_result($result, $number, $field=0)
    {
        mysqli_data_seek($result, $number);
        $row = mysqli_fetch_array($result);
        return $row[$field];
    }

    function mysql_free_result($result)
    {
        mysqli_free_result($result);
    }

    function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    function mysql_data_seek($result, $rowNum)
    {
        return mysqli_data_seek($result, $rowNum);
    }

    function sql_regcase($str)
    {
        $result = "";

        $chars = str_split($str);
        foreach($chars as $char) {
            if (preg_match("/[A-Za-z]/", $char)) {
                $result .= "[" . mb_strtoupper($char, 'UTF-8') . mb_strtolower($char, 'UTF-8') . "]";
            } else {
                $result .= $char;
            }
        }

        return $result;
    }
}
