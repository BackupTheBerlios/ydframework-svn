<?php

require_once('HTML/QuickForm/textarea.php');

/**
 * HTML class for a htmlarea field
 *
 * HTMLArea is a WYSIWYG HTML editor which can be obtained from
 * http://dynarch.com/mishoo/htmlarea.epl
 * 
 * @author       Jan Wagner <wagner@netsols.de>
 * @access       public
 */
class HTML_QuickForm_htmlarea extends HTML_QuickForm_textarea
{
    /**
     * URL where to find htmlarea
     *
     * @var string
     * @access private
     */
    var $_url = './htmlarea/';
    
    /**
     * Language of HTMLArea
     *
     * @var string
     * @access private
     */
    var $_lang = 'en';
    
    /**
     * Configuration array for HTMLArea
     *
     * @var array
     * @access private
     */
    var $_config = array();
    
    /**
     * Additional buttons for HTMLArea
     *
     * @var array
     * @access private
     */
    var $_buttons = array();
    
    /**
     * Additional buttons of standard toolbar
     *
     * @var array
     * @access private
     */
    var $_toolbar = array();

    /**
     * Hidden buttons of standard toolbar
     *
     * @var array
     * @access private
     */
    var $_hidden_buttons = array();

    /**
     * Additional dropdown lists for HTMLArea
     *
     * @var array
     * @access private
     */
    var $_dropdown = array();
    
    /**
     * Registered Plugins
     *
     * @var array
     * @access private
     */
    var $_plugins = array();
    
    /**
     * Class constructor
     *
     * @param   string  Htmlarea name/id
     * @param   string  Htmlarea label
     * @param   array   Options for htmlarea
     * @param   string  Attributes for the textarea
     * @access  public
     * @return  void
     */
    function HTML_QuickForm_htmlarea($elementName=null, $elementLabel=null, $options=array(), $attributes=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'htmlarea';
        
        if (is_array($options)) {
            foreach ($options as $k => $v) {                
                if (isset($this->{"_$k"}) && method_exists($this, "set$k")) {
                    $this->{"set$k"}($v);
                }
            }
        }
    }
    
    /**
     * Set the url where the htmlarea scripts are located
     *
     * @param string
     * @return void
     */
    function setURL($url)
    {
        if (is_string($url) && strlen($url)) {
            $this->_url = $url;
            if (substr($url, -1) != '/') {
                $this->_url .= '/';
            }
        }
    }
    
    /**
     * Set the htmlarea language
     *
     * @param string
     * @return void
     */
    function setLang($lang)
    {
        if (is_string($lang) && strlen($lang)) {
            $this->_lang = $lang;
        }
    }
    
