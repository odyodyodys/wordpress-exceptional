<?php /*
  Plugin Name:  Exceptional
  Plugin URI:   http://odysonline.gr
  Description:  Adds the Exceptional modules
  Version:      0.0.2
  Author:       Odys
  Author URI:   http://odysonline.gr
*/

class Exceptional_Runtime
{
    // singleton instance
    private static $_instance;

    // All the paths the autoloader should search to find the requested class
    private static $PLUGIN_PATHS;
    
    /**
     * The base path of the plugin
     */
    private static $BASE_PATH;

    /**
     * @var Exceptional_FilteringTemplateEngine[] Templates that are used
     */
    private $templateEngines;

    // CONSTRUCTORS
    private function __construct()
    {
        $this->templateEngines = array();
        
        // Setup autoloader. When using a class name, php searches for the requested class inside the following paths
        // All follow the naming convension Exceptional_ClassName and the class file is named ClassName (without the prefix)
        // Eg: When the Exceptional_Seo class is used, it searches for Seo.php inside all the PluginPaths.
        
        // Paths with classes sorted as most used to less used to optimize search speed
        self::$PLUGIN_PATHS = array('controllers/', 'models/', 'utils/', 'views/templates/', '/');
        self::$BASE_PATH = plugin_dir_path(__FILE__);
        // register autoloader
        spl_autoload_register(array(__CLASS__, 'Autoloader'));
        
        // init self as delayed as possible
        add_filter('get_header', array($this, 'Init'), 11);
        
        // init modules
        $seo = Exceptional_Seo::Instance();
        $seo->Init();
    }
    
    // METHODS
    
    /**
     * Singleton instance
     */
    public static function Instance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new Exceptional_Runtime();
        }
        return self::$_instance;
    }
    
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

    public static function Autoloader($class)
    {
        // Handle request starting with the plugin prefix only
        if (strpos($class, 'Exceptional_') !== 0)
        {
            return;
        }

        // try to find it in the registered paths        
        // remove the prefix to get the class filename
        $classFile = str_replace('Exceptional_', '', $class). '.php';
        foreach (self::$PLUGIN_PATHS as $path)
        {
            // eg Exceptional_Seo -> /controllers/Seo.php
            if (@include self::$BASE_PATH.$path.$classFile )
            {
                // found and included. job is done
                break;
            }
        }
    }

    /**
     * @param Exceptional_TemplateEngineBase $template A template engine that is used
     */
    public function RegisterTemplateEngine(Exceptional_TemplateEngineBase $template)
    {
        $this->templateEngines[] = $template;
    }
}

// Init the runtime
Exceptional_Runtime::Instance();

?>