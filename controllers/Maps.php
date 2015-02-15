<?php
/**
 * Map and Poi controller
 */
class Exceptional_Maps
{
    // FIELDS AND PROPERTIES
    private static $_instance; // singleton instance
    
    /**
     * @var Exceptional_MapTemplateEngine The template class to use for displaying the map
     */
    private static $_template;
    
    /**
     * The Pois of the map
     * @var Exceptional_APoi[] 
     */
    public $_pois;

    // CONSTRUCTORS
    private function __construct()
    {
        
    }
    
    // FUNCTIONS
    public static function Instance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new Exceptional_Maps();
        }
        return self::$_instance;
    }
        
    /**
     * Inits the class to be ready to deliver data
     * Is called after construct and after data has been set (eg pois added)
     */
    public function Init()
    {
        // check template engine is set
        if (!isset(self::$_template))
        {
            // TODO inform admin with an error
        }
    }
    
    /**
     * Sets the template engine that will be used for rendering
     * @param Exceptional_MapTemplateEngine $template
     */
    public function SetTemplateEngine(Exceptional_MapTemplateEngine $template)
    {
        self::$_template = $template;
    }
    
    /**
     * Adds a poi to the list of pois to display in the map
     * @param Exceptional_APoi $poi
     */
    public function AddPoi(Exceptional_APoi $poi)
    {
        if (!is_null($poi))
        {
            $this->_pois[] = $poi;
        }
    }
    
    /**
     * Displays the map using the registered Template Engine
     */
    public function DisplayMap()
    {
        self::$_template->DisplayMap($this->_pois);
    }
}
