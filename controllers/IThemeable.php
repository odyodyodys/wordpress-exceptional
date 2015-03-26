<?php
/**
 * Interface that makes a something to support themes (Themeable), allowing its appearance to be controlled outside the plugin
 *
 * @author Odys
 */
interface Exceptional_IThemeable
{
    /**
     * Sets the template engine instance that will be used for rendering
     * @param Exceptional_ATemplate $template The template engine instance
     */
    function SetTemplateEngine(Exceptional_ATemplate $template);    
}
