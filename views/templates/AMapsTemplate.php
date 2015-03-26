<?php
/**
 * Base class to display a map with POIs
 */
abstract class Exceptional_AMapsTemplate extends Exceptional_ATemplate
{
    /**
     * @param Exceptional_APoi[] $pois The Pois to add to map
     */
    abstract public function DisplayMap($pois);
}
