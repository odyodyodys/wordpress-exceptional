<?php
/**
 * Class Exceptional_Filtering
 * 
 * Filtering controller
 */
class Exceptional_Filtering extends Exceptional_APresentationController
{
    // FILTERS & PROPERTIES
    
    /**
     * @var Exceptional_AFilter[] the filters of the page (the ones that matter to business logic)
     */
    private $_filters;
        
    /**
     * @var array[] Query vars that are needed to be retained (eg. sorting )
     */
    private $_retainedVars;
    
    /**
     * Similar to the category/tag base in Settings->Permalink. It is the base where all urls are applied
     * eg: for 'topics' the filters would be example.com/topics/filter1/term1/filter2/term2,term3
     * This must be set before calling Init()
     * @var string The base url
     */
    public $BaseUrl;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->_filters = array();
        $this->_retainedVars = array();
    }

    // Methods
    
    /**
     * Inits the Filtering to be ready to deliver data
     * Is called after construct and after data has been set (eg registered filters)
     */
    public function Init()
    {
        parent::Init();
        
        // init filters
        $this->InitFilters();
    }
    
    /**
     * Initializes filters
     */
    private function InitFilters()
    {
        // get data to set to filters
        $appliedFilters = $this->GetAppliedFilterSlugs();
        foreach ($this->_filters as $filter)
        {
            $filter->InitAppliedTerms($appliedFilters);
        }
        
        foreach ($this->_filters as $filter)
        {
            foreach ($filter->Terms as $term)
            {
                $term->Permalink = $this->GetFilterPermalink($filter, $term->Slug);
            }
        }
    }

    /**
     * Registers a filter to be available. Must be called prior to Init
     * @param Exceptional_AFilter $filter
     */
    public function RegisterFilter(Exceptional_AFilter $filter)
    {
        $this->_filters[] = $filter;
    }
    
    /**
     * @param string $var The url variable to be retained in filter permalinks
     */
    public function RegisterRetainedQueryVar($var)
    {
        if (!isset($this->_retainedVars))
        {
            $this->_retainedVars = array();
        }
        
        $this->_retainedVars[] = $var;        
    }

    /**
     * Takes a $taxonomy_slug slug and a taxonomy $term to filter by. It combines terms of the same taxonomy with a plus (+), so WordPress will use an AND operator to combine the terms.
     * @param Exceptional_AFilter|string $filter a Filter
     * @param string $term a taxonomy term slug
     * @return string Permalink for the filtered/unfiltered content based on this term
     */
    private function GetFilterPermalink($filter, $term)
    {        
        global $wp_query;

        // Clone filter, set term as applied
        $newFilter = clone $filter;
        $curTerm = $newFilter->GetTermBySlug($term);
        $newFilter->SetTermApplied($term, !$curTerm->IsApplied);
        
        // combine urls of all filters
        $filter_query = '/';
        foreach ($this->_filters as $tmpFilter)
        {
            if ($tmpFilter->Slug == $newFilter->Slug)
            {
                $tmpFilter = $newFilter;
            }
            
            $filter_query .= $tmpFilter->GetFilterUrl();
        }
        
        return trailingslashit( $this->GetUrlBase().$filter_query).$this->GetRetainedVars();
    }
    
    /**
     * Returns the base url, without a trailing slash. The one we can start building the filters at.
     */
    private function GetUrlBase()
    {
        if (is_post_type_archive())
        {
            global $wp_query;
            $base = get_post_type_archive_link($wp_query->query['post_type']);
        }
        else
        {
            $base = home_url($this->BaseUrl);
        }
        
        return untrailingslashit($base);
    }
    
    /**
     * Returns the url that has no filters applied. Can be the "Remove all filters" link
     */
    public function GetNoFiltersUrl()
    {
        return trailingslashit($this->GetUrlBase()).$this->GetRetainedVars();
    }

    /**
     * Returns the custom query vars that need to be retained in the urs
     */
    private function GetRetainedVars()
    {
        // append retained variables (eg  ?var1=value1&var2=value2
        global $wp_query;
        $varSets = array();
        foreach ($this->_retainedVars as $var)
        {
            if (array_key_exists($var, $_GET))
            {
                $value = $_GET[$var];
                $varSets[] = $var.'='.$value;
            }
        }
        
        // bulk query vars
        $retainedQueryVars = '';
        if (!empty($varSets))
        {
            $retainedQueryVars = '?'.implode('&', $varSets);
        }
        
        return $retainedQueryVars;
    }

        /**
     * Generates all the rewrite rules for a given post type.
     *
     * The rewrite rules allow a post type to be filtered by all possible combinations & permutations
     * of taxonomies that apply to the specified post type and additional query_vars specified with
     * the $query_vars parameter.
     *
     * Must be called from a function hooked to the 'generate_rewrite_rules' action so that the global
     * $wp_rewrite->preg_index function returns the correct value.
     *
     * @param string|object $post_type The post type for which you wish to create the rewrite rules
     * @param array $query_vars optional Non-taxonomy query vars you wish to create rewrite rules for. 
     * Rules will be created to capture any single string for the query_var, that is, a rule of the form '/query_var/(.+)/'
     *
     */
    public function GeneratePostTypeRewriteRules($post_type, $query_vars = array())
    {
        global $wp_rewrite;

        if (!is_object($post_type))
        {
            $post_type = get_post_type_object($post_type);
        }

        $new_rewrite_rules = array();

        $taxonomies = get_object_taxonomies($post_type->name, 'objects');

        // Add taxonomy filters to the query vars array
        foreach ($taxonomies as $taxonomy)
        {
            $query_vars[] = $taxonomy->query_var;
        }

        // Loop over all the possible combinations of the query vars
        for ($i = 1; $i <= count($query_vars); $i++)
        {

            $new_rewrite_rule = $post_type->rewrite['slug'] . '/';
            $new_query_string = 'index.php?post_type=' . $post_type->name;

            // Prepend the rewrites & queries
            for ($n = 1; $n <= $i; $n++)
            {
                $new_rewrite_rule .= '(' . implode('|', $query_vars) . ')/(.+?)/';
                $new_query_string .= '&' . $wp_rewrite->preg_index($n * 2 - 1) . '=' . $wp_rewrite->preg_index($n * 2);
            }

            // Allow paging of filtered post type - WordPress expects 'page' in the URL but uses 'paged' in the query string so paging doesn't fit into our regex
            $new_paged_rewrite_rule = $new_rewrite_rule . 'page/([0-9]{1,})/';
            $new_paged_query_string = $new_query_string . '&paged=' . $wp_rewrite->preg_index($i * 2 + 1);

            // Make the trailing backslash optional
            $new_paged_rewrite_rule = $new_paged_rewrite_rule . '?$';
            $new_rewrite_rule = $new_rewrite_rule . '?$';

            // Add the new rewrites
            $new_rewrite_rules = array($new_paged_rewrite_rule => $new_paged_query_string, $new_rewrite_rule => $new_query_string) + $new_rewrite_rules;
        }
        
        return $new_rewrite_rules;
    }

    /**
     * Returns an array with current applied filters (filterName => array(activeFilterTerms))
     */
    private function GetAppliedFilterSlugs()
    {
        // we want to find applied terms in filters (taxonomies and active terms)
        // get if from query vars 
        global $wp_query;
        $queryTerms = array_keys($wp_query->query);

        // array[taxonomy => array[terms]]
        $filters = array();
        foreach ($queryTerms as $curFilter)
        {
            $filterQuery = get_query_var($curFilter);
            if (!empty($filterQuery) && is_string($filterQuery))
            {
                $termSlugs = preg_split('/(,|\+)/', $filterQuery);                                
                $filters[$curFilter] = $termSlugs;
            }
        }
        
        return $filters;
    }
    
    /**
     * Returns all filters
     */
    public function GetFilters()
    {
        return $this->_filters;
    }

    /**
     * Returns the applied filters
     * @return Exceptional_AFilter[] Applied filters
     */
    public function GetAppliedFilters()
    {
        $applied = array();
        foreach ($this->_filters as $filter)
        {
            if ($filter->IsApplied && $filter->IsPublic)
            {
                $applied[] = $filter;                
            }
        }
        return $applied;
    }
    
    /**
     * Returns true if at least one filter is applied
     */
    public function HaveAppliedFilters()
    {
        $have = false;
        foreach ($this->_filters as $filter)
        {
            if ($filter->IsApplied && $filter->IsPublic)
            {
                $have = true;
                break;
            }
        }
        return $have;
    }

    /**
     * Displays the applied filters using the registered template engine
     */
    public function DisplayAppliedFilters()
    {
        $this->_template->DisplayAppliedFilters($this->GetAppliedFilters());
    }
    
    /**
     * Displays the filtering panel using the registered template engine
     */
    public function DisplayFilteringPanel()
    {
        // create a list with all public filters
        $publicFilters = array();
        foreach ($this->_filters as $filter)
        {
            if ($filter->IsPublic)
            {
                $publicFilters[] = $filter;
            }
        }
        
        $this->_template->DisplayFilteringPanel($publicFilters);
    }
}
?>