<?php
/**
 * Base class to display a map with POIs
 */
abstract class Exceptional_MapTemplateEngine extends Exceptional_TemplateEngineBase
{
    /**
     * @param Exceptional_Poi[] $pois The Pois to add to map
     */
    abstract public function DisplayMap($pois);
}
