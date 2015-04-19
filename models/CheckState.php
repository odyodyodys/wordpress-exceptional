<?php
/**
 * Enum. The available check states of a tri-state thing
 *
 * @author Odys
 */
abstract class Exceptional_CheckState
{    
    const Checked = 1;
    const Intermediate = 2;
    const Unchecked = 3;
    
    /**
     * Returns the css class of a checked state
     * @param string $state a CheckedState
     */
    public static function GetClass($state)
    {
        $desc = '';
        switch ($state)
        {
            case self::Checked:
                $desc = 'checked';
                break;
            case self::Intermediate:
                $desc = 'intermediate';
                break;
            case self::Unchecked:
                $desc = 'unchecked';
                break;
        }
        return 'checked-state-'.$desc;
    }
}