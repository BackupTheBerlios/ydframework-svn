<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

        This program is free software; you can redistribute it and/or
        modify it under the terms of the GNU General Public License
        as published by the Free Software Foundation;

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

    */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    YDConfig::set( 'YD_LOG_FILE', 'YDInstaller_log.xml', false );
    
    include_once( YD_DIR_HOME_CLS . '/YDTemplate.php' );
    include_once( YD_DIR_HOME_CLS . '/YDForm.php' );
    include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php' );
    include_once( YD_DIR_HOME_CLS . '/YDLog.php' );
    include_once( YD_DIR_HOME_CLS . '/YDPersistent.php' );

    /**
     *  This class defines a YDInstaller object.
     */
    class YDInstaller extends YDRequest {

        var $_items = array();
        var $_css = array();
        var $_css_link = array();
        var $_js = array();
        var $_js_link = array();
        var $_errors = array();

        /**
         *  This is the class constructor for the YDInstaller class.
         */
        function YDInstaller( $title, $description='', $log=true ) {

            // Initialize the parent class
            $this->YDRequest();
            
            $this->_template = new YDTemplate();
            $this->_form     = new YDForm( 'YDInstaller' );
            
            // Set form details
            $this->_form->setHtmlError( '' , '' );
            $this->_form->registerRenderer( 'YDInstaller', 'YDInstaller_renderer', dirname( __FILE__ ) . '/YDInstaller_renderer.php' );
            
            // Source and target directories
            $this->_source_dir = YD_SELF_DIR;
            $this->_target_dir = YD_SELF_DIR;
            
            // Title and description
            $this->_title       = $title;
            $this->_description = $description;
            $this->_log         = $log;
            
            // Check if the log directory is writeable
            if ( $log && ! $this->isWriteable( YDPath::getDirectoryName( YDConfig::get( 'YD_LOG_FILE' ) ) ) ) {
                $this->addError( 'The installer log directory is not writeable.' );
            }
            
            // Default show form = false
            $this->_show_form = false;
            
            // Set the installer values to the object
            $values = YDPersistent::get( 'YD_INSTALLER_VALUES', array(), 'YD_INSTALLER' );
            foreach ( $values as $name => $value ) {
                $this->$name = $value;
            }

        }
        
        /**
         *  This function will set the HTML that is added before and after the element
         *  label to indicate that the element is required. This only has affect if
         *  you use the default toHtml function.
         *
         *  @param $start   The HTML that should be added before the label.
         *  @param $end     The HTML that should be added after the label.
         */
        function setHtmlRequired( $start, $end ) {
            $this->_form->setHtmlRequired( $start, $end );
        }
        
        /**
         *  Assign a a new variable to the template.
         *
         *  @param  $name   The name of the variable.
         *  @param  $value  The value of the variable.
         */
        function assign( $name, $value ) {
            $this->_template->assign( $name, $value );
        }
        
        /**
         *  This function will parse the template and will display the parsed contents.
         */
        function display() {
            
            // Assign form render if form is defined
            if ( $this->_show_form ) {
                $this->assign( '_form', $this->_form->render( 'YDInstaller' ) );
                
                if ( sizeof( $this->_form->_errors ) ) {
                    foreach ( $this->_form->_errors as $error ) {
                        $this->addError( $error, false );
                    }
                }
                
            }
            
            // Assign class variables
            $this->assign( '_show_form', $this->_show_form );
            $this->assign( '_items', $this->_items );
            $this->assign( '_css', $this->_css );
            $this->assign( '_css_link', $this->_css_link );
            $this->assign( '_js', $this->_js );
            $this->assign( '_js_link', $this->_js_link );
            $this->assign( '_title', $this->_title );
            $this->assign( '_description', $this->_description );
            $this->assign( '_errors', $this->_errors );
            
            // Display the template
            $this->_template->display( dirname( __FILE__ ) . '/YDInstaller.tpl' );
            
        }
        
        /**
         *  This function sets the form elements that will be saved after submittion.
         *
         *  @param  $name  Form element name
         */
        function saveField( $name ) {
            
            // $name can be an array of fields
            if ( ! is_array( $name ) ) {
                $name = array( $name );
            }
            
            // Add the fields
            foreach ( $name as $n ) {
                if ( is_null( $this->getField( $n ) ) ) {
                    $this->setField( $n, null );
                }
            }
            
        }
        
        /**
         *  This function adds a header to the installer body.
         *
         *  @param  $text        The header text
         *  @param  $attributes  (optional) Array of attributes
         */
        function addHeader( $text, $attributes=array() ) {
            $this->_items[] = array(
                'type' => 'header',
                'info' => $text,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
        
        /**
         *  This function adds a &lt;pre&gt; container to the installer body.
         *
         *  @param  $text        The text
         *  @param  $attributes  (optional) Array of attributes
         */
        function addPre( $text, $attributes=array() ) {
            $this->_items[] = array(
                'type' => 'pre',
                'info' => $text,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
        
        /**
         *  This function adds HTML code to the installer body.
         *
         *  @param  $text        The HTML code
         */
        function addHtml( $text ) {
            $this->_items[] = array(
                'type' => 'html',
                'info' => $text
            );
        }
        
        /**
         *  This function adds a separator to the installer body.
         */
        function addSeparator() {
            $this->_items[] = array(
                'type' => 'separator',
                'info' => '',
                'attributes' => array(),
            );
        }
        
        /**
         *  This function adds a list to the installer body.
         *
         *  @param  $list        The list array
         *  @param  $attributes  (optional) Array of attributes
         */
        function addList( $list, $attributes=array() ) {
            $this->_items[] = array(
                'type' => 'list',
                'info' => $list,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
        
        /**
         *  This function adds a numeric list to the installer body.
         *
         *  @param  $list        The list array
         *  @param  $attributes  (optional) Array of attributes
         */
        function addNumericList( $list, $attributes=array() ) {
            $this->_items[] = array(
                'type' => 'num_list',
                'info' => $list,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
        
        /**
         *  This function adds a text paragraph to the installer body.
         *
         *  @param  $text        The text
         *  @param  $attributes  (optional) Array of attributes
         */
        function addText( $text, $attributes=array() ) {
            $this->_items[] = array(
                'type' => 'text',
                'info' => $text,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
        
        /**
         *  This function adds an image to the installer body.
         *
         *  @param  $url        The image url
         *  @param  $attributes  (optional) Array of attributes
         */
        function addImage( $url, $attributes=array() ) {
            $this->_items[] = array(
                'type' => 'image',
                'info' => $url,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
    
        /**
         *  This function adds an alert to the installer body.
         *
         *  @param  $text        The text
         *  @param  $attributes  (optional) Array of attributes
         */
        function addAlert( $text, $attributes=array() ) {
            
            $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' alert' : 'alert';
            
            $this->_items[] = array(
                'type' => 'alert',
                'info' => $text,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
        }
        
        /**
         *  This function adds a highligthed PHP code to the installer body.
         *
         *  @param  $code        The PHP code
         */
        function addPhp( $code ) {
            
            preg_match( '/<code>(.*)<\/code>/s', highlight_string( $code, true ), $match );
            $this->addHtml( '<p class="code">' . trim( $match[1] ) . '</p>' );
            
        }
        
        /**
         *  This function adds a link to the installer template body.
         *
         *  @param  $url        The url
         *  @param  $text       (optional) The link text. Default: $url.
         *  @param  $attributes (optional) Array of attributes
         */
        function addLink( $url, $text='', $attributes=array() ) {
            
            $attributes['href'] = $url;
            
            $this->_items[] = array(
                'type' => 'link',
                'info' => sizeof( $text ) ? $text : $url,
                'attributes' => YDForm::_convertToHtmlAttrib( $attributes )
            );
            
        }
        
        /**
         *  This function adds a link to a installer action to the installer template body.
         *
         *  @param  $action     The action name
         *  @param  $text       (optional) The link text. Default: $action.
         *  @param  $attributes (optional) Array of attributes
         */
        function addLinkToAction( $action, $text='', $attributes=array() ) {
            $url = '?do=' . $action;
            $this->addLink( $url, $text, $attributes );
        }
        
        /**
         *  This function adds CSS code to the installer template header.
         *
         *  @param  $code        The CSS code
         */
        function addCss( $code ) {
            $this->_css[] = $code;
        }
        
        /**
         *  This function adds a CSS link to the installer template header.
         *
         *  @param  $url  The url.
         */
        function addCssLink( $url ) {
            $this->_css_link[] = $url;
        }
        
        /**
         *  This function adds javascript code to the template header.
         *
         *  @param  $code        The javascript code
         */
        function addJavascript( $code ) {
            $this->_js[] = $code;
        }
        
        /**
         *  This function adds a javascript link to the template header.
         *
         *  @param  $url        The url
         */
        function addJavascriptLink( $url ) {
            $this->_js_link[] = $url;
        }
        
        /**
         *  This function adds a submit button.
         *
         *  @param  $label      The label text
         *  @param  $attributes (optional) Array of attributes
         *
         *  @returns  A reference to the form element
         */
        function & addSubmit( $label, $attributes=array() ) {
            
            $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' submit' : 'submit';
            
            return $this->addField( 'submit', '_cmdSubmit_' . mt_rand( 0, 99999 ), $label, $attributes );
        }
        
        /**
         *  This function adds a button to the installer action.
         *
         *  @param  $label      The label text
         *  @param  $action     The action name
         *  @param  $attributes (optional) Array of attributes
         *
         *  @returns  A reference to the form element
         */
        function & addButtonToAction( $label, $action='', $attributes=array() ) {
            
            $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' button' : 'button';
            
            $attributes['onclick'] = "javascript:document.location.href='?do=" . $action . "';";
            
            return $this->addField( 'button', '_cmdButton_' . mt_rand( 0, 99999 ), $label, $attributes );
        }
        
        /**
         *  This function adds a button to an url.
         *
         *  @param  $label      The label text
         *  @param  $url        The url
         *  @param  $attributes (optional) Array of attributes
         *
         *  @returns  A reference to the form element
         */
        function & addButtonToLink( $label, $url='', $attributes=array() ) {
            
            $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' button' : 'button';
            
            $attributes['onclick'] = "javascript:document.location.href='" . $url . "';";
            return $this->addField( 'button', '_cmdButton_' . mt_rand( 0, 99999 ), $label, $attributes );
        }
        
        /**
         *  This function adds a button.
         *
         *  @param  $label      The label text
         *  @param  $attributes (optional) Array of attributes
         *
         *  @returns  A reference to the form element
         */
        function & addButton( $label, $attributes=array() ) {
            
            $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' button' : 'button';
            
            return $this->addField( 'button', '_cmdButton_' . mt_rand( 0, 99999 ), $label, $attributes );
        }
        
        /**
         *  This function adds navigation buttons.
         *
         *  @param  $submit_label      (optional) The submit button label. Default: 'Continue'
         *  @param  $button_label      (optional) The cancel/back button label. Default: empty (no button)
         *  @param  $button_info       (optional) The cancel/back button action or url.
         *  @param  $button_action     (optional) If true, adds a button to an installer
         *                             action, otherwise, adds a button to an url. Default: true.
         *  @param  $attributes        (optional) Array of attributes
         *
         *  @returns  A reference to the submit form element
         */
        function & addNavButtons( $submit_label='Continue', $button_label='', $button_info='', $button_action=true, $attributes=array() ) {
            
            if ( strlen( $button_label ) ) {
                if ( $button_action ) {
                    $this->addButtonToAction( $button_label, $button_info, $attributes );
                } else {
                    $this->addButtonToLink( $button_label, $button_info, $attributes );
                }
            }
            return $this->addSubmit( $submit_label, $attributes );
            
        }
        
        /**
         *  This function will add a new element to the form.
         *
         *  @param $type        The type of element to add.
         *  @param $name        The name of the form element.
         *  @param $label       (optional) The label for the form element.
         *  @param $attributes  (optional) The attributes for the form element.
         *  @param $options     (optional) The options for the elment.
         *
         *  @returns  A reference to the form element
         */
        function & addField( $type, $name, $label='', $attributes=array(), $options=array() ) {
            $this->_show_form = true;
            return $this->_form->addElement( $type, $name, $label, $attributes, $options );
        }
        
        /**
         *  This function is an alias of YDRequest::redirectToAction.
         *
         *	@param $action	(optional) Name of the action to redirect to. If no action is specified, it will redirect to
         *					the default action.
         *	@param $params	(optional) The $_GET parameters as an associative array that need to be added to the URL
         *					before redirecting. They will automatically get URL encoded.
         */
        function goTo( $action='default', $params=array() ) {
            $this->redirectToAction( $action, $params );
        }
        
        /**
         *  This function resets all persistent fields values.
         */
        function resetFields() {
            YDPersistent::set( 'YD_INSTALLER_VALUES', array(), 'YD_INSTALLER', null, true );
        }
        
        /**
         *  This function deletes the installer log file.
         */
        function resetLog() {
            @unlink( YDConfig::get( 'YD_LOG_FILE' ) );
        }
        
        /**
         *  This function sets a persistent field value.
         *
         *  @param  $name  Field name
         *  @param  $value Field value
         */
        function setField( $name, $value ) {
            
            $values = YDPersistent::get( 'YD_INSTALLER_VALUES', array(), 'YD_INSTALLER' );
            $values[ $name ] = $value;
            
            $this->$name = $value;
            
            YDPersistent::set( 'YD_INSTALLER_VALUES', $values, 'YD_INSTALLER', null, true );
        }
        
        /**
         *  This function gets a persistent field value.
         *
         *  @param  $name  Field name
         *
         *  @returns  The field value
         */
        function getField( $name ) {
            $values = YDPersistent::get( 'YD_INSTALLER_VALUES', array(), 'YD_INSTALLER' );
            return isset( $values[ $name ] ) ? $values[ $name ] : null;
        }
        
        /**
         *  This function gets a form element value.
         *
         *  @param  $name  The form element name
         *
         *  @returns  The form element value
         */
        function getValue( $name ) {
            return $this->_form->getValue( $name );
        }
        
        /**
         *  This function gets all form elements values.
         *
         *  @returns  An array with all values
         */        
        function getValues() {
            return $this->_form->getValues();
        }

        /**
         *  This function adds an error to the installer body.
         *
         *  @param  $message  The error message
         *  @param  $display  (optional) Displays the template after. Default: false
         */
        function addError( $message, $display=false ) {
            
            $this->_errors[] = $message;
            
            if ( $display ) {
                $this->display();
            }
            
        }

        /**
         *  Add a rule to the form for the specified field.
         *
         *  @param  $element    The element to apply the rule on. If you specify an array, it will add the rule for each
         *                      element in the array.
         *  @param  $rule       The name of the rule to apply.
         *  @param  $error      The error message to show if an error occured.
         *  @param  $options    (optional) The options to pass to the validator function.
         */
        function addRule( $element, $rule, $error, $options=null ) {
            $this->_form->addRule( $element, $rule, $error, $options );
        }
        
        /**
         *  Add a filter to the form for the specified field.
         *
         *  @param  $element    The element to apply the filter on. If you specify an array, it will add the filter for
         *                      each element in the array.
         *  @param  $filter     The name of the filter to apply.
         */
        function addFilter( $element, $filter ) {
            $this->_form->addFilter( $element, $filter );
        }
        
        /**
         *  Add rule that point to a custom function and is not bound to a specific form element. The callback function
         *  should return an associative array with the names of the fields and the errors. You can use the special name
         *  __ALL__ to add a form wide error.
         *
         *  @param $callback    The callback of the funtion to perform for this form rule.
         */
        function addFormRule( $callback ) {
            $this->_form->addFormRule( $callback );
        }
        
        /**
         *  Add a rule to compare different form elements with each other.
         *
         *  @param  $elements   The array of elements to compare with each other.
         *  @param  $rule       The name of the rule to apply. This can be "equal", "asc" or "desc".
         *  @param  $error      The error message to show if an error occured.
         */
        function addCompareRule( $elements, $rule, $error ) {
            $this->_form->addCompareRule( $elements, $rule, $error );
        }
        
        /**
         *  This function set the default values for the form.
         *
         *  @param $array   Associative array containing the default values.
         */
        function setDefaults( $array ) {
            $this->_form->setDefaults( $array );
        }
        
        /**
         *  This function set the default value for a form element.
         *
         *  @param  $name     Name of the form element
         *  @param  $value    Default value for the form element
         *  @param  $raw      (optional) Indicates if the value is a raw value.
         */
        function setDefault( $name, $value, $raw=false ) {
            $this->_form->setDefault( $name, $value, $raw );
        }
        
        /**
         *  This function returns if the installer step is valid.
         *
         *  @returns     True if valid, false otherwise.
         */
        function isValid() {

            // If we have errors, return false
            if ( sizeof( $this->_errors ) ) {
                return false;
            }

            // If there are no form items, return true
            if ( ! $this->_show_form ) {
                return true;
            }

            // If form is not valid, return false
            if ( ! $this->_form->isValid() ) {
                return false;
            }
            
            // Add the form values to the object
            $values = $this->getValues();
            $fields = YDPersistent::get( 'YD_INSTALLER_VALUES', array(), 'YD_INSTALLER' );
            
            foreach ( $values as $field => $value ) {
                if ( in_array( $field, array_keys( $fields ) ) ) {
                    $this->setField( $field, $value );
                }
            }
            
            return true;
            
        }
        
        /**
         *  This function is an alias of YDInstaller::isValid.
         *
         *  @returns     True if valid, false otherwise.
         */
        function validate() {
            return $this->isValid();
        }
        
        /**
         *  This function returns true if a given path is a directory.
         *
         *  @param  $path  The path.
         *
         *  @returns     True if it's a directory, false otherwise.
         */
        function isDirectory( $path ) {
            return is_dir( YDPath::getFullPath( $path ) );
        }
        
        /**
         *  This function returns true if a given path is a file.
         *
         *  @param  $path  The path.
         *
         *  @returns     True if it's a file, false otherwise.
         */
        function isFile( $path ) {
            return is_file( YDPath::getFullPath( $path ) );
        }
        
        /**
         *  This function returns true if a given path is a writeable.
         *
         *  @param  $path  The path.
         *
         *  @returns     True if it's writeable, false otherwise.
         */
        function isWriteable( $path ) {
            $dir = new YDFSDirectory( $path );
            return $dir->isWriteable();
        }
        
        /**
         *  This function returns true if a given path is a readable.
         *
         *  @param  $path  The path.
         *
         *  @returns     True if it's readable, false otherwise.
         */
        function isReadable( $path ) {
            $dir = new YDFSDirectory( $path );
            return $dir->isReadable();
        }
        
        /**
         *  This function sets the source directory for the filesystem actions.
         *
         *  @param  $path  (optional) The source directory path. Default: YD_SELF_DIR.
         */
        function setSourceDirectory( $path=YD_SELF_DIR ) {
            $this->_source_dir = YDPath::getFullPath( $path );
        }

        /**
         *  This function sets the target directory for the filesystem actions.
         *
         *  @param  $path  (optional) The target directory path. Default: YD_SELF_DIR.
         */
        function setTargetDirectory( $path=YD_SELF_DIR ) {
            $this->_target_dir = YDPath::getFullPath( $path );
        }
        
        /**
         *  This function copies a directory or an array of directories from the
         *  source directory to the target directory.
         *
         *  @param  $directories  The directory name or an array of directories
         *                        relative to the source directory.
         *  @param  $overwrite    (optional) If true, overwrite the files. Default: true.
         *  @param  $recursive    (optional) Copies all subdirectories as well. Default: true.
         *
         *  @returns     An array with the results.
         */
        function copyDirectory( $directories, $overwrite=true, $recursive=true ) {
            
            $res = array();
            
            if ( ! is_array( $directories ) ) {
                $directories = array( $directories );
            }
            
            foreach ( $directories as $directory ) {
            
                // Source directory path
                $source = YDPath::join( $this->_source_dir, $directory );
                
                // Target directory path
                $target = YDPath::join( $this->_target_dir, $directory );
                
                // Source directory
                $d = new YDFSDirectory( $source );
                
                // Source directory files
                $files = $d->getContents( '', '', array( 'YDFSFile', 'YDFSImage' ) );
                
                foreach ( $files as $k => $file ) {
                    $files[$k] = YDPath::join( $directory, $file );
                }
                
                // Copy source directory files to target
                $res = array_merge( $res, $this->copyFile( $files, $overwrite ) );
                
                if ( $recursive ) {
                    
                    // Get source subdirectories
                    $dirs = $d->getContents( '', '', 'YDFSDirectory' );
                    
                    // Cycle through all source subdirectories
                    foreach ( $dirs as $dir ) {
                        $res = array_merge( $res, $this->copyDirectory( str_replace( $this->_source_dir, '', YDPath::join( $d->getAbsolutePath(), $dir ) ), $overwrite, $recursive ) );
                    }
                    
                }
            
            }
            
            return $res;
        }
        
        /**
         *  This function copies a file or an array of files from the
         *  source directory to the target directory.
         *
         *  @param  $files        The file name or an array of files
         *                        relative to the source directory.
         *  @param  $overwrite    (optional) If true, overwrite the files. Default: true.
         *
         *  @returns     An array with the results.
         */
        function copyFile( $files, $overwrite=true ) {
            
            if ( ! is_array( $files ) ) {
                $files = array( $files );
            }
            
            $res = array();
            
            foreach ( $files as $key => $file ) {
                
                // Source file path
                $source = YDPath::join( $this->_source_dir, $file );
                
                // Target file path
                $target = YDPath::join( $this->_target_dir, $file );
                
                // Target directory path
                $target_dir = YDPath::getDirectoryName( $target );
                
                // If target file exists and $overwrite is false go to next file
                if ( is_file( $target ) && ! $overwrite ) {
                    
                    $res[] = array( 'success' => false, 'action' => 'copy', 'type' => 'file', 'item' => $target, 'reason' => 'exist' );
                    if ( $this->_log ) YDLog::warning( 'Copying file: ' . $target . ' - already exist' );
                    continue;
                }
                
                // Create the target directory if it doesn't exist
                if ( ! is_dir( $target_dir ) ) {
                    
                    $res = array_merge( $res, $this->createDirectory( str_replace( $this->_target_dir, '', $target_dir ) ) );
                    
                }

                // Copy the file
                if ( ! $success = @copy( $source, $target ) ) {
                    if ( $this->_log ) YDLog::error( 'Copying file: ' . $source . ' -> ' . $target );
                } else {
                    if ( $this->_log ) YDLog::info( 'Copying file: ' . $source . ' -> ' . $target );
                }
                
                $res[] = array( 'success' => $success, 'action' => 'copy', 'type' => 'file', 'item' => $target, 'reason' => 'failure' );
                
            }
            
            return $res;
            
        }
        
        /**
         *  This function copies a file or an array of files using a pattern from the
         *  source directory to the target directory. Example: *.txt
         *
         *  @param  $files        The file pattern or an array of file patterns
         *                        relative to the source directory.
         *  @param  $overwrite    (optional) If true, overwrite the files. Default: true.
         *
         *  @returns     An array with the results.
         */
        function copyFileByPattern( $files, $overwrite=true ) {
            
            if ( ! is_array( $files ) ) {
                $files = array( $files );
            }
            
            $res = array();
            
            foreach ( $files as $key => $pattern ) {
                
                // Get the directory out of the pattern
                $pattern = str_replace( '/', YDPath::getDirectorySeparator(), $pattern );
                $pattern = str_replace( '\\', YDPath::getDirectorySeparator(), $pattern );
                
                $pos = strrpos( $pattern, YDPath::getDirectorySeparator() );
                
                $dir = '';
                if ( $pos ) {
                    $dir = substr( $pattern, 0, $pos+1 );
                    $pattern = substr( $pattern, $pos+1, strlen( $pattern )-$pos );
                }
                
                // Get the directory files with the pattern
                $d = new YDFSDirectory( YDPath::join( $this->_source_dir, $dir ) );
                $fs = $d->getContents( $pattern, '', 'YDFSFile' );
                
                // Copy the files 
                foreach ( $fs as $file ) {
                    $res = array_merge( $res, $this->copyFile( YDPath::join( $dir, $file ), $overwrite ) );
                }
                
            }
            
            return $res;
            
        }
        
        /**
         *  This function formats an array of results that were returned by the
         *  filesystem methods.
         *
         *  @param  $results  The array of results
         *
         *  @returns     The formatted string.
         */
        function getReport( $results ) {
            
            $msg = '';
            foreach ( $results as $f ) {
                if ( $f['success'] ) {
                    $msg .= 'Success ';
                } else {
                    $msg .= 'Failed ';
                }
                if ( $f['action'] == 'delete' ) {
                    $msg .= 'deleting ';
                } else if ( $f['action'] == 'create' ) {
                    $msg .= 'creating ';
                } else {
                    $msg .= 'copying ';
                }
                $msg .= $f['type'] . ' "' . str_replace( $this->_target_dir, '', $f['item'] ) . '"';
                
                if ( ! $f['success'] ) {
                    if ( $f['reason'] == 'failure' ) {
                        $msg .= ' - system failure';
                    } else {
                        if ( $f['action'] == 'delete' ) {
                            $msg .= ' - doesn\'t exist';
                        } else {
                            $msg .= ' - already exist';
                        }
                    }
                }
                
                $msg .= YD_CRLF;
            }
            
            return $msg;
            
        }
        
        /**
         *  This function creates a directory or an array of directories at the
         *  target directory.
         *
         *  @param  $directories  The directory name or an array of directories names
         *                        relative to the target directory.
         *  @param  $mode         (optional) Default: 0700.
         *
         *  @returns     An array with the results.
         */
        function createDirectory( $directories, $mode=0700 ) {
            
            if ( ! is_array( $directories ) ) {
                $directories = array( $directories );
            }
            
            $res = array();
            
            foreach ( $directories as $directory ) {
                
                // Target directory
                $directory = YDPath::join( $this->_target_dir, $directory );
                
                // If already defined, continue to next directory
                if ( is_dir( $directory ) ) {
                    $res[] = array( 'success' => false, 'action' => 'create', 'type' => 'directory', 'item' => $directory, 'reason' => 'exist' );
                    if ( $this->_log ) YDLog::warning( 'Creating directory: ' . $directory . ' - already exist' );
                    continue;
                }
                
                // Get the parent directory
                $d = new YDFSDirectory( YDPath::getDirectoryName( $directory ) );
                
                if ( ! $success = $d->createDirectory( $directory ) ) {
                    if ( $this->_log ) YDLog::error( 'Creating directory: ' . $directory );
                } else {
                    if ( $this->_log ) YDLog::info( 'Creating directory: ' . $directory );
                }
                
                $res[] = array( 'success' => $success, 'action' => 'create', 'type' => 'directory', 'item' => $directory, 'reason' => 'failure' );
            
            }
            
            return $res;
            
        }
        
        /**
         *  This function deletes a directory or an array of directories from the
         *  target directory.
         *
         *  @param  $directories  The directory name or an array of directories names
         *                        relative to the target directory.
         *
         *  @returns     An array with the results.
         */
        function deleteDirectory( $directories ) {
            
            if ( ! is_array( $directories ) ) {
                $directories = array( $directories );
            }
            
            $res = array();
            
            foreach ( $directories as $directory ) {
                
                // The directory path
                $directory = YDPath::join( $this->_target_dir, $directory );
                
                if ( ! is_dir( $directory ) ) {
                    $res[] = array( 'success' => false, 'action' => 'delete', 'type' => 'directory', 'item' => $directory, 'reason' => 'exist' );
                    if ( $this->_log ) YDLog::warning( 'Deleting directory: ' . $directory . ' - already exist' );
                    continue;
                }
                
                // The directory
                $d = new YDFSDirectory( $directory );
                
                // Delete the directory
                if ( ! $success = $d->deleteDirectory( $directory ) ) {
                    if ( $this->_log ) YDLog::error( 'Deleting directory: ' . $directory );
                } else {
                    if ( $this->_log ) YDLog::info( 'Deleting directory: ' . $directory );
                }
                
                $res[] = array( 'success' => $success, 'action' => 'delete', 'type' => 'directory', 'item' => $directory, 'reason' => 'failure' );
            }
            
            return $res;
        }
        
        /**
         *  This function deletes a file or an array of files from the
         *  target directory.
         *
         *  @param  $files  The file name or an array of filenames
         *                  relative to the target directory.
         *
         *  @returns     An array with the results.
         */
        function deleteFile( $files ) {
            
            if ( ! is_array( $files ) ) {
                $files = array( $files );
            }
            
            $res = array();
            
            foreach ( $files as $file ) {
                
                // The file path
                $file = YDPath::join( $this->_target_dir, $file );
                
                if ( ! is_file( $file ) ) {
                    $res[] = array( 'success' => false, 'action' => 'delete', 'type' => 'file', 'item' => $file, 'reason' => 'exist' );
                    if ( $this->_log ) YDLog::warning( 'Deleting file: ' . $file . ' - already exist' );
                    continue;
                }
                
                // Target directory path
                $dir = YDPath::getDirectoryName( $file );
                
                // The directory
                $d = new YDFSDirectory( $dir );
                
                // Delete the file
                if ( ! $success = $d->deleteFile( $file ) ) {
                    if ( $this->_log ) YDLog::error( 'Deleting file: ' . $file );
                } else {
                    if ( $this->_log ) YDLog::info( 'Deleting file: ' . $file );
                }
                
                $res[] = array( 'success' => $success, 'action' => 'delete', 'type' => 'file', 'item' => $file, 'reason' => 'failure' );
            }
            
            return $res;
            
        }

        /**
         *  This function deletes a file or an array of files using a pattern
         *  from the target directory.
         *
         *  @param  $files  The file pattern or an array of file patterns
         *                  relative to the target directory.
         *
         *  @returns     An array with the results.
         */
        function deleteFileByPattern( $files ) {
            
            if ( ! is_array( $files ) ) {
                $files = array( $files );
            }
            
            $res = array();
            
            foreach ( $files as $key => $pattern ) {
                
                // Get the directory out of the pattern
                $pattern = str_replace( '/', YDPath::getDirectorySeparator(), $pattern );
                $pattern = str_replace( '\\', YDPath::getDirectorySeparator(), $pattern );
                
                $pos = strrpos( $pattern, YDPath::getDirectorySeparator() );
                
                $dir = '';
                if ( $pos ) {
                    $dir = substr( $pattern, 0, $pos+1 );
                    $pattern = substr( $pattern, $pos+1, strlen( $pattern )-$pos );
                }
                
                // Get the files based on the pattern
                $d = new YDFSDirectory( YDPath::join( $this->_target_dir, $dir ) );
                $fs = $d->getContents( $pattern, '', 'YDFSFile' );
                
                // Delete the files
                foreach ( $fs as $file ) {
                    $res = array_merge( $res, $this->deleteFile( YDPath::join( $dir, $file ) ) );
                }
                
            }
            
            return $res;
            
        }
        
        
    }

?>
