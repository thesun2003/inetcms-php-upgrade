<?php

class SiteLocale
{
    static public function get($text) {
        global $LNG;

        return !empty($LNG[$text]) ? $LNG[$text] : $text;
    }
}