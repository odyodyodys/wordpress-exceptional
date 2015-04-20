<?php
/**
 * Map, Poi and Route controller
 */
class Exceptional_Maps extends Exceptional_APresentationController
{
    // FIELDS AND PROPERTIES

    /**
     * The Pois of the map
     * @var Exceptional_Poi[] 
     */
    private $_pois;
    
    /**
     * The routes of the map
     * @var Exceptional_Route[]
     */
    private $_routes;
    
    protected function __construct()
    {
        parent::__construct();
        
        $this->_pois = [];
        $this->_routes = [];
    }

    /**
     * Adds a poi to the list of pois to display in the map
     * @param Exceptional_Poi $poi
     */
    public function AddPoi(Exceptional_Poi $poi = NULL)
    {
        if (!is_null($poi))
        {
            $this->_pois[] = $poi;
        }
    }
    
    public function AddRoute(Exceptional_Route $route = NULL)
    {
        if (!is_null($route))
        {
            $this->_routes[] = $route;
        }        
    }

    /**
     * Displays the map using the registered Template Engine
     */
    public function DisplayMap()
    {
        $this->_template->DisplayMap($this->_pois, $this->_routes);
    }
}
