<?php
/**
 * Handles notices. Small messages that inform the user about actions, errors, states and can optionally contain operation/actions.
 *
 * @author Antonis
 */
class Exceptional_Notices extends Exceptional_APresentationController
{
    /**
     * Each notice must belong to one of them, like categories. They are provided to the template engine also.
     * @var array Notice types
     */
    private $_types;

    /**
     * @var array The notices. Key is the type and value is an array of notices for this type.
     */
    private $_notices;
    
    /**
     * Action that triggers the notices to be displayed
     * @var string The action name
     */
    private $_displayAction;

    private function __construct()
    {
        parent::__construct();
        
        $this->_notices = array();
        $this->_types = array();
    }
    
    // Methods
    
    public function Init()
    {
        parent::Init();
        
        add_action($this->_displayAction, array($this, 'Display'));
    }

    /**
     * Registers a notice type
     * @param string $type
     */
    public function RegisterType($type)
    {
        $this->_types[] = $type;
    }

    /**
     * Adds a notice to the notices
     * @param string $notice The notice. Can also be markup
     * @param string $type The notice type
     */
    public function AddNotice($notice, $type)
    {
        if (!array_key_exists($type, $this->_notices))
        {
            $this->_notices[$type] = array();
        }
        
        $this->_notices[$type][] = $notice;
    }
    
    /**
     * Sets the display action
     * @param string $action The action name
     */
    public function SetDisplayAction($action)
    {
        $this->_displayAction = $action;
    }
    
    /**
     * Displays all notices
     */
    public function Display()
    {
        $this->_template->DisplayNotices($this->_notices);
    }
}
