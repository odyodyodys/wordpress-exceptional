<?php
/**
 * Utility methods for strings
 *
 * @author Odys
 */
class Exceptional_String
{
    public static function StartsWith($haystack, $needle)
    {
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }
    
    public static function EndsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle))===$needle;
    }
}
