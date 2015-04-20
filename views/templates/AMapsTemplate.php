<?php
/**
 * Base class to display a map with POIs
 */
abstract class Exceptional_AMapsTemplate extends Exceptional_ATemplate
{
    /**
     * Displays the map. Optionally adds markers and routes
     * @param Exceptional_Poi[] $pois The Pois to add to map
     * @param Exceptional_Route[] $routes The routes to add to map
     */
    abstract public function DisplayMap($pois = array(), $routes = array());
}
