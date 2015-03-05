<?php
/**
 * Utilities for user input (numbers, strings, urls, etc)
 *
 * @author Odys
 */
class Exceptional_Input
{
    /**
     * Ensures a url is external
     * @param string $url
     */
    static function ExternalUrl($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url))
        {
            $url = "http://" . $url;
        }
        return $url;
    }
}
