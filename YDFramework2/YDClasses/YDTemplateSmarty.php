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
     *
     *  @todo
     *      Implement new error mechanism.
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

?>