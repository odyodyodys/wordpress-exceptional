<?php
/**
 * Class ExceptionalSeo
 * 
 * The main controller of the plugin
 * 
 * 
 */
class Exceptional_Seo
{
    // Variables
    private static $_instance; // singleton instance

    // Methods
    public static function Instance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new Exceptional_Seo();
        }
        return self::$_instance;
    }
    
    public function Init()
    {
        add_filter('sanitize_title', array($this, 'TitleSanitizerFilter'));
    }
    
    /**
     * Sanitizes the title by replacing text with the latin corresponding
     * Hook for the sanitize_title filter action
     */
    public function TitleSanitizerFilter($str)
    {        
        // this gets called regularly, we only really want it to take place when user is changing the name (slug) of a post
        // so basically only when in admin area
        if (!is_admin())
        {
            return $str;
        }
        
        // get blog charset (once)
        static $charset = null;
        if (is_null($charset))
        {
            $charset = get_option('blog_charset');
        }

        // string separator
        $sep = '-';

        // character translation table  
        $chars = array
            (
            // greek consonants
            'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ζ' => 'z', 'θ' => 'th', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'ks', 'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps',
            // greek vowels
            'α' => 'a', 'ά' => 'a', 'ε' => 'e', 'έ' => 'e', 'η' => 'i', 'ή' => 'i', 'ι' => 'i', 'ί' => 'i', 'ϊ' => 'i', 'ΐ' => 'i', 'ου' => 'ou', 'ού' => 'ou', 'ο' => 'o', 'ό' => 'o', 'υ' => 'y', 'ύ' => 'y', 'ϋ' => 'y', 'ΰ' => 'y', 'ς' => 's', 'ω' => 'o', 'ώ' => 'o'
        );

        // lowercase and try to preserve charset
        if (!function_exists('mb_strtolower'))
        {
            $str = strtolower($str);
        }
        else
        {
            $str = mb_strtolower($str, $charset);
        }

        // strip tags and fix encoded chars
        $str = trim(strip_tags(urldecode($str)));

        // convert disallowed chars into allowed
        foreach ($chars as $no => $yes)
        {
            $str = str_replace($no, $yes, $str);
        }

        // replaces non allowed chars into spaces
        $str = preg_replace('/[^a-z0-9' . implode('', $chars) . ']/ui', ' ', $str);

        // delete remaining spaces
        $str = preg_replace('/\s+/', $sep, str_replace('+', ' ', $str));

        // replaces spaces with default separator
        $str = preg_replace("/(^$sep|$sep$)/", '', str_replace(' ', $sep, $str));

        return $str;
    }

}

?>