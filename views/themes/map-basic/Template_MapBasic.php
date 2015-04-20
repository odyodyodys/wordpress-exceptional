<?php
/**
 * Sample template of how to use Exceptional_AMapsTemplate
 */
class Template_MapBasic extends Exceptional_AMapsTemplate
{
    public function DisplayMap($pois = array(), $routes = array())
    {?>
        <section class="exceptional-map"><?php
            foreach ($pois as $poi):?>
                <div class="poi" data-lat="<?php echo $poi->GetLat();?>" data-long="<?php echo $poi->GetLong();?>"
                     data-title="<?php echo $poi->GetTitle();?>" data-description="<?php echo $poi->GetDescription()?>"
                     data-icon="<?php echo $poi->GetIcon(); ?>"></div>
            <?php
            endforeach;?>
        </section><?php
    }
}
