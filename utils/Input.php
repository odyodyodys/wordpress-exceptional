<?php
/**
 * Utilities for user input (numbers, strings, urls, etc)
 *
 * @author Odys
 */
class Exceptional_Input
{
    /**
     * Fast and unreliable way to tell if an expression might be a regex.
     * Only checks if it has the Perl-compatible syntax, it starts and ends with a /
     * @param string $expr The expression to check
     */
    public static function IsRegex($expr)
    {
        return Exceptional_String::StartsWith($expr, '/') && Exceptional_String::EndsWith($expr, '/');
    }

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
