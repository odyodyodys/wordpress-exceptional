<?php
/**
 * A filter that has numeric values
 *
 * @author Odys
 */
class Exceptional_NumericFilter extends Exceptional_AFilter
{
    public $Min;
    public $Max;
    public $Step;

    /**
     * @param int $min Minimum value (inclusive)
     * @param int $max Maximum value (inclusive)
     * @param int $step Step
     */
    public function __construct($name, $slug, $min, $max, $step = 1,  $isPublic = true)
    {
        parent::__construct($name, $slug, Exceptional_FilterOperator::_SINGLE, $isPublic);
        
        $this->Min = $min;
        $this->Max = $max;
        $this->Step = $step;
        
        // only one term can exist and most likely it'll be the current value
        $this->Terms[] = new Exceptional_NumericFilterTerm();
    }
    
    /**
     * Init the term. If the filter is applied, the only term must have this slug and be applied also
     * @param array $appliedFilters
     */
    public function InitAppliedTerms(array $appliedFilters)
    {
        // set applied filters
        if (array_key_exists($this->Slug, $appliedFilters))
        {
            $this->IsApplied = true;
                        
            $this->Terms[0]->Slug = $appliedFilters[$this->Slug][0];
            $this->SetTermApplied($appliedFilters[$this->Slug][0], true);
        }
    }
    
}
