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
    
    /**
     * Concatenates words in a human friendly form.
     * Example: array(bananas, apples, oranges) outputs 'bananas, apples and oranges'
     * http://stackoverflow.com/a/8586179/245495
     * @param array $words The words to implode
     * @param string $separator The separator between items. Only the character without spaces
     * @param string $lastSeparator The separator between the last two terms. Only character without spaces
     * @return string The result
     */
    static function LexicalImplode($words, $separator = ',', $lastSeparator = 'and')
    {
        $last  = array_slice($words, -1);
        $first = join("$separator ", array_slice($words, 0, -1));
        $both  = array_filter(array_merge(array($first), $last));
        return join(" $lastSeparator ", $both);
    }
}
