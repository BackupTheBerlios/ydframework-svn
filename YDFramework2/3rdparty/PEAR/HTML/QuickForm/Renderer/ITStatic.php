<?php

require_once('HTML/QuickForm/Renderer.php');

class HTML_QuickForm_Renderer_ITStatic extends HTML_QuickForm_Renderer
{

    var $_tpl = null;
    var $_formName = 'form';
    var $_errors = array();
    var $_showRequired = false;
    var $_inGroup;
    var $_elementIndex = 0;
    var $_duplicateElements = array();
    var $_required = '{label}<font size="1" color="red">*</font>';
    var $_error = '<font color="red">{error}</font><br />{html}';
    var $_hidden = '';

    function HTML_QuickForm_Renderer_ITStatic(&$tpl)
    {
        $this->HTML_QuickForm_Renderer();
        $this->_tpl =& $tpl;
    }

    function startForm(&$form)
    {
        $this->_formName = $form->getAttribute('id');

        if (count($form->_duplicateIndex) > 0) {
            foreach ($form->_duplicateIndex as $elementName => $indexes) {
                $this->_duplicateElements[$elementName] = 0;
            }
        }
    }

    function finishForm(&$form)
    {
        if (!empty($this->_errors) && $this->_tpl->blockExists($this->_formName.'_error_loop')) {
            foreach ($this->_errors as $error) {
                $this->_tpl->setVariable($this->_formName.'_error', $error);
                $this->_tpl->parse($this->_formName.'_error_loop');
            }
        }
        if ($this->_showRequired) {
            $this->_tpl->setVariable($this->_formName.'_required_note', $form->getRequiredNote());
        }
        if (!empty($this->_hidden)) {
            $this->_tpl->setVariable($this->_formName . '_hidden', $this->_hidden);
        }
        $this->_tpl->setVariable($this->_formName.'_attributes', $form->getAttributes(true));
        $this->_tpl->setVariable($this->_formName.'_javascript', $form->getValidationScript());
    }

    function renderHeader(&$header)
    {
        $name = $header->getName();
        $varName = $this->_formName.'_header';

        if (!empty($name) && $this->_tpl->placeHolderExists($this->_formName.'_header_'.$name)) {
            $varName = $this->_formName.'_header_'.$name;
        }
        $this->_tpl->setVariable($varName, $header->toHtml());
    }

    function renderElement(&$element, $required, $error)
    {
        $name = $element->getName();

        if (!empty($this->_inGroup)) {
            $varName = $this->_formName.'_'.str_replace(array('[', ']'), '_', $name);
            if (substr($varName, -2) == '__') {
                $varName = $this->_inGroup.'_'.$this->_elementIndex.'_';
                $this->_elementIndex++;
            }
            if ($varName != $this->_inGroup) {
                $varName .= '_' == substr($varName, -1)? '': '_';
                $label = $element->getLabel();
                $html = $element->toHtml();

                if ($required && !$element->isFrozen()) {
                    $this->_renderRequired($label, $html);
                    $this->_showRequired = true;
                }
                if (!empty($label)) {
                    if (is_array($label)) {
                        foreach ($label as $key => $value) {
                            $this->_tpl->setVariable($varName.'label_'.$key, $value);
                        }
                    } else {
                        $this->_tpl->setVariable($varName.'label', $label);
                    }
                }
                $this->_tpl->setVariable($varName.'html', $html);
            }

        } else {

            $name = str_replace(array('[', ']'), array('_', ''), $name);

            if (isset($this->_duplicateElements[$name])) {
                $varName = $this->_formName.'_'.$name.'_'.$this->_duplicateElements[$name];
                $this->_duplicateElements[$name]++;
            } else {
                $varName = $this->_formName.'_'.$name;
            }

            $label = $element->getLabel();
            $html = $element->toHtml();

            if ($required) {
                $this->_showRequired = true;
                $this->_renderRequired($label, $html);
            }
            if (!empty($error)) {
                $this->_renderError($label, $html, $error);
            }
            if (is_array($label)) {
                foreach ($label as $key => $value) {
                    $this->_tpl->setVariable($varName.'_label_'.$key, $value);
                }
            } else {
                $this->_tpl->setVariable($varName.'_label', $label);
            }
            $this->_tpl->setVariable($varName.'_html', $html);
        }
    }

    function renderHidden(&$element)
    {
        if ($this->_tpl->placeholderExists($this->_formName . '_hidden')) {
            $this->_hidden .= $element->toHtml();
        } else {
            $this->_tpl->setVariable($this->_formName.'_'.$element->getName().'_html', $element->toHtml());
        }
    }

    function startGroup(&$group, $required, $error)
    {
        $name = $group->getName();
        $varName = $this->_formName.'_'.$name;

        $this->_elementIndex = 0;

        $html = $this->_tpl->placeholderExists($varName.'_html') ? $group->toHtml() : '';
        $label = $group->getLabel();

        if ($required) {
            $this->_renderRequired($label, $html);
        }
        if (!empty($error)) {
            $this->_renderError($label, $html, $error);
        }
        if (!empty($html)) {
            $this->_tpl->setVariable($varName.'_html', $html);
        } else {
            if (!empty($error)) {
                if ($this->_tpl->placeholderExists($varName.'_error') &&
                   (strpos($this->_error, '{html}') !== false || strpos($this->_error, '{label}') !== false)) {
                    $error = str_replace('{error}', $error, $this->_error);
                    $this->_tpl->setVariable($varName.'_error', $error);
                    array_pop($this->_errors);
                }
            }
        }
        if (is_array($label)) {
            foreach ($label as $key => $value) {
                $this->_tpl->setVariable($varName.'_label_'.$key, $value);
            }
        } else {
            $this->_tpl->setVariable($varName.'_label', $label);
        }
        $this->_inGroup = $varName;
    }

    function finishGroup(&$group)
    {
        $this->_inGroup = '';
    }

    function setRequiredTemplate($template)
    {
        $this->_required = $template;
    }

    function setErrorTemplate($template)
    {
        $this->_error = $template;
    }

    function _renderRequired(&$label, &$html)
    {
        if (!empty($label) && strpos($this->_required, '{label}') !== false) {
            if (is_array($label)) {
                $label[0] = str_replace('{label}', $label[0], $this->_required);
            } else {
                $label = str_replace('{label}', $label, $this->_required);
            }
        }
        if (!empty($html) && strpos($this->_required, '{html}') !== false) {
            $html = str_replace('{html}', $html, $this->_required);
        }
    }

    function _renderError(&$label, &$html, $error)
    {
        if (!empty($label) && strpos($this->_error, '{label}') !== false) {
            if (is_array($label)) {
                $label[0] = str_replace(array('{label}', '{error}'), array($label[0], $error), $this->_error);
            } else {
                $label = str_replace(array('{label}', '{error}'), array($label, $error), $this->_error);
            }
        } elseif (!empty($html) && strpos($this->_error, '{html}') !== false) {
            $html = str_replace(array('{html}', '{error}'), array($html, $error), $this->_error);
        } else {
            $this->_errors[] = $error;
        }
    }
}
?>