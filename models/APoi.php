<?php
/**
 * Abstract class that describes a POI (Point of interest) that is to be placed on a map
 *
 */
abstract class Exceptional_APoi
{
    public abstract function GetId();
    public abstract function GetIcon();
    public abstract function GetLat();
    public abstract function GetLong();
    public abstract function GetTitle();
    public abstract function GetDescription();
}
