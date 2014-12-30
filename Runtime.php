<?php /*
  Plugin Name:  Exceptional
  Plugin URI:   http://odysonline.gr
  Description:  Adds the Exceptional modules
  Version:      0.0.1
  Author:       Odys
  Author URI:   http://odysonline.gr
*/

class Exceptional_Runtime
{
    // All the paths the autoloader should search to find the requested class
    private static $PLUGIN_PATHS;

    public static function Init()
    {
        // Setup autoloader. When using a class name, php searches for the requested class inside the following paths
        // All follow the naming convension Exceptional_ClassName and the class file is named ClassName (without the prefix)
        // Eg: When the Exceptional_Seo class is used, it searches for Seo.php inside all the PluginPaths.
        
        // Paths with classes
        self::$PLUGIN_PATHS = array('', 'controllers', 'models', 'views/templates');
        // register autoloader
        spl_autoload_register(array(__CLASS__, 'Autoloader'));
        
        // Init modules
        $seo = Exceptional_Seo::Instance();
        $seo->Init();
    }

    public static function Autoloader($class)
    {
        // Handle request starting with the plugin prefix only
        if (strpos($class, 'Exceptional_') !== 0)
        {
            return;
        }

        // try to find it in the registered paths
        $basePath = plugin_dir_path(__FILE__);
        // remove the prefix to get the class filename
        $classFile = str_replace('Exceptional_', '', $class). '.php';
        foreach (self::$PLUGIN_PATHS as $path)
        {
            // eg Exceptional_Seo -> /controllers/Seo.php
            if (@include $basePath.$path.'/'. $classFile )
            {
                break;
            }
        }
    }

}

// Init the runtime
Exceptional_Runtime::Init();

?>