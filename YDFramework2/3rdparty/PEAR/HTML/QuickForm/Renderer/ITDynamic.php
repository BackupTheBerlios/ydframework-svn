<?php

require_once 'HTML/QuickForm/Renderer.php';

class HTML_QuickForm_Renderer_ITDynamic extends HTML_QuickForm_Renderer
{

    var $_tpl = null;
    var $_errors = array();
    var $_showRequired = false;
    var $_groupSeparator = null;
    var $_groupElementIdx = 0;
    var $_elementBlocks = array();
    var $_headerBlock = null;

    function HTML_QuickForm_Renderer_ITDynamic(&$tpl)
    {
        $this->HTML_QuickForm_Renderer();
        $this->_tpl =& $tpl;
        $this->_tpl->setCurrentBlock('qf_main_loop');
    }

    function finishForm(&$form)
    {
        if (!empty($this->_errors) && $this->_tpl->blockExists('qf_error_loop')) {
            foreach ($this->_errors as $error) {
                $this->_tpl->setVariable('qf_error', $error);
                $this->_tpl->parse('qf_error_loop');
            }
        }
        if ($this->_showRequired) {
            $this->_tpl->setVariable('qf_required_note', $form->getRequiredNote());
        }
        $this->_tpl->setVariable('qf_attributes', $form->getAttributes(true));
        $this->_tpl->setVariable('qf_javascript', $form->getValidationScript());
    }

    function renderHeader(&$header)
    {
        $blockName = $this->_matchBlock($header);
        if ('qf_header' == $blockName && isset($this->_headerBlock)) {
            $blockName = $this->_headerBlock;
        }
        $this->_tpl->setVariable('qf_header', $header->toHtml());
        $this->_tpl->parse($blockName);
        $this->_tpl->parse('qf_main_loop');
    }

    function renderElement(&$element, $required, $error)
    {
        $blockName = $this->_matchBlock($element);
        if ('qf_main_loop' != $this->_tpl->currentBlock) {
            if (0 != $this->_groupElementIdx && $this->_tpl->placeholderExists('qf_separator', $blockName)) {
                if (is_array($this->_groupSeparator)) {
                    $this->_tpl->setVariable('qf_separator', $this->_groupSeparator[($this->_groupElementIdx - 1) % count($this->_groupSeparator)]);
                } else {
                    $this->_tpl->setVariable('qf_separator', (string)$this->_groupSeparator);
                }
            }
            $this->_groupElementIdx++;

        } elseif(!empty($error)) {
            if ($this->_tpl->blockExists($blockName . '_error')) {
                $this->_tpl->setVariable('qf_error', $error);
            } else {
                $this->_errors[] = $error;
            }
        }
        if ($required) {
            $this->_showRequired = true;
            if ($this->_tpl->blockExists($blockName . '_required')) {
                $this->_tpl->touchBlock($blockName . '_required');
            }
        }
        $labels = $element->getLabel();
        if (is_array($labels)) {
            $mainLabel = array_shift($labels);
        } else {
            $mainLabel = $labels;
        }
        $this->_tpl->setVariable('qf_element', $element->toHtml());
        if ($this->_tpl->placeholderExists('qf_label', $blockName)) {
            $this->_tpl->setVariable('qf_label', $mainLabel);
        }
        if (is_array($labels)) {
            foreach($labels as $key => $label) {
                $key = is_int($key)? $key + 2: $key;
                if ($this->_tpl->blockExists($blockName . '_label_' . $key)) {
                    $this->_tpl->setVariable('qf_label_' . $key, $label);
                }
            }
        }
        $this->_tpl->parse($blockName);
        $this->_tpl->parseCurrentBlock();
    }

    function renderHidden(&$element)
    {
        $this->_tpl->setVariable('qf_hidden', $element->toHtml());
        $this->_tpl->parse('qf_hidden_loop');
    }

    function startGroup(&$group, $required, $error)
    {
        $blockName = $this->_matchBlock($group);
        $this->_tpl->setCurrentBlock($blockName . '_loop');
        $this->_groupElementIdx = 0;
        $this->_groupSeparator  = empty($group->_separator)? '&nbsp;': $group->_separator;
        if ($required) {
            $this->_showRequired = true;
            if ($this->_tpl->blockExists($blockName . '_required')) {
                $this->_tpl->touchBlock($blockName . '_required');
            }
        }
        if (!empty($error)) {
            if ($this->_tpl->blockExists($blockName . '_error')) {
                $this->_tpl->setVariable('qf_error', $error);
            } else {
                $this->_errors[] = $error;
            }
        }
        $this->_tpl->setVariable('qf_group_label', $group->getLabel());
    }

    function finishGroup(&$group)
    {
        $this->_tpl->parse($this->_matchBlock($group));
        $this->_tpl->setCurrentBlock('qf_main_loop');
        $this->_tpl->parseCurrentBlock();
    }

    function _matchBlock(&$element)
    {
        $name = $element->getName();
        $type = $element->getType();
        if (isset($this->_elementBlocks[$name]) && $this->_tpl->blockExists($this->_elementBlocks[$name])) {
            if (('group' == $type) || ($this->_elementBlocks[$name] . '_loop' != $this->_tpl->currentBlock)) {
                return $this->_elementBlocks[$name];
            }
        }
        if ('group' != $type && 'qf_main_loop' != $this->_tpl->currentBlock) {
            $prefix = substr($this->_tpl->currentBlock, 0, -5); // omit '_loop' postfix
        } else {
            $prefix = 'qf';
        }
        if ($this->_tpl->blockExists($prefix . '_' . $type)) {
            return $prefix . '_' . $type;
        } else {
            return $prefix . '_element';
        }
    }

    function setElementBlock($elementName, $blockName = null)
    {
        if (is_array($elementName)) {
            $this->_elementBlocks = array_merge($this->_elementBlocks, $elementName);
        } else {
            $this->_elementBlocks[$elementName] = $blockName;
        }
    }

    function setHeaderBlock($blockName)
    {
        $this->_headerBlock = $blockName;
    }
}
?>
