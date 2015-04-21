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
     * Returns the terms of the filter that are checked
     */
    public function GetCheckedTerms()
    {
        $checked = array();
        foreach ($this->Terms as $term)
        {
            $checked = array_merge($checked, $term->GetChecked());
        }
        return $checked;
    }
    
    /**
     * Returns a term of the filter based on its slug
     * @param string $termSlug
     * @return Exceptional_AFilterTerm|NULL The target term or null if not found
     */
    public function GetTermBySlug($termSlug)
    {
        return $this->GetTermBySlugRecursive($termSlug, $this->Terms);
    }
    
    /**
     * Traverses a term tree to find the one with the requested slug
     * @param string $termSlug The slug of the requested term
     * @param array $terms The terms to search into
     * @return Exceptional_AFilterTerm The requested term or null
     */
    private function GetTermBySlugRecursive($termSlug, $terms)
    {
        $term = NULL;
        foreach ($terms as $tmpTerm)
        {
            if ($tmpTerm->Slug === $termSlug)
            {
                $term = $tmpTerm;
            }
            else
            {
                $term = $this->GetTermBySlugRecursive($termSlug, $tmpTerm->Children);
            }
            
            if ($term !== NULL)
            {
                break;
            }
        }
        return $term;
    }
    
    /**
     * Sets which terms are checked based on the applied term slugs
     * @param array $appliedFilterSlugs Associative array with key the filter slug and value array of term slugs
     */
    public function InitAppliedTerms(array $appliedFilterSlugs)
    {
        // is about this filter;
        if (array_key_exists($this->Slug, $appliedFilterSlugs))
        {
            $this->IsApplied = true;
            
            // set checked terms in filter
            foreach ($appliedFilterSlugs[$this->Slug] as $termSlug)
            {
                $this->SetTermState($termSlug, Exceptional_CheckState::Checked);
            }
        }
    }

    /**
     * Changes the state of a filter term
     * @param string $termSlug Slug of the term to set as applied
     * @param Exceptional_CheckState $state The state to set
     */
    public function SetTermState($termSlug, $state)
    {
        foreach ($this->Terms as $term)
        {
            if ($term->SetCheckedState($state, $termSlug))
            {
                // the value in the term has been applied, the filter is now applied
                $this->IsApplied = true;                
            }
            else if ($this->Operator === Exceptional_FilterOperator::_SINGLE)
            {
                // if on single filter operator we mustn't break when term is found. Its also needed to set all other terms as not applied
                $term->SetCheckedState(Exceptional_CheckState::Unchecked);
            }
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
            $checkedTerms = array();
            foreach ($this->Terms as $term)
            {
                $checkedTerms = array_merge($checkedTerms, $term->GetChecked());
            }

            if (!empty($checkedTerms))
            {
                $url = $this->Slug .'/'. implode($this->Operator, Exceptional_Array::Instance()->Values($checkedTerms, 'Slug')).'/';
            }
        }
        
        return $url;
    }
}
