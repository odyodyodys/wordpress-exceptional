<?php
/**
 * Utilities for arrays
 *
 * @author Odys
 */
class Exceptional_Array
{
    /**
     * Returns the first or all objects in an array that have property=valueExpr or valueExpr matches as a regex egainst an object's property
     * Eg. valueExpr = 'orange' would match an object having 'orange' as a value of any of its properties
     * valueExpr = '/regExExpression/' would match an object having a value matching that regex.
     * @param array $objects An array of objects
     * @param string $property The property to compare
     * @param mixed $valueExpr The value the property must have, or a regex expression to match the property value against
     * @param bool $all Return the first match or all
     * @return object|array|null The matching objects or empty array
     */
    public static function Having($objects, $property, $valueExpr, $all = false)
    {
        $isRegex = Exceptional_Input::IsRegex($valueExpr);
        
        $matching = array();

        foreach ($objects as $obj)
        {
            if (!$isRegex && $obj->{$property} === $valueExpr)
            {
                $matching[] = $obj;
            }
            elseif ($isRegex && preg_match($valueExpr, $obj->{$property}))
            {
                $matching[] = $obj;
            }
            
            // looking for one only and found it
            if (!$all && !empty($matching) != null)
            {
                break;
            }
        }
        
        // if only first match, return the object without an array
        $result = null;
        if (!$all && !empty($matching))
        {
            $result = $matching[0];
        }
        elseif(!empty($matching))
        {
            $result = $matching;
        }
        return $result;
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
