<?php
/**
 * FilterOperator enum is how the terms in a filter are combined
 */
abstract class Exceptional_FilterOperator
{
    const _OR = ',';
    const _AND = '+';
    const _SINGLE = '';
    
    /**
     * Returns the css class of an operator value
     * @param string $operator An operator value
     */
    public static function GetClass($operator)
    {
        $desc = '';
        switch ($operator)
        {
            case self::_OR:
                $desc = 'or';
                break;
            case self::_AND:
                $desc = 'and';
                break;
            case self::_SINGLE:
                $desc = 'single';
                break;
        }
        return 'operator-'.$desc;
    }
}
?>