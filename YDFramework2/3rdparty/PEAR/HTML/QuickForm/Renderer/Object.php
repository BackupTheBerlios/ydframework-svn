<?php

require_once('HTML/QuickForm/Renderer.php');

class HTML_QuickForm_Renderer_Object extends HTML_QuickForm_Renderer {

    var $_obj= null;
    var $_sectionCount;
    var $_currentSection;
    var $_currentGroup = null;
    var $_elementType = 'QuickFormElement';
    var $_elementStyles = array();
    var $_collectHidden = false;

    function HTML_QuickForm_Renderer_Object($collecthidden = false) 
    {
        $this->HTML_QuickForm_Renderer();
        $this->_collectHidden = $collecthidden;
        $this->_obj = new QuickformForm;
    }

    function toObject() 
    {
        return $this->_obj;
    }

    function setElementType($type) {
        $this->_elementType = $type;
    }

    function startForm(&$form) 
    {
        $this->_obj->frozen = $form->isFrozen();
        $this->_obj->javascript = $form->getValidationScript();
        $this->_obj->attributes = $form->getAttributes(true);
        $this->_obj->requirednote = $form->getRequiredNote();
        $this->_obj->errors = new StdClass;

        if($this->_collectHidden) {
            $this->_obj->hidden = '';
        }
        $this->_elementIdx = 1;
        $this->_currentSection = null;
        $this->_sectionCount = 0;
    }

    function renderHeader(&$header) 
    {
        $hobj = new StdClass;
        $hobj->header = $header->toHtml();
        $this->_obj->sections[$this->_sectionCount] = $hobj;
        $this->_currentSection = $this->_sectionCount++;
    }
    function renderElement(&$element, $required, $error) 
    {
        $elObj = $this->_elementToObject($element, $required, $error);
        if(!empty($error)) {
            $name = $elObj->name;
            $this->_obj->errors->$name = $error;
        }
        $this->_storeObject($elObj);
    }

    function renderHidden(&$element) {
        if($this->_collectHidden) {
            $this->_obj->hidden .= $element->toHtml() . "\n";
        } else {
            $this->renderElement($element, false, null);
        }
    }

    function startGroup(&$group, $required, $error) 
    {
        $this->_currentGroup = $this->_elementToObject($group, $required, $error);
        if(!empty($error)) {
            $name = $this->_currentGroup->name;
            $this->_view->errors->$name = $error;
        }
    }

    function finishGroup(&$group) 
    {
        $this->_storeObject($this->_currentGroup);
        $this->_currentGroup = null;
    }

    function _elementToObject(&$element, $required, $error) 
    {
        if($this->_elementType) {
            $ret = new $this->_elementType;
        }
        $ret->name = $element->getName();
        $ret->value = $element->getValue();
        $ret->type = $element->getType();
        $ret->frozen = $element->isFrozen();
        $labels = $element->getLabel();
        if (is_array($labels)) {
            $ret->label = array_shift($labels);
            foreach ($labels as $key => $label) {
                $key = is_int($key)? $key + 2: $key;
                $ret->{'label_' . $key} = $label;
            }
        } else {
            $ret->label = $labels;
        }
        $ret->required = $required;
        $ret->error = $error;

        if(isset($this->_elementStyles[$ret->name])) {
            $ret->style = $this->_elementStyles[$ret->name];
            $ret->styleTemplate = "styles/". $ret->style .".html";
        }
        if($ret->type == 'group') {
            $ret->separator = $element->_separator;
            $ret->elements = array();
        } else {
            $ret->html = $element->toHtml();
        }
        return $ret;
    }

    function _storeObject($elObj) 
    {
        $name = $elObj->name;
        if(is_object($this->_currentGroup) && $elObj->type != 'group') {
            $this->_currentGroup->elements[] = $elObj;
        } elseif (isset($this->_currentSection)) {
            $this->_obj->sections[$this->_currentSection]->elements[] = $elObj;
        } else {
            $this->_obj->elements[] = $elObj;
        }
    }

    function setElementStyle($elementName, $styleName = null)
    {
        if(is_array($elementName)) {
            $this->_elementStyles = array_merge($this->_elementStyles, $elementName);
        } else {
            $this->_elementStyles[$elementName] = $styleName;
        }
    }

}

class QuickformForm {

    var $frozen;        
    var $javascript;
    var $attributes;
    var $requirednote;
    var $hidden;
    var $errors;
    var $elements;
    var $sections;

     function outputHeader() {
        $hdr = "<form " . $this->attributes . ">\n";
        return $hdr;
       }

     function outputJavaScript() {
        return $this->javascript;
     }
}

class QuickformElement {
    
    var $name;
    var $value;
    var $type;
    var $frozen;
    var $label;
    var $required;
    var $error;
    var $style;
    var $html;
    var $separator;
    var $elements;

    function isType($type)
    {
        if($this->type == $type) {
            return true;
        } else {
            return false;
        }
    }
    
    function notFrozen()
    {
        if(!$this->frozen) {
            return true;
        } else {
            return false;
        }
    }

    function isButton()
    {
        if($this->type == "submit" || $this->type == "reset") {
            return true;
        } else {
            return false;
        }
    }

    function outputStyle()
    {
        $filename = "styles/".$this->style.".html";
        ob_start();
        HTML_Template_Flexy::staticQuickTemplate($filename, $this);
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }

}
?>