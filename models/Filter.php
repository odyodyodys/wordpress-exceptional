<?php
/**
 * Represents a filter. A wordpress Taxonomy that can filter results by appling filters to it.
 */
class Exceptional_Filter
{
    // FIELDS & PROPERTIES
    
    /**
     * @var string The taxonomy name
     */
    public $Taxonomy;
    
    /**
     *
     * @var string The slug of the taxonomy. Can be different from Taxonomy using rewrite rules (ugly taxonomy name can be pretty)
     */
    public $Slug;

    /**
     * @var string the nice name of the filter
     */
    public $Name;

    /**
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
     * If a filter is not active it doesn't appear when displaying filters, nor its terms are enumerated
     * @var bool Is public
     */
    public $IsPublic;

    /**
     * Constructor
     * @param string $taxonomy The taxonomy of the filter
     * @param string $name The nice name of the filter
     * @param array $terms Array of Exceptional_FilterTerm that are the terms of this filter
     * @param Exceptional_FilterOperator $operator The operator that is applied to the terms of this filter
     * @param bool $isPublic If a filter is public or not
     * @param string $slug The url representation of the taxonomy
     */
    public function __construct($taxonomy, $name, $operator = Exceptional_FilterOperator::_OR, $isPublic = true, $slug = '')
    {
        $this->Taxonomy = $taxonomy;
        $this->Name = $name;
        $this->Operator = $operator;
        $this->IsPublic = $isPublic;
        $this->IsApplied = false;
        $this->Slug = empty($slug) ? $taxonomy : $slug;
        
        // init my terms
        $this->Terms = array();
        $terms = get_terms($taxonomy, array('get' => 'all'));
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
        if (!empty($this->Terms))
        {
            foreach ($this->Terms as $term)
            {
                if ($term->Slug == $termSlug)
                {
                    $term->IsApplied = $isApplied;
                }
                else if ($this->Operator == Exceptional_FilterOperator::_SINGLE)
                {
                    // we mustn't break when term is found. It is also needed to set all other terms as not applied
                    $term->IsApplied = false;
                }
            }
        }
        else
        {
            // filter has no terms (its dummy or problematic). Create a dummy term to support it manually
            // Note that only one dummy term can exist
            $term = new Exceptional_FilterTerm();
            $term->Slug = $termSlug;
            $term->IsApplied = $isApplied;
            $this->Terms[] = $term;
        }
        
        // if a term is applied, the filter must be applied too
        if ($isApplied == true)
        {
            $this->IsApplied = true;
        }
    }
    
    /**
     * Gets the url part for this filter (Eg: taxonomy/term1,term2/)
     * It is used to retain the applied filter terms for other filters.
     * If the filter is not applied, an empty string is returned
     * eg: Filter color has red+blue and filter size wants to filter by size. It needs to retain the color terms
     * otherwise the size filter will only filter by size, without combining with the color filter.
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
    
    /**
     * Gets the css class of the filter
     */
    public function GetClass()
    {
        return 'filter filter-'.$this->Taxonomy.' '.Exceptional_FilterOperator::GetClass($this->Operator);
    }
}