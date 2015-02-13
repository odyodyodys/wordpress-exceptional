<?php
/**
 * Utilities for arrays
 *
 * @author Odys
 */
class Exceptional_Array
{
    /**
     * Returns the first object in an array that has property=value
     * @param array $objects An array of objects
     * @param string $property The property to compare
     * @param mixed $value The value the property must have
     * @return mixed|null The object having property=value or null if not found
     */
    public static function Having($objects, $property, $value)
    {
        $target = NULL;
        foreach ($objects as $obj)
        {
            if ($obj->{$property} === $value)
            {
                $target = $obj;
                break;
            }            
        }
        return $target;
    }
    
    /**
     * Returns an array with the values of the $property of all objects
     * @param array $objects The array with objects
     * @param string $property The property to get values from
     * @return array Array with values of the property of all objects
     */
    public static function Values($objects, $property)
    {
        $values = array();
        foreach ($objects as $obj)
        {
            $values[] = $obj->{$property};
        }
                
        return $values;
    }
}
