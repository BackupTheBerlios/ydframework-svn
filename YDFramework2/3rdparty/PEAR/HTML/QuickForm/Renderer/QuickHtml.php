<?php

require_once('HTML/QuickForm/Renderer/Default.php');

class HTML_QuickForm_Renderer_QuickHtml extends HTML_QuickForm_Renderer_Default {

    var $renderedElements = array();

    function HTML_QuickForm_Renderer_QuickHtml()
    {
        $this->HTML_QuickForm_Renderer_Default();
        $this->clearAllTemplates();
    }

    function toHtml($data = '')
    {
        foreach (array_keys($this->renderedElements) as $key) {
            if (!$this->renderedElements[$key]['rendered']) {
                $this->renderedElements[$key]['rendered'] = true;
                $data .= $this->renderedElements[$key]['html'] . "\n";
            }
        }

        $this->_html = str_replace('</form>', $data . "\n</form>", $this->_html);
        return $this->_html;
    }

    function elementToHtml($elementName, $elementValue = null)
    {
        $elementKey = null;
        foreach ($this->renderedElements as $key => $data) {
            if ($data['name'] == $elementName && 
                (is_null($elementValue) ||
                 $data['value'] == $elementValue)) {
                $elementKey = $key;
                break;
            }
        }

        if (is_null($elementKey)) {
            $msg = is_null($elementValue) ? "Element $elementName does not exist." : 
                "Element $elementName with value of $elementValue does not exist.";
            return PEAR::raiseError(null, QUICKFORM_UNREGISTERED_ELEMENT, null, E_USER_WARNING, $msg, 'HTML_QuickForm_Error', true);
        } else {
            if ($this->renderedElements[$elementKey]['rendered']) {
                $msg = is_null($elementValue) ? "Element $elementName has already been rendered." : 
                    "Element $elementName with value of $elementValue has already been rendered.";
                return PEAR::raiseError(null, QUICKFORM_ERROR, null, E_USER_WARNING, $msg, 'HTML_QuickForm_Error', true);
            } else {
                $this->renderedElements[$elementKey]['rendered'] = true;
                return $this->renderedElements[$elementKey]['html'];
            }
        }
    }

    function renderElement(&$element, $required, $error)
    {
        $this->_html = '';
        parent::renderElement($element, $required, $error);
        if (!$this->_inGroup) {
            $this->renderedElements[] = array(
                    'name' => $element->getName(), 
                    'value' => $element->getValue(), 
                    'html' => $this->_html, 
                    'rendered' => false);
        }
        $this->_html = '';
    }

    function renderHidden(&$element)
    {
        $this->renderedElements[] = array(
                'name' => $element->getName(), 
                'value' => $element->getValue(), 
                'html' => $element->toHtml(), 
                'rendered' => false);
    }

    function finishGroup(&$group)
    {
        $this->_html = '';
        parent::finishGroup($group);
        $this->renderedElements[] = array(
                'name' => $group->getName(), 
                'value' => $group->getValue(), 
                'html' => $this->_html, 
                'rendered' => false);
        $this->_html = '';
    }

}
?>
