<?php
/**
 * Base for AFilter's Terms
 *
 * @author Odys
 */
abstract class Exceptional_AFilterTerm
{
    /**
     * The permalink to filter using this term. If the term is already applied to a filter, the link is a filter without the term.
     */
    public $Permalink;
    
    /**
     * @var Exceptional_CheckState Specifies the checked state of the term
     */
    private $_checkedState;
    
    public $Slug;
    
    /**
     * The children of this term, if the taxonomy is hierarchical
     * @var Exceptional_AFilterTerm Children terms of this term
     */
    public $Children;

    public function __construct()
    {
        $this->_checkedState = Exceptional_CheckState::Unchecked;
        $this->Children = array();
    }
    
    /**
     * Clone the children term so they are safe to be manipulated.
     */    
    function __clone()
    {
        $newChildren = array();
        foreach ($this->Children as $child)
        {
            $newChildren[] = clone $child;
        }
        $this->Children = $newChildren;
    }
    
    /**
     * Sets the checked state. If slug is provided, the new value is set only if the term (or its children) match
     * @param Exceptional_CheckState $checkedState The checked state to be set
     * @param string $slug The slug of the term set the applied value
     * @return bool When slug is supplied returns true if a term with that slug exists, if slug is not supplied returns true
     */
    public function SetChecked($checkedState, $slug = '')
    {
        $success = false;
        if ($this->Slug === $slug || empty($slug))
        {
            $this->_checkedState = $checkedState;
            $success = true;
            
            // when checking/unckecking a parent, all children should be unckecked
            if ($checkedState === Exceptional_CheckState::Checked || $checkedState === Exceptional_CheckState::Unchecked)
            {
                foreach ($this->Children as $childTerm)
                {
                    $childTerm->SetChecked(Exceptional_CheckState::Unchecked);
                }
            }
        }
        else
        {
            // its not the parent, search in children
            foreach ($this->Children as $childTerm)
            {
                if($childTerm->SetChecked($checkedState, $slug) && $checkedState === Exceptional_CheckState::Checked)
                {
                    // the child is the target, update self to intermediate
                    $this->_checkedState = Exceptional_CheckState::Intermediate;
                    
                    $success = true;
                }
            }
        }
        
        return $success;
    }
    
    /**
     * Returns the checked state of the term
     * @return Exceptional_CheckState
     */
    public function GetCheckedState()
    {
        return $this->_checkedState;        
    }

    /**
     * Returns the checked terms, self or children
     * @return array Array with checked terms
     */
    public function GetChecked()
    {
        $checked = array();
        
        if ($this->_checkedState == Exceptional_CheckState::Checked)
        {
            $checked[] = $this;
        }
        else
        {
            foreach ($this->Children as $childTerm)
            {
                $checked = array_merge($checked, $childTerm->GetChecked());
            }
        }
        
        return $checked;
    }
    
    /**
     * Returns if the term has children terms
     */
    public function HasChildren()
    {
        return !empty($this->Children);
    }

    /**
     * The css class for this term
     */
    public function GetClass()
    {
        return "term term-{$this->Slug} ". Exceptional_CheckState::GetClass($this->_checkedState);
    }
}
