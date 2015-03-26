<?php
/**
 * Simple template for filters
 */
class Exceptional_Template_FilteringBasic extends Exceptional_AFilteringTemplate
{
    public function __construct()
    {
        parent::__construct();
        
        $myPath = plugin_dir_url(__FILE__);
        $this->RegisterStyle($myPath.'styles.css');
        $this->RegisterScript($myPath.'scripts.js');
    }
    
    /**
     * Displays the filtering panel
     * @param Exceptional_AFilter[] $filters The available filters
     */
    public function DisplayFilteringPanel($filters)
    {?>
        <ul class="filters"><?php                
            foreach ($filters as $filter):?>
                <li class="<?php echo $filter->GetClass() ?>">
                    <h3><?php echo $filter->Name;?></h3>
                    <ul class="terms"><?php
                    foreach ($filter->Terms as $term):?>
                        <li class="<?php echo $term->GetClass(); ?>"><a href="<?php echo $term->Permalink; ?>" rel="nofollow"><?php
                            echo $term->Name;?>
                        </a></li><?php
                    endforeach;?>
                    </ul>
                </li>
            <?php
            endforeach;?>
        </ul><?php
    }

    /**
     * Displays the currently applied filters
     * @param Exceptional_AFilter[] $appliedFilters The filters currently applied
     */
    public function DisplayAppliedFilters($appliedFilters)
    {
        if (!empty($appliedFilters)):?>
            <div class="archive-meta">
                <ul class="filters"><?php
                    foreach ($appliedFilters as $filter):?>
                        <li class="filter"><?php echo $filter->Name; ?>
                            <dl class="terms"><?php
                            foreach ($filter->GetAppliedTerms() as $term):?>
                                <dt class="term"><?php 
                                    echo $term->Name; 
                                    // display infolink only when there is a term description
                                    if(!empty($term->Description)):?>
                                        <button class="infolink btn btn-link btn-lg nounderline" type="button">
                                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                                        </button><?php
                                    endif;?>
                                </dt>
                                <dd class="description hide"><?php echo $term->Description;?></dd>
                                <?php 
                            endforeach;?>
                            </dl>
                        </li><?php
                    endforeach;
                    ?>
                </ul>
            </div><?php
        endif;
    }

}