<?php
/**
 * Is the term of a filter (Exceptional_TaxonomyFilter)
 */
class Exceptional_TaxonomyFilterTerm extends Exceptional_AFilterTerm
{
    // The wordpress native term object
    private $_nativeTerm;
    public $Id;
    public $Name;    
    public $Description;

    public function __construct($nativeTerm = NULL)
    {
        parent::__construct();
        
        if (!is_null($nativeTerm))
        {
            $this->_nativeTerm = $nativeTerm;
            $this->Id = $nativeTerm->term_id;
            $this->Name = $nativeTerm->name;
            $this->Slug = $nativeTerm->slug;
            $this->Description = $nativeTerm->description;
        }
    }
}
?>