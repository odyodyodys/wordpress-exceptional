<?php
/**
 * The basic class to build an Exceptional Controller.
 * All controllers that need a constructor must declare it protected
 *
 * @author Odys
 */
abstract class Exceptional_AController
{
    /**
     * Protected constructor to prevent creating a new instance of the
     * Singleton via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }
    
    /**
     * Private clone method to prevent cloning of the instance of the Singleton instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the Singleton instance.
     *
     * @return void
     */
    private function __wakeup()
    {
        
    }
    
    /**
     * Singleton
     */
    final public static function Instance()
    {
        static $_instance = null;
        
        if (!$_instance)
        {
            $_instance = new static();
        }
        return $_instance;
    }
}
