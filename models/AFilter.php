<?php
/**
 * Base class for Filters used by Filtering
 *
 * @author Odys
 */
abstract class Exceptional_AFilter
{
    /**
     * @var string the nice name of the filter
     */
    public $Name;
    
    /**
     *
     * @var string The slug of the taxonomy.
     */
    public $Slug;

    /**
     * @var AFilterTerm[] Terms of the filter 
     */
    public $Terms;
    
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
     *
     * @var Exceptional_FilterOperator Operator between filter terms
     */
    public $Operator;
    
    public function __construct($name, $slug, $operator, $isPublic = true )
    {
        $this->Name = $name;
        $this->Slug = $slug;
        $this->Operator = $operator;
        $this->IsPublic = $isPublic;
        $this->IsApplied = false;
    }
    
    /**
     * Clone the terms so they are safe to be manipulated.
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
     * Gets the css class of the filter
     */
    public function GetClass()
    {
        return 'filter filter-'.$this->Slug.' '.Exceptional_FilterOperator::GetClass($this->Operator);
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
     * Sets which terms are applied based on the applied terms
     * @param array $appliedFilters
     */
    public function InitAppliedTerms(array $appliedFilters)
    {
        // set applied filters
        if (array_key_exists($this->Slug, $appliedFilters))
        {
            $this->IsApplied = true;
            // set applied terms in applied filters
            foreach ($appliedFilters[$this->Slug] as $termSlug)
            {
                $this->SetTermApplied($termSlug, true);
            }
        }
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
                // we mustn't break when term is found. It is also needed to set all other terms as not applied
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
}
