<?php
/**
 * Base class for filtering template engines
 */
abstract class Exceptional_FilteringTemplateEngine extends Exceptional_TemplateEngineBase
{
    /**
     * Dispays the filtering panel
     * @param Exceptional_AFilter[] $filters The filters to display
     */
    public abstract function DisplayFilteringPanel($filters);
    
    /**
     * Displays the applied filters (currently applied)
     * @param Exceptional_AFilter[] $filters The filters to display
     */
    public abstract function DisplayAppliedFilters($filters);
}