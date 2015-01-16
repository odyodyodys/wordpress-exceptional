<?php
/**
 * Class Exceptional_Filtering
 * 
 * Filtering controller
 */
class Exceptional_Filtering
{
    // fields and properties
    private static $_instance; // singleton instance
    private $_filters; // the filters of the page (the ones that matter to business logic)
    
    /**
     * @var Exceptional_FilteringTemplateEngine The template class to use for displaying the filters 
     */
    private static $_template;

    // Constructors
    public function __construct()
    {
        $this->filters = array();
    }

    // Methods
    public static function Instance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new Exceptional_Filtering();
        }
        return self::$_instance;
    }
    
    /**
     * Inits the Filtering to be ready to deliver data
     * Is called after construct and after data has been set (eg registered filters)
     */
    public function Init()
    {
        // check template engine is set
        if (!isset(self::$_template))
        {
            // TODO inform admin with an error
        }
        
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
            // set applied filters
            if (array_key_exists($filter->Slug, $appliedFilters))
            {
                $filter->IsApplied = true;
                // set applied terms in applied filters
                foreach ($appliedFilters[$filter->Slug] as $termSlug)
                {
                    $filter->SetTermApplied($termSlug, true);
                }
            }
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
     * Sets the template engine tha will be used for rendering
     * @param Exceptional_Theme_FilteringBasic $template
     */
    public function SetTemplateEngine($template)
    {
        self::$_template = $template;
    }

    /**
     * Registers a filter to be available. Must be called prior to Init
     * @param Exceptional_Filter $filter
     */
    public function RegisterFilter($filter)
    {
        $this->_filters[] = $filter;
    }

    /**
     * Takes a $taxonomy_slug slug and a taxonomy $term to filter by. It combines terms of the same taxonomy with a plus (+), so WordPress will use an AND operator to combine the terms.
     * @param Exceptional_Filter $filter a Taxonomy slug
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
        $existingQuery = '';
        foreach ($this->_filters as $tmpFilter)
        {
            if ($tmpFilter->Slug == $newFilter->Slug)
            {
                $tmpFilter = $newFilter;
            }
            
            $existingQuery .= $tmpFilter->GetFilterUrl();
        }

        if (!empty($existingQuery))
        {
            $filter_query = $existingQuery . $filter_query;
        }

        return trailingslashit(get_post_type_archive_link($wp_query->query['post_type']) . $filter_query);
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
            if (!empty($filterQuery))
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
     * @return Exceptional_Filter[] Applied filters
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
     * Displays the applied filters using the registered template engine
     */
    public function DisplayAppliedFilters()
    {
        self::$_template->DisplayAppliedFilters($this->GetAppliedFilters());
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
        
        self::$_template->DisplayFilteringPanel($publicFilters);
    }
}
?>