<?php
/**
 * Is the term of a filter (Exceptional_Filter)
 */
class Exceptional_FilterTerm
{
    // The wordpress native term object
    private $_nativeTerm;
    // The permalink to filter using this term. If the term is already applied to a filter, the link is a filter without the term.
    public $Permalink;
    // If the term is applied (filter is applied with this term and/or others)
    public $IsApplied;

    public function __construct($nativeTerm)
    {
        $this->_nativeTerm = $nativeTerm;
    }

    public function GetName()
    {
        return $this->_nativeTerm->name;
    }
    public function GetSlug()
    {
        return $this->_nativeTerm->slug;
    }
    
}
?>