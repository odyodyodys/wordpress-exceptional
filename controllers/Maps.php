<?php
/**
 * Map and Poi controller
 */
class Exceptional_Maps extends Exceptional_APresentationController
{
    // FIELDS AND PROPERTIES

    /**
     * The Pois of the map
     * @var Exceptional_APoi[] 
     */
    public $_pois;
    
    /**
     * Adds a poi to the list of pois to display in the map
     * @param Exceptional_APoi $poi
     */
    public function AddPoi(Exceptional_APoi $poi = NULL)
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
        $this->_template->DisplayMap($this->_pois);
    }
}
