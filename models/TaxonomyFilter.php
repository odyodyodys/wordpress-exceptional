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
        $nativeTerms = get_categories(array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'hierarchical' => 1 ));
        
        // Index all terms by parent id, for easy lookup later
        $termsByParent = array();
        foreach ($nativeTerms as $nativeTerm)
        {
            $parentId = $nativeTerm->category_parent;
            if (!array_key_exists($parentId, $termsByParent))
            {
                $termsByParent[$parentId] = array();
            }
            $termsByParent[$parentId][] = new Exceptional_TaxonomyFilterTerm($nativeTerm);
        }

        $this->TermChildrenHierarchical($termsByParent, $termsByParent[0], $this->Terms);
    }
    
    /**
     * Recursively build the term tree based on terms by parrent
     * @param array $termsByParent term objects (wp) by parent id
     * @param array $childrenOfParent The part of the $termsByParent we are interested in
     * @param array $childBag Resulting array containing all children of the same parent
     */
    public function TermChildrenHierarchical(&$termsByParent, &$childrenOfParent, &$childBag)
    {
        foreach ($childrenOfParent as $childTerm)
        {
            $childId = $childTerm->Id;
            if (array_key_exists($childId, $termsByParent))
            {
                $this->TermChildrenHierarchical($termsByParent, $termsByParent[$childId], $childTerm->Children);
            }
            $childBag[$childId] = $childTerm;
        }
    }

    public function InitAppliedTerms(array $appliedFilterSlugs)
    {
        // is about this filter;
        if (array_key_exists($this->Taxonomy, $appliedFilterSlugs))
        {
            $this->IsApplied = true;
            
            // set checked terms in filter
            foreach ($appliedFilterSlugs[$this->Taxonomy] as $termSlug)
            {
                $this->SetTermState($termSlug, Exceptional_CheckState::Checked);
            }
        }
    }
    
    /**
     * Gets the css class of the filter
     */
    public function GetClass()
    {
        return 'filter filter-type-taxonomy filter-'.$this->Taxonomy.' '.Exceptional_FilterOperator::GetClass($this->Operator);
    }
}