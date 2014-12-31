<?php
/**
 * Base for template files
 */
abstract class Exceptional_TemplateEngineBase
{
    private $_styles;
    private $_scripts;

    // CONSTRUCTORS
    
    public function __construct()
    {
        $this->_scripts = array();
        $this->_styles = array();
        
        // tell the runtime that it has to init me
        $runtime = Exceptional_Runtime::Instance();
        $runtime->RegisterTemplateEngine($this);
    }

    // METHODS
    protected function RegisterScript($script)
    {
        $this->_scripts[] = $script;
    }
    
    protected function RegisterStyle($style)
    {
        $this->_styles[] = $style;
    }
    
    public function GetScripts()
    {
        return $this->_scripts;
    }
    
    public function GetStyles()
    {
        return $this->_styles;
    }
}