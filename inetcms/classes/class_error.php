<?php

define('ERROR_PATTERN', "<!--error --><div class='div_message' align='center'><div align='center' class='error_pattern'>%s</div></div><!--error -->");
define('USER_ERROR_PATTERN', "<!--error --><div class='div_message' align='center'><div align='center' class='user_error_pattern'>%s</div></div><!--error -->");

using::add_class('sitelocale');

class ErrorMessage
{
    protected $pattern = ERROR_PATTERN;

    function showText($text) {
        print sprintf($this->pattern, $text);
    }

    function show($text = '') {
        $this->showText(SiteLocale::get($text));
    }
}

class UserError extends ErrorMessage
{
    protected $pattern = USER_ERROR_PATTERN;
}
