<?php
/**
 * Represents a filter. A wordpress Taxonomy that can filter results by appling filters to it.
 */
class Exceptional_Filter
{
    public $Slug;
    public $Name;
    public $Terms;
    public $Operator;
    // If the filter is currently applied
    public $IsApplied;


    /**
     * 
     * @param string $slug The slug of the filter
     * @param string $name The nice name of the filter
     * @param array $terms Array of Exceptional_FilterTerm that are the terms of this filter
     * @param Exceptional_FilterOperator $operator The operator that is applied to the terms of this filter
     */
    public function __construct($slug, $name, $terms = array(), $operator = Exceptional_FilterOperator::_OR)
    {
        $this->Slug = $slug;
        $this->Name = $name;
        $this->Terms = $terms;
        $this->Operator = $operator;
    }
}
?>