    /**
     * Set config array for HTMLArea
     *
     * @param mixed Name of config var or array
     * @param mixed Value of config var
     * @access public
     * @return void     
     */
    function setConfig($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->_config[$k] = $v;
            }
        } else {
            $this->_config[$name] = $value;
        }
    }

    /**
     * Set Toolbar in config array
     *
     * @param array Array of buttons
     * @access public
     * @return void     
     */
    function setToolbar($buttons)
    {
        if (is_array($buttons) && count($buttons)) {
            $this->_config['toolbar'][] = $buttons;
        }
    }

    /**
     * Register button to HTMLArea
     *
     * Format of array:
     * array('id' => 'ID of the button',
     *       'tooltip' =>'Tooltip for the Button',
     *       'image' => 'Image for the button',
     *       'textmode' => true,
     *       'action' => 'Action to take on press of button',
     *       'context' => 'Context for the button')
     *
     * @param array 
     * @access public
     * @return void     
     */
    function registerButton($button)
    {
        if (is_array($button) && count($button)) {
            $this->_buttons[] = $button;
        } 
    }
    
    /**
     * Push button to standard toolbar
     *
     * @param string button name
     * @access public
     * @return void     
     */
    function pushButton($name)
    {
        if (!in_array($name, $this->_toolbar)) {
            $this->_toolbar[] = $name;
        }
    }

    /**
     * Remove button from standard toolbar
     *
     * @param string button name
     * @access public
     * @return void     
     */
    function hideButton($name)
    {
        if (strlen($name)) {
            if (!in_array($name, $this->_hidden_buttons)) {
                $this->_hidden_buttons[] = "$name";
            }
        }
    }

    /**
     * Register additional dropdown list
     *
     * Format of array:
     * array('id' => 'dropdown_id',
     *       'tooltip' => 'Tooltip',
     *       'options' => array('name1' => 'value1', 'name2' => 'value2'),
     *       'action' => 'Javascript action to take upon changing value',
     *       'refresh' => 'Javascript action to take upon refreshing page')
     *
     * @param array Dropdown list options
     * @access public
     * @return void
     */
    function registerDropdown($list)
    {
        if (is_array($list) && count($list)) {
            $this->_dropdown[] = $list;
        }
    }
    
    /**
     * Register Plugin to HTMLArea
     *
     * @param string plugin name
     * @param array plugin config
     * @access public
     * @return void     
     */
    function registerPlugin($name, $config = array())
    {
        $this->_plugins[$name] = $config;
    }
    
    /**
     * Return JS representation of config array
     *
     * @access private
     * @return string  
     */
    function _config2JS()
    {
        $name = $this->getAttribute('name');
        $js = '';
        
        // register buttons
        if (count($this->_buttons)) {
            foreach ($this->_buttons as $button) {
                $js .= sprintf("%sConfig.registerButton('%s','%s','%s',%s,%s%s);\n",
                               $name,
                               $button['id'],
                               $button['tooltip'],
                               $button['image'],
                               ($button['textmode']) ? 'true' : 'false',
                               $button['action'],
                               (isset($button['context'])) ? ",{$button['context']}" : '');
            }
        }
        // register dropdown lists
        if (count($this->_dropdown)) {
            foreach ($this->_dropdown as $dropdown) {
                $js .= sprintf("%sConfig.registerDropdown( { 'id' : '%s', 'tooltip' : '%s', 'options' : %s, 'refresh' : %s, 'action' : %s } );\n",
                               $name,
                               $dropdown['id'],
                               $dropdown['tooltip'],
                               $this->_var2JS($dropdown['options']),
                               $dropdown['refresh'],                               
                               $dropdown['action']);
            }
        }
        // push buttons to standard toolbar
        if (count($this->_toolbar)) {
            $js .= "{$name}Config.toolbar.push([ ";
            $i = 1;
            foreach ($this->_toolbar as $button) {
                $js .= "'$button'";
                if ($i++ < count($this->_toolbar)) {
                    $js .= ', ';
                }
                $js .= " ]);\n";
            }
        }        
        // hide buttons from standard toolbar
        if (count($this->_hidden_buttons)) {
            $js .= "{$name}Config.hideSomeButtons(' ";
            foreach ($this->_hidden_buttons as $button) {
                $js .= "$button ";
            }
            $js .= "');\n";
        }        
        // output config
        foreach ($this->_config as $k => $v) {
            $js .= "{$name}Config.$k = ";
            $js .= $this->_var2JS($v);
            $js .= ";\n";
        }
        return $js;
    }
    
    function _var2JS($var)
    {
        $type = gettype($var);        
        if ($type == "array") {
            $value = $this->_array2JS($var);
        } elseif ($type == "boolean") {
            $value = ($var) ? 'true' : 'false';
        } elseif ($type == "integer" || $type == "double") {
            $value = $var;
        } else {
            $value = "'$var'";
        }
        return $value;
    }

    /**
     * Return JS representation of an array
     *
     * @access private
     * @return string  
     */
    function _array2JS($value)
    {
        $hash = true;
        if (is_array($value)) {
            $key = key($value);
            if (is_numeric($key) && $key === 0) {
                $hash = false;
            }
            reset($value);
        }
        
        $js  = ($hash) ? '{ ' : '[ ';
        
        $i = 1;
        $j = count($value);
        foreach ($value as $k => $v) {
           if ($hash) { $js .= "'$k' : "; }
           $js .= $this->_var2JS($v);
           if ($i++ < $j) {
               $js .= ', ';
           }
        }
        $js .= ($hash) ? ' }' : ' ]';

        return $js;
    }
    
    /**
     * Check if the browser supports HTMLArea (IE 5.5+, Gecko > 20030210)
     *
     * @access public
     * @return boolean
     */
    function browserSupported()
    {
        $supported = false;
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);            
            if (($ie_pos = strpos($agent, 'msie')) !== false &&
                strpos($agent, 'opera') === false) {                
                if ((float) substr($agent, $ie_pos + 5, 3) >= 5.5) {
                    $supported = true;
                }
            } elseif (($gecko_pos = strpos($agent, 'gecko')) !== false) {
                if ((int) substr($agent, $gecko_pos + 6, 8 ) >= 20030210) {
                    $supported = true;
                }
            }             
        }   
        return $supported;
    }

    /**
     * Return the htmlarea in HTML
     *
     * @access public
     * @return string
     */
    function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } elseif ($this->browserSupported() == false) {
            return parent::toHtml();
        } else {            
            $name = $this->getAttribute('name');
            $this->updateAttributes(array('id' => $name));
            
            $pre_js = $post_js = '';
            
            if (!defined('HTML_QUICKFORM_HTMLAREA_LOADED')) {
                // set config vars
                $pre_js  = "\n<script type=\"text/javascript\">\n";
                $pre_js .= "_editor_url = '$this->_url';\n";
                $pre_js .= "_editor_lang = '$this->_lang';\n";
                $pre_js .= "</script>\n";
                
                // load htmlarea
                $pre_js .= "<script type=\"text/javascript\" src=\"{$this->_url}htmlarea.js\"></script>\n";
                
                define('HTML_QUICKFORM_HTMLAREA_LOADED', true);
            }
            
            // load plugins
            $plugin_section = false;            
            foreach ($this->_plugins as $plugin => $config) {
                $const = 'HTML_QUICKFORM_HTMLAREA_PLUGIN_' . strtoupper($plugin);
                if (!defined($const)) {
                    if (!$plugin_section) {
                        $pre_js .= "\n<script type=\"text/javascript\">\n";
                        $plugin_section = true;
                    }
                    $pre_js .= "HTMLArea.loadPlugin('$plugin');\n";
                    define($const, true);
                }
            }
            if ($plugin_section) {
                $pre_js .= "</script>\n";
            }
            
            // setup editor
            $post_js = "\n<script type=\"text/javascript\">\n";
            $post_js .= "var {$name}Editor = new HTMLArea('$name');\n";
            
            // register plugins
            foreach ($this->_plugins as $plugin => $config) {                
                $post_js .= "{$name}Editor.registerPlugin($plugin";
                if (count($config)) {
                    $post_js .= "," . $this->_array2JS($config);
                }
                $post_js .= ");\n";
            }
            // set config
            $post_js .= "var {$name}Config = {$name}Editor.config;\n";
            $post_js .= $this->_config2JS();
            $post_js .= "{$name}Editor.generate();\n";
            $post_js .= "</script>\n";        
            
            return $pre_js.parent::toHtml().$post_js;
        }
    }
    
    /**
     * Returns the htmlarea content in HTML
     * 
     * @access public
     * @return string
     */
    function getFrozenHtml()
    {
        return $this->getValue();
    }
}

?>