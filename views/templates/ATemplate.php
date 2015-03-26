<?php
/**
 * Base for template files
 */
abstract class Exceptional_ATemplate
{
    private $_styles;
    private $_scripts;
    
    /**
     * The url base of the class that implements this. Use it to register scripts and style relative to the template engine
     * @var string Url base
     */
    protected $_myUri;

    /**
     * Constructor
     */    
    public function __construct()
    {
        $this->_scripts = array();
        $this->_styles = array();
        
        // set myPath
        $reflector = new ReflectionClass(get_class($this));
        $classPath = str_replace('\\', '/', dirname($reflector->getFileName()));
        $contentDir = str_replace('\\', '/', WP_CONTENT_DIR);
        $this->_myUri = trailingslashit(str_replace( $contentDir, WP_CONTENT_URL, $classPath));

        // tell the runtime that it has to init me
        $runtime = Exceptional_Runtime::Instance();
        $runtime->RegisterTemplateEngine($this);
    }

    // METHODS
    protected function RegisterScript($script, $depedencies = array(), $version = false, $in_footer = false)
    {
        $this->_scripts[] = array($script, $depedencies, $version, $in_footer);
    }
    
    protected function RegisterStyle($style, $depedencies = array(), $version = false, $media = 'all')
    {
        $this->_styles[] = array($style, $depedencies, $version, $media);
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