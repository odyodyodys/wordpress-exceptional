<?php
/**
 * Base for template files
 */
abstract class Exceptional_TemplateEngineBase
{
    private $_styles;
    private $_scripts;
    protected $_myPath;

    // CONSTRUCTORS
    
    public function __construct()
    {
        $this->_scripts = array();
        $this->_styles = array();
        
        // set myPath
        $reflector = new ReflectionClass(get_class($this));
        $classPath = str_replace('\\', '/', dirname($reflector->getFileName()));
        $contentDir = str_replace('\\', '/', WP_CONTENT_DIR);
        $this->_myPath = trailingslashit(str_replace( $contentDir, WP_CONTENT_URL, $classPath));

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