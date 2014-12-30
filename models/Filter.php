<?php
/**
 * Represents a filter. A wordpress Taxonomy that can filter results by appling filters to it.
 */
class Exceptional_Filter
{
    // FIELDS & PROPERTIES
    
    public $Slug;
    /**
     * @var string the nice name of the filter
     */
    public $Name;
    /**
     *
     * @var Exceptional_FilterTerm[] Terms of the filter 
     */
    public $Terms;
    /**
     *
     * @var Exceptional_FilterOperator Operator between filter terms
     */
    public $Operator;
    /**
     * @var bool If the filter is currently applied
     */
    public $IsApplied;

    /**
     * Constructor
     * @param string $slug The slug of the filter
     * @param string $name The nice name of the filter
     * @param array $terms Array of Exceptional_FilterTerm that are the terms of this filter
     * @param Exceptional_FilterOperator $operator The operator that is applied to the terms of this filter
     */
    public function __construct($slug, $name, $operator = Exceptional_FilterOperator::_OR)
    {
        $this->Slug = $slug;
        $this->Name = $name;
        $this->Operator = $operator;
        $this->IsApplied = false;
        
        // init my terms
        $this->Terms = array();
        $terms = get_terms($slug);
        foreach ($terms as $term)
        {
            $this->Terms[] = new Exceptional_FilterTerm($term);
        }
    }
    
    // METHODS
    
    /**
     * Clone the filter. Also clones its terms and is safe to be manipulated.
     */    
    function __clone()
    {
        $newTerms = array();
        foreach ($this->Terms as $term)
        {
            $newTerms[] = clone $term;
        }
        $this->Terms = $newTerms;
    }
    
    /**
     * Returns a term of the filter based on its slug
     * @param string $termSlug
     */
    public function GetTermBySlug($termSlug)
    {
        $term = NULL;
        foreach ($this->Terms as $tmpTerm)
        {
            if ($tmpTerm->Slug == $termSlug)
            {
                $term = $tmpTerm;
                break;
            }
        }
        return $term;
    }
    
    /**
     * Returns the terms that are applied to the filter
     */
    public function GetAppliedTerms()
    {
        $applied = array();
        foreach ($this->Terms as $term)
        {
            if ($term->IsApplied)
            {
                $applied[] = $term;
            }
        }
        return $applied;
    }
    
    /**
     * Marks a term of the filter as applied
     * @param string $termSlug Slug of the term to set as applied
     */
    public function SetTermApplied($termSlug, $isApplied)
    {    
        foreach ($this->Terms as $term)
        {
            if ($term->Slug == $termSlug)
            {
                $term->IsApplied = $isApplied;
            }
            else if ($this->Operator == Exceptional_FilterOperator::_SINGLE)
            {
                $term->IsApplied = false;
            }
        }
        
        // if a term is applied, the filter must be applied too
        if ($isApplied == true)
        {
            $this->IsApplied = true;
        }
    }
    
    /**
     * Gets the url part for this filter (Eg: taxonomy/term1,term2/)
     * If the filter is not applied, an empty string is returned
     */
    public function GetFilterUrl()
    {
        $url = '';
        if ($this->IsApplied)
        {
            $appliedTerms = array();
            foreach ($this->Terms as $term)
            {
                if (!$term->IsApplied)
                {
                    continue;              
                }
                
                $appliedTerms[] = $term->Slug;
            }
            
            if (!empty($appliedTerms))
            {
                $url = $this->Slug .'/'. implode($this->Operator, $appliedTerms).'/';
            }
        }
        
        return $url;
    }
}