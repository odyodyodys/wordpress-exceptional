<?php /*
  Plugin Name:  Exceptional
  Plugin URI:   http://odysonline.gr
  Description:  Adds the Exceptional modules
  Version:      0.34.1
  Author:       Odys
  Author URI:   http://odysonline.gr
*/

// required as it's inherited. Cannot use autoloader for this because it's not set up yet.
require_once 'controllers/AController.php';

class Exceptional_Runtime extends Exceptional_AController
{
    // All the paths the autoloader should search to find the requested class
    private $_pluginPaths;
    
    /**
     * The base path of the plugin
     */
    private $_basePath;

    /**
     * @var Exceptional_ATemplate[] Templates that are used
     */
    private $templateEngines;

    // CONSTRUCTORS
    protected function __construct()
    {
        parent::__construct();
        
        $this->templateEngines = array();
        
        // Setup autoloader. When using a class name, php searches for the requested class inside the following paths
        // All follow the naming convension Exceptional_ClassName and the class file is named ClassName (without the prefix)
        // Eg: When the Exceptional_Seo class is used, it searches for Seo.php inside all the PluginPaths.
        
        // Paths with classes sorted as most used to less used to optimize search speed
        $this->_pluginPaths = array('controllers/', 'models/', 'utils/', 'views/templates/', '/');
        $this->_basePath = plugin_dir_path(__FILE__);
        // register autoloader
        spl_autoload_register(array($this, 'Autoloader'));
        
        // init self as delayed as possible
        add_filter('get_header', array($this, 'Init'), 11);
        
        // init modules
        Exceptional_Seo::Instance()->Init();
    }
    
    // METHODS
    
    public function Init()
    {
        // init template engines
        foreach ($this->templateEngines as $template)
        {
            // init scripts and styles of the template
            // as handle it gets ClassName1, ClassName2 etc. So no matter how many use the same template engine, the
            // resources will get included once only.
            $i = 1;
            $templateClass = get_class($template);
            foreach ($template->GetScripts() as $scriptData)
            {
                wp_enqueue_script($templateClass.$i++, $scriptData[0], $scriptData[1], $scriptData[2], $scriptData[3]);
            }
            $i = 1;
            foreach ($template->GetStyles() as $styleData)
            {
                wp_enqueue_style($templateClass.$i++, $styleData[0], $styleData[1], $styleData[2], $styleData[3]);
            }
        }
    }

    public function Autoloader($class)
    {        
        // Handle request starting with the plugin prefix only
        if (strpos($class, 'Exceptional_') !== 0)
        {
            return;
        }

        // try to find it in the registered paths        
        // remove the prefix to get the class filename
        $classFile = str_replace('Exceptional_', '', $class). '.php';
        foreach ($this->_pluginPaths as $path)
        {
            // eg Exceptional_Seo -> /controllers/Seo.php
            if (@include $this->_basePath.$path.$classFile )
            {
                // found and included. job is done
                break;
            }
        }
    }

    /**
     * @param Exceptional_ATemplate $template A template engine that is used
     */
    public function RegisterTemplateEngine(Exceptional_ATemplate $template)
    {
        $this->templateEngines[] = $template;
    }
}

// Init the runtime
Exceptional_Runtime::Instance();

?>