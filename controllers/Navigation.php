<?php
/**
 * Controller for navigational stuff
 *
 * @author Odys
 */
class Exceptional_Navigation extends Exceptional_AController
{
    protected function __construct()
    {
        parent::__construct();
    }
    
    public function Init()
    {
        // enable menu relative links
        add_filter('nav_menu_link_attributes', array($this, 'FilterNavMenuLinkAttributes'));
    }
    
    public function FilterNavMenuLinkAttributes($atts)
    {
        // make links starting with / to be relative to site
        if (substr($atts['href'], 0, 1) === '/')
        {            
            // if the url only contains alphanumeric, dash, underscore, comma and plus
            // and doesn't contain ?, & and =
            // trailingslashit
            $pattern = "/^[a-z1-9_\-\/\+,]/"; // alphanumeric, dash, underscore, plus, comma
            if(preg_match($pattern, $atts['href'], $matches) && strpos($atts['href'], '?') === FALSE && strpos($atts['href'], '&') === FALSE && strpos($atts['href'], '=') === FALSE)
            {
                $atts['href'] = trailingslashit($atts['href']);
            }
            
            $atts['href'] = esc_url(site_url($atts['href']));
        }
        
        return $atts;
    }
}
