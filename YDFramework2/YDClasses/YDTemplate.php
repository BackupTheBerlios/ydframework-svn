<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    /**
     *  This class implements an template object and does this by extending the
     *  Smarty class. For more information about Smarty, please take a look at
     *  http://smarty.php.net/ which contains a reference about all the tags
     *  that can be used in the templates.
     *
     *  In order to make this work, you will need to make the temp directory
     *  under the YDFramework2 directory world-writeable as this directory is
     *  used to store the compiled templates.
     *
     *  Unless changed manually, the template engine will look in the same
     *  directory as your script to find the template. The templates all share
     *  the same file extension, which is '.tpl'. By using the setTemplateDir
     *  function, you can change this and indicate which directory needs to be
     *  looked in for finding the template files.
     *  
     *  @remark
     *      Try to put as much logic as possible in the code, and try to avoid
     *      putting most logic in the templates. Keeping the templates as simple
     *      as possible will increase performance and maintainebility of your
     *      code.
     *
     *  @remark
     *      The YDSmartyPlugins folder which can be found under the YDFramework2
     *      directory is automatically configure as a search path for blocks,
     *      modifiers, plugins etc. for Smarty. Please put all the custom
     *      Smarty items in this directory. This will make it easier for you to
     *      upgrade Smarty to a new version if needed. If there is a folder
     *      called "includes" in the current script's directory, this will also
     *      be searched in for plugins.
     *
     *  @internal
     */
    class YDTemplateSmarty extends Smarty {

        /**
         *  This is the class constructor for the YDTemplateSmarty class. It 
         *  does not take any arguments and is smart enough to configure itself.
         *  By default, it looks in the same directory as the current script to
         *  find the templates.
         *
         *  The temporary directory is set to the YDFramework2/temp directory
         *  and needs to be world writeable. Debbuging is determined based on
         *  YD_DEBUG parameter and caching is disabled.
         */
        function YDTemplateSmarty() {

            // Initialize the Smarty class
            $this->Smarty();

            // Configure Smarty
            $this->config_dir = YD_DIR_3RDP_SMARTY . '/configs';
            $this->template_dir = YD_SELF_DIR;
            $this->compile_dir = YD_DIR_TEMP;
            $this->caching = false;
            $this->use_sub_dirs = false;
            $this->compile_id = md5( YD_SELF_DIR );

            // Add an extra plugins directory
            $this->plugins_dir = array(
                YD_DIR_HOME . '/YDSmartyPlugins',
                YD_DIR_3RDP_SMARTY . '/plugins',
            );

            // Add the includes directory if any
            if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
                array_push( $this->plugins_dir, YD_PATHDELIM . YD_SELF_DIR );
            }

        }

        /**
         *  We create a custom version of Smarty's temporary filename generator
         *  to make it support the fact that we want to put all the compiled
         *  templates from different directories in the same temp directory.
         *
         *  First thing we do it to change the compile_id parameter to include
         *  the current directory. To shorten the names of the files, we perform
         *  an md5 checksum on them so that they are fixed in length.
         *
         *  @internal
         *
         *  @param $auto_base
         *  @param $auto_source
         *  @param $auto_id
         *
         *  @return string
         */
        function _get_auto_filename(
            $auto_base, $auto_source = null, $auto_id = null
        ) {

            $_compile_dir_sep =  $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';

            if(@is_dir($auto_base)) {
                $_return = $auto_base . DIRECTORY_SEPARATOR;
            } else {
                $_params = array('file_path' => $auto_base);
                require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.get_include_path.php');
                smarty_core_get_include_path($_params, $this);
                $_return = isset($_params['new_file_path']) ? $_params['new_file_path'] . DIRECTORY_SEPARATOR : null;
            }

            $fileid = '';

            if(isset($auto_id)) {
                $auto_id = str_replace('%7C',$_compile_dir_sep,(urlencode($auto_id)));
                $fileid .= $auto_id . $_compile_dir_sep;
            }

            if(isset($auto_source)) {
                $_filename = urlencode(basename($auto_source));
                $_crc32 = crc32($auto_source) . $_compile_dir_sep;
                $_crc32 = '%%' . substr($_crc32,0,3) . $_compile_dir_sep . '%%' . $_crc32;
                $fileid .= $_crc32 . $_filename;
            }

            return $_return . YD_TMP_PRE . md5( $fileid );

        }

    }

    /**
     *  This class implements an template object and does this by using the
     *  YDTemplateSmarty class.
     *
     *  In order to make this work, you will need to make the temp directory
     *  under the YDFramework2 directory world-writeable as this directory is
     *  used to store the compiled templates.
     *
     *  Unless changed manually, the template engine will look in the same
     *  directory as your script to find the template. The templates all share
     *  the same file extension, which is '.tpl'. By using the setTemplateDir
     *  function, you can change this and indicate which directory needs to be
     *  looked in for finding the template files.
     *  
     *  @remark
     *      Try to put as much logic as possible in the code, and try to avoid
     *      putting most logic in the templates. Keeping the templates as simple
     *      as possible will increase performance and maintainebility of your
     *      code.
     *
     *  @remark
     *      The YDSmartyPlugins folder which can be found under the YDFramework2
     *      directory is automatically configure as a search path for blocks,
     *      modifiers, plugins etc. for Smarty. Please put all the custom
     *      Smarty items in this directory. This will make it easier for you to
     *      upgrade Smarty to a new version if needed. If there is a folder
     *      called "includes" in the current script's directory, this will also
     *      be searched in for plugins.
     */
    class YDTemplate extends YDBase {

        /**
         *  This is the class constructor for the YDTemplate class. It does not 
         *  take any arguments and is smart enough to configure itself. By 
         *  default, it looks in the same directory as the current script to
         *  find the templates.
         *
         *  The temporary directory is set to the YDFramework2/temp directory
         *  and needs to be world writeable. Debbuging is determined based on
         *  YD_DEBUG parameter and caching is disabled.
         */
        function YDTemplate() {

            // Initialize YDBase
            $this->YDBase();

            // Instantiate the template object
            $this->tpl = new YDTemplateSmarty();

        }

        /**
         *  This function adds a variable to the template and gives it the
         *  specified name.
         *
         *  @param $name Name you want to use for this variable for referencing
         *               it in the template.
         *  @param $value The value of the variable you want to add.
         */
        function setVar( $name, $value ) {
            $this->tpl->assign( $name, $value );
        }

        /**
         *  This function is used to change the directory in which to look for
         *  the template files. Make sure the directory exists and is writeable.
         *
         *  If the directory cannot be found, a fatal error is raised and the
         *  execution is stopped.
         *
         *  @param $dir Directory you want to use as the template directory.
         *              This can be a relative or absolute path. An absolute 
         *              path is the preferred way of working.
         */
        function setTemplateDir( $dir ) {

            // Check if the directory exists
            if ( ! is_dir( $dir ) ) {
                new YDFatalError(
                    'Cannot use the directory "' . $dir . '" as the template '
                    . 'directory because the directory could not be found'
                );
            }
            
            // Set the template directory
            $this->tpl->templateDir = realpath( $dir );

        }

        /**
         *  This function will parse the template and will return the parsed
         *  contents. The name of the template you need to specify is the
         *  basename of the template without the file extension. So, if you want
         *  to use the template file called "mytemplate.tpl", this function call
         *  will look as follows:
         *
         *  @code
         *  $template->getOutput( 'mytemplate' );
         *  @endcode
         *
         *  This function call will cause the template engine to look in the
         *  defined template directory for a file called "mytemplate.tpl" and
         *  it will parse this template and output the parsed contents.
         *
         *  This function automatically adds some variables to the template,
         *  which you can use as well in the template:
         *
         *  - YD_FW_NAME:      name of the framework, e.g. "Yellow Duck Framework"
         *  - YD_FW_VERSION:   version of the framework, e.g. "2.0.0"
         *  - YD_FW_NAMEVERS:  the combination of the two items above, e.g.
         *                     "Yellow Duck Framework 2.0.0"
         *  - YD_SELF_SCRIPT:  Contains the current script's path, e.g.
         *                     "/myapp/index.php"
         *  - YD_SELF_URI:     The URI which was given in order to access this
         *                     page, e.g. "/myapp/index.php?do=edit"
         *  - YD_ACTION_PARAM: the name of the $_GET parameter that specifies
         *                     which action needs to be executed. This is "do"
         *                     by default.
         *
         *  - YD_ENV:     These variables are imported into PHP's global
         *                namespace from the environment under which the PHP
         *                parser is running.
         *  - YD_COOKIE:  An associative array of variables passed to the
         *                current script via HTTP cookies.
         *  - YD_GET:     An associative array of variables passed to the
         *                current script via the HTTP GET method. .
         *  - YD_POST:    An associative array of variables passed to the 
         *                current script via the HTTP POST method. 
         *  - YD_FILES:   An associative array of items uploaded to the current
         *                script via the HTTP POST method. 
         *  - YD_REQUEST: An associative array consisting of the contents of
         *                YD_GET, YD_POST, and YD_COOKIE. 
         *  - YD_SESSION: An associative array containing session variables
         *                available to the current script.
         *  - YD_GLOBALS: An associative array containing references to all
         *                variables which are currently defined in the global
         *                scope of the script.
         *
         *  @param $name The name of the template you want to parse and output.
         *
         *  @returns This function returns the output of the parsed template.
         */
        function getOutput( $name ) {

            // Check if the template directory is writeable
            if ( ! is_writable( $this->tpl->compile_dir ) ) {
                new YDFatalError(
                    'The directory "' . $this->tpl->compile_dir . '" should be '
                    . 'world writable for the Yellow Duck Framework to work '
                    . 'properly.'
                );
            }

            // Add some default variables
            $this->setVar( 'YD_FW_NAME', YD_FW_NAME );
            $this->setVar( 'YD_FW_VERSION', YD_FW_VERSION );
            $this->setVar( 'YD_FW_NAMEVERS', YD_FW_NAMEVERS );
            $this->setVar( 'YD_SELF_SCRIPT', YD_SELF_SCRIPT );
            $this->setVar( 'YD_SELF_URI', YD_SELF_URI );
            $this->setVar( 'YD_ACTION_PARAM', YD_ACTION_PARAM );

            // Add PHP variables
            $this->setVar( 'YD_ENV', $_ENV );
            $this->setVar( 'YD_COOKIE', $_COOKIE );
            $this->setVar( 'YD_GET', $_GET );
            $this->setVar( 'YD_POST', $_POST );
            $this->setVar( 'YD_FILES', $_FILES );
            $this->setVar( 'YD_REQUEST', $_REQUEST );
            $this->setVar( 'YD_SESSION', $_SESSION );
            $this->setVar( 'YD_GLOBALS', $GLOBALS );
            $this->setVar( 'YD_SERVER', $_SERVER );

            // Get the output from the template
            return $this->tpl->fetch( $name . YD_TPL_EXT );

        }

    }

?>