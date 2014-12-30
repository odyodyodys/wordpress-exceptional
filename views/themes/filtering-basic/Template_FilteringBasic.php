<?php
/**
 * Simple template for filters
 */
class Exceptional_Template_FilteringBasic extends Exceptional_FilteringTemplateEngine
{
    public function DisplayFilteringPanel($filters)
    {?>
        <ul class="filters"><?php                
            foreach ($filters as $filter):?>
                <li class="filter"><?php
                    echo $filter->Name;?>
                    <ul class="terms"><?php
                    foreach ($filter->Terms as $term):?>
                        <li class="term"><a href="<?php echo $term->Permalink; ?>"><?php
                            echo ($term->IsApplied?'-':'+'). $term->Name;?>
                        </a></li><?php
                    endforeach;?>
                    </ul>
                </li>
            <?php
            endforeach;?>
        </ul><?php
    }

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