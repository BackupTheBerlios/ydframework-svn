<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Includes
    require_once( 'YDBase.php' );
    require_once( 'YDError.php' );
    require_once( 'Savant.php' );

    /**
     *  This class implements an template object.
     *
     *  Unless changed manually, the template engine will look in the same
     *  directory as your script to find the template. The templates all share
     *  the same file extension, which is '.tpl'. By using the setTemplateDir
     *  function, you can change this and indicate which directory needs to be
     *  looked in for finding the template files.
     */
    class YDTemplate extends YDBase {

        /**
         *  This is the class constructor for the YDTemplate class. It does not
         *  take any arguments and is smart enough to configure itself. By
         *  default, it looks in the same directory as the current script to
         *  find the templates.
         */
        function YDTemplate() {

            // Initialize YDBase
            $this->YDBase();

            // Instantiate the template object
            $this->_tpl = & new Savant();

            // Add the default template directory
            $this->setTemplateDir( YD_SELF_DIR );

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
            $this->_tpl->assign( $name, $value );
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
            $this->_tpl->addPath( 'template', $dir );

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
         *  - YD_FW_NAME:      name of the framework
         *  - YD_FW_VERSION:   version of the framework
         *  - YD_FW_NAMEVERS:  the combination of the two items above
         *  - YD_FW_HOMEPAGE:  the homepage of the Yellow Duck Framework
         *  - YD_SELF_SCRIPT:  Contains the current script's path, e.g.
         *                     "/myapp/index.php"
         *  - YD_SELF_URI:     The URI which was given in order to access this
         *                     page, e.g. "/myapp/index.php?do=edit"
         *  - YD_ACTION_PARAM: the name of the $_GET parameter that specifies
         *                     which action needs to be executed. This is "do"
         *                     by convention.
         *
         *  - YD_ENV:     These variables are imported into PHP's global
         *                namespace from the environment under which the PHP
         *                parser is running.
         *  - YD_COOKIE:  An associative array of variables passed to the
         *                current script via HTTP cookies.
         *  - YD_GET:     An associative array of variables passed to the
         *                current script via the HTTP GET method.
         *  - YD_POST:    An associative array of variables passed to the
         *                current script via the HTTP POST method.
         *  - YD_FILES:   An associative array of items uploaded to the current
         *                script via the HTTP POST method.
         *  - YD_REQUEST: An associative array consisting of the contents of
         *                YD_GET, YD_POST and YD_COOKIE.
         *  - YD_SESSION: An associative array containing session variables
         *                available to the current script.
         *  - YD_GLOBALS: An associative array containing references to all
         *                variables which are currently defined in the global
         *                scope of the script.
         *
         *  - YD_DB_CONN_CNT: Number of database connections that were made.
         *  - YD_DB_SQLQ_CNT: Number of database queries that were performed.
         *
         *  @param $name The name of the template you want to parse and output.
         *
         *  @returns This function returns the output of the parsed template.
         *
         *  @todo
         *      We should only append the template extension is it's not done
         *      yet by the user itself. Now, you always have to remember not to
         *      specify the template extension.
         */
        function getOutput( $name ) {

            // Add some default variables
            $this->setVar( 'YD_FW_NAME', YD_FW_NAME );
            $this->setVar( 'YD_FW_VERSION', YD_FW_VERSION );
            $this->setVar( 'YD_FW_NAMEVERS', YD_FW_NAMEVERS );
            $this->setVar( 'YD_FW_HOMEPAGE', YD_FW_HOMEPAGE );
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

            // Add the database connection count
            if ( isset( $GLOBALS['YD_DB_CONN_CNT'] ) ) {
                $this->setVar( 'YD_DB_CONN_CNT', $GLOBALS['YD_DB_CONN_CNT'] );
            } else {
                $this->setVar( 'YD_DB_CONN_CNT', 0 );
            }

            // Add the database qeury count
            if ( isset( $GLOBALS['YD_DB_SQLQ_CNT'] ) ) {
                $this->setVar( 'YD_DB_SQLQ_CNT', $GLOBALS['YD_DB_SQLQ_CNT'] );
            } else {
                $this->setVar( 'YD_DB_SQLQ_CNT', 0 );
            }

            // Get the output from the template
            return $this->_tpl->fetch( $name . YD_TPL_EXT );

        }

    }

?>