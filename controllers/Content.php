<?php
/**
 * Class Exceptional_Content
 * 
 * Content related stuff
 */
class Exceptional_Content
{
    // properties
    private static $_instance; // singleton instance
    
    // Methods
    public static function Instance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new Exceptional_Content();
        }
        return self::$_instance;
    }
    
    public function Init()
    {
        
    }
    
    // Takes a $taxonomy_slug slug and a taxonomy $term to filter by. It combines terms of the same taxonomy with a plus (+), so WordPress will use an AND operator to combine the terms.
    public function GetFilterPermalink($taxonomy_slug, $term)
    {
        global $wp_query;

        // If there is already a filter running for this taxonomy
        if (isset($wp_query->query_vars[$taxonomy_slug]))
        {
            // And the term for this URL is not already being used to filter the taxonomy
            if (strpos($wp_query->query_vars[$taxonomy_slug], $term) === false)
            {
                // Append the term
                $filter_query = $taxonomy_slug . '/' . $wp_query->query_vars[$taxonomy_slug] . '+' . $term;
            }
            else
            {
                // Otherwise, remove the term
                if ($wp_query->query_vars[$taxonomy_slug] == $term)
                {
                    $filter_query = '';
                }
                else
                {
                    $filter = str_replace($term, '', $wp_query->query_vars[$taxonomy_slug]);
                    // Remove any residual + symbols left behind
                    $filter = str_replace('++', '+', $filter);
                    $filter = preg_replace('/(^\+|\+$)/', '', $filter);
                    $filter_query = $taxonomy_slug . '/' . $filter;
                }
            }
        }
        else
        {
            $filter_query = $taxonomy_slug . '/' . $term;
        }

        // Maintain the filters for other taxonomies
        if (isset($wp_query->tax_query))
        {

            foreach ($wp_query->tax_query->queries as $query)
            {

                $tax = get_taxonomy($query['taxonomy']);

                // Have we already handled this taxonomy?
                if ($tax->query_var == $taxonomy_slug)
                {
                    continue;
                }

                // Make sure taxonomy hasn't already been added to query string
                if (strpos($existing_query, $tax->query_var) === false)
                {
                    $existing_query .= $tax->query_var . '/' . $wp_query->query_vars[$tax->query_var] . '/';
                }
            }
        }

        if (isset($existing_query))
        {
            $filter_query = $existing_query . $filter_query;
        }

        return trailingslashit(get_post_type_archive_link('eg_event') . $filter_query);
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

}
?>