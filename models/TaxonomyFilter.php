<?php
/**
 * Represents a filter. A wordpress Taxonomy that can filter results by appling filters to it.
 */
class Exceptional_TaxonomyFilter extends Exceptional_AFilter
{
    // FIELDS & PROPERTIES
    
    /**
     * @var string The taxonomy name. Can be different from Slug using rewrite rules (ugly taxonomy name can be pretty slug)
     */
    public $Taxonomy;
    
    /**
     * Constructor
     * @param string $taxonomy The taxonomy of the filter
     * @param string $name The nice name of the filter
     * @param Exceptional_FilterOperator $operator The operator that is applied to the terms of this filter
     * @param bool $isPublic If a filter is public or not
     * @param string $slug The url representation of the taxonomy
     */
    public function __construct($taxonomy, $name, $operator = Exceptional_FilterOperator::_OR, $isPublic = true, $slug = '')
    {
        parent::__construct($name, empty($slug) ? $taxonomy : $slug, $operator, $isPublic);
        
        $this->Taxonomy = $taxonomy;
                
        // init my terms
        $this->Terms = array();
        $terms = get_terms($taxonomy, array('get' => 'all'));
        foreach ($terms as $term)
        {
            $this->Terms[] = new Exceptional_TaxonomyFilterTerm($term);
        }
    }
    
    public function InitAppliedTerms(array $appliedFilters)
    {
        // set applied filters
        if (array_key_exists($this->Taxonomy, $appliedFilters))
        {
            $this->IsApplied = true;
            // set applied terms in applied filters
            foreach ($appliedFilters[$this->Taxonomy] as $termSlug)
            {
                $this->SetTermApplied($termSlug, true);
            }
        }
    }
    
    /**
     * Gets the css class of the filter
     */
    public function GetClass()
    {
        return 'filter filter-'.$this->Taxonomy.' '.Exceptional_FilterOperator::GetClass($this->Operator);
    }
}