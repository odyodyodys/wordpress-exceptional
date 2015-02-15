<?php
/**
 * Base for AFilter's Terms
 *
 * @author Odys
 */
abstract class Exceptional_AFilterTerm
{
    // The permalink to filter using this term. If the term is already applied to a filter, the link is a filter without the term.
    public $Permalink;
    /**
     * @var bool If the term is applied (filter is applied with this term and/or others)
     */
    public $IsApplied;
    
    public $Slug;
    
    public function __construct()
    {
        $this->IsApplied = false;
    }
    
    /**
     * The css class for this term
     */
    public function GetClass()
    {
        return 'term term-'.$this->Slug.($this->IsApplied? ' applied': '');
    }    
}
