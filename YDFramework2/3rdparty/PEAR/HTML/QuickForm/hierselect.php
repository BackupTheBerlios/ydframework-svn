<?php

require_once('HTML/QuickForm/group.php');
require_once('HTML/QuickForm/select.php');

class HTML_QuickForm_hierselect extends HTML_QuickForm_group
{   

    var $_options = array();
    var $_nbElements = 0;
    var $_js = "<script type=\"text/javascript\">\n";
    var $_jsArrayName = '';

    function HTML_QuickForm_hierselect($elementName=null, $elementLabel=null, $attributes=null, $separator=null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        if (isset($separator)) {
            $this->_separator = $separator;
        }
        $this->_type = 'hierselect';
        $this->_appendName = true;
    }

    function setOptions(&$options)
    {
        $this->_options = &$options;

        if (empty($this->_elements)) {
            $this->_nbElements = count($this->_options);
            $this->_createElements();
        } else {
            $totalNbElements = count($this->_options);
            for ($i = $this->_nbElements; $i < $totalNbElements; $i ++) {
                $this->_elements[] =& new HTML_QuickForm_select($i, null, array(), $this->getAttributes());
                $this->_nbElements++;
            }
        }
        
        $this->_setOptions();
        $this->_setJS();
    }

    function setMainOptions(&$array)
    {
        $this->_options[0] = &$array;

        if (empty($this->_elements)) {
            $this->_nbElements = 2;
            $this->_createElements();
        }
    }

    function setSecOptions(&$array)
    {
        $this->_options[1] = &$array;
        
        if (empty($this->_elements)) {
            $this->_nbElements = 2;
            $this->_createElements();
        } else {
            $totalNbElements = 2;
            for ($i = $this->_nbElements; $i < $totalNbElements; $i ++) {
                $this->_elements[] =& new HTML_QuickForm_select($i, null, array(), $this->getAttributes());
                $this->_nbElements++;
            }
        }
        
        $this->_setOptions();
        $this->_setJS();
    }

    function _setOptions()
    {
        $toLoad = '';
        foreach (array_keys($this->_elements) AS $key) {
            if (eval("return isset(\$this->_options[{$key}]{$toLoad});") ) {
                $array = eval("return \$this->_options[{$key}]{$toLoad};");
                if (is_array($array)) {                
                    $select =& $this->_elements[$key];
                    $select->_options = array();
                    $select->loadArray($array);
                    
                    $value  = is_array($v = $select->getValue()) ? $v[0] : key($array);                    
                    $toLoad .= '['.$value.']';
                }
            }
        }
    }

    function setValue($value)
    {
        $this->_nbElements = count($value);
        parent::setValue($value);
        $this->_setOptions();
    }

    function _createElements()
    {
        for ($i = 0; $i < $this->_nbElements; $i++) {
            $this->_elements[] =& new HTML_QuickForm_select($i, null, array(), $this->getAttributes());
        }
    }

    function _setJS()
    {
        $js      = '';
        $this->_jsArrayName = $this->getName();
        for ($i = 1; $i < $this->_nbElements; $i++) {
            $this->_setJSArray($this->_jsArrayName, $this->_options[$i], $js);
        }
    }

    function _setJSArray($grpName, $options, &$js, $optValue = '')
    {
        if (is_array($options)) {
            $js = '';
            $name  = ($optValue === '') ? $grpName : $grpName.'_'.$optValue;
            foreach($options AS $k => $v) {
                $this->_setJSArray($name, $v, $js, $k);
            }
            
            $this->_js .= ($js !== '') ? $name." = {\n".$js."\n}\n" : '';
            $js = '';
        } else {
            if ($js != '') {
                $js .= ",\n";
            }
            $js .= '"'.$optValue.'":"'.$options.'"';
        }
    }

    function toHtml()
    {
        if ($this->_flagFrozen) {
            $this->_js = '';
        } else {
            $keys               = array_keys($this->_elements);
            $nbElements         = count($keys);
            $nbElementsUsingFnc = $nbElements - 1;
            for ($i = 0; $i < $nbElementsUsingFnc; $i++) {
                $select =& $this->_elements[$keys[$i]];
                $select->updateAttributes(
                    array('onChange' => 'swapOptions(this, \''.$this->getName().'\', '.$keys[$i].', '.$nbElements.', \''.$this->_jsArrayName.'\');')
                );
            }
            
            if (!defined('HTML_QUICKFORM_HIERSELECT_EXISTS')) {
                $this->_js .= "function swapOptions(frm, grpName, eleIndex, nbElements, arName)\n"
                             ."{\n"
                             ."    var n = \"\";\n"
                             ."    var ctl;\n\n"
                             ."    for (var i = 0; i < nbElements; i++) {\n"
                             ."        ctl = frm.form[grpName+'['+i+']'];\n"
                             ."        if (i <= eleIndex) {\n"
                             ."            n += \"_\"+ctl.value;\n"
                             ."        } else {\n"
                             ."            ctl.length = 0;\n"
                             ."        }\n"
                             ."    }\n\n"
                             ."    var t = eval(\"typeof(\"+arName + n +\")\");\n"
                             ."    if (t != 'undefined') {\n"
                             ."        var the_array = eval(arName+n);\n"
                             ."        var j = 0;\n"
                             ."        n = eleIndex + 1;\n"
                             ."        ctl = frm.form[grpName+'['+ n +']'];\n"
                             ."        for (var i in the_array) {\n"
                             ."            opt = new Option(the_array[i], i, false, false);\n"
                             ."            ctl.options[j++] = opt;\n"
                             ."        }\n"
                             ."    }\n"
                             ."}\n";
                define('HTML_QUICKFORM_HIERSELECT_EXISTS', true);
            }
            $this->_js .= "</script>\n";
        }
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer =& new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);
        return $this->_js.$renderer->toHtml();
    }

    function accept(&$renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ('updateValue' == $event) {
            return HTML_QuickForm_element::onQuickFormEvent($event, $arg, $caller);
        } else {
            return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

}
?>