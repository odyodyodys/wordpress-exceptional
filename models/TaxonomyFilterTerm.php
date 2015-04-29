<?php
/**
 * Is the term of a filter (Exceptional_TaxonomyFilter)
 */
class Exceptional_TaxonomyFilterTerm extends Exceptional_AFilterTerm
{
    // The wordpress native term object
    public $NativeTerm;
    public $Id;
    public $Name;    
    public $Description;

    public function __construct($nativeTerm = NULL)
    {
        parent::__construct();
        
        if (!is_null($nativeTerm))
        {
            $this->NativeTerm = $nativeTerm;
            $this->Id = $nativeTerm->term_id;
            $this->Name = $nativeTerm->name;
            $this->Slug = $nativeTerm->slug;
            $this->Description = $nativeTerm->description;
        }
    }
}
?>