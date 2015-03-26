<?php

/**
 * An Exceptional controller base capable of displaying stuff
 *
 * @author Odys
 */
abstract class Exceptional_APresentationController extends Exceptional_AController implements Exceptional_IThemeable
{
    /**
     * @var Exceptional_ATemplate The template engine to use for displaying the themeable
     */
    protected $_template;
    
    /**
     * Inits the class to be ready to deliver data
     * Is called after construct and after data has been set
     */
    public function Init()
    {
        // check template engine is set
        if (!isset($this->_template))
        {
            // TODO inform admin with an error
            var_dump('Error');
        }
    }
    
    /**
     * Sets the template engine tha will be used for rendering
     * @param Exceptional_ATemplate $template
     */
    public function SetTemplateEngine(Exceptional_ATemplate $template)
    {
        $this->_template = $template;
    }
}
