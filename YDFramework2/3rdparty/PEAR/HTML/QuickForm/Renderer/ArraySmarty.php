<?php

require_once 'HTML/QuickForm/Renderer/Array.php';

class HTML_QuickForm_Renderer_ArraySmarty extends HTML_QuickForm_Renderer_Array
{

    var $_tpl = null;
    var $_elementIdx = 0;
    var $_groupElementIdx = 0;
    var $_required = '';
    var $_error = '';

    function HTML_QuickForm_Renderer_ArraySmarty(&$tpl)
    {
        $this->HTML_QuickForm_Renderer_Array(true);
        $this->_tpl =& $tpl;
    }

    function renderHeader(&$header)
    {
        if ($name = $header->getName()) {
            $this->_ary['header'][$name] = $header->toHtml();
        } else {
            $this->_ary['header'][$this->_sectionCount] = $header->toHtml();
        }
        $this->_currentSection = $this->_sectionCount++;
    }

    function startGroup(&$group, $required, $error)
    {
        parent::startGroup($group, $required, $error);
        $this->_groupElementIdx = 1;
    }

    function _elementToArray(&$element, $required, $error)
    {
        $ret = parent::_elementToArray($element, $required, $error);

        if ('group' == $ret['type']) {
            $ret['html'] = $element->toHtml();
            unset($ret['elements']);
        }
        if (!empty($this->_required)){
            $this->_renderRequired($ret['label'], $ret['html'], $required, $error);
        }
        if (!empty($this->_error)) {
            $this->_renderError($ret['label'], $ret['html'], $error);
            $ret['error'] = $error;
        }
        if (strstr($ret['name'], '[') or $this->_currentGroup) {
            preg_match('/([^]]*)\\[([^]]*)\\]/', $ret['name'], $matches);
            if (isset($matches[1])) {
                $sKeysSub = substr_replace($ret['name'], '', 0, strlen($matches[1]));
                $sKeysSub = str_replace(
                    array('['  ,   ']', '[\'\']'),
                    array('[\'', '\']', '[]'    ),
                    $sKeysSub
                );
                $sKeys = '[\'' . $matches[1]  . '\']' . $sKeysSub;
            } else {
                $sKeys = '[\'' . $ret['name'] . '\']';
            }
            if ($this->_currentGroup) {
                if ($this->_currentGroup['keys'] == $sKeys and 'radio' != $ret['type']) {
                    return false;
                }
                if (0 === strpos($sKeys, $this->_currentGroup['keys'])) {
                    $sKeys = substr_replace($sKeys, '', 0, strlen($this->_currentGroup['keys']));
                }
            }
        } elseif ($ret['name'] == '') {
            $sKeys = '[\'element_' . $this->_elementIdx . '\']';
        } else {
            $sKeys = '[\'' . $ret['name'] . '\']';
        }
        if ('radio' == $ret['type'] and substr($sKeys, -2) != '[]') {
            $sKeys .= '[\'' . $ret['value'] . '\']';
        }
        $this->_elementIdx++;
        $ret['keys'] = $sKeys;
        return $ret;
    }

    function _storeArray($elAry)
    {
        if ($elAry) {
            $sKeys = $elAry['keys'];
            unset($elAry['keys']);
            if (is_array($this->_currentGroup) && ('group' != $elAry['type'])) {
                $toEval = '$this->_currentGroup' . $sKeys . ' = $elAry;';
            } else {
                $toEval = '$this->_ary' . $sKeys . ' = $elAry;';
            }
            eval($toEval);
        }
        return;
    }

    function _renderRequired(&$label, &$html, &$required, &$error)
    {
        $this->_tpl->assign(array(
            'label'    => $label,
            'html'     => $html,
            'required' => $required,
            'error'    => $error
        ));
        if (!empty($label) && strpos($this->_required, $this->_tpl->left_delimiter . '$label') !== false) {
            $label = $this->_tplFetch($this->_required);
        }
        if (!empty($html) && strpos($this->_required, $this->_tpl->left_delimiter . '$html') !== false) {
            $html = $this->_tplFetch($this->_required);
        }
        $this->_tpl->clear_assign(array('label', 'html', 'required'));
    }

    function _renderError(&$label, &$html, &$error)
    {
        $this->_tpl->assign(array('label' => '', 'html' => '', 'error' => $error));
        $error = $this->_tplFetch($this->_error);
        $this->_tpl->assign(array('label' => $label, 'html'  => $html));

        if (!empty($label) && strpos($this->_error, $this->_tpl->left_delimiter . '$label') !== false) {
            $label = $this->_tplFetch($this->_error);
        } elseif (!empty($html) && strpos($this->_error, $this->_tpl->left_delimiter . '$html') !== false) {
            $html = $this->_tplFetch($this->_error);
        }
        $this->_tpl->clear_assign(array('label', 'html', 'error'));
    }

    function _tplFetch($tplSource)
    {
        if (!function_exists('smarty_function_eval')) {
            require SMARTY_DIR . '/plugins/function.eval.php';
        }
        return smarty_function_eval(array('var' => $tplSource), $this->_tpl);
    }

    function setRequiredTemplate($template)
    {
        $this->_required = $template;
    }

    function setErrorTemplate($template)
    {
        $this->_error = $template;
    }
}
?>