<?php

    /*
     *  This examples demonstrates:
     *  - How to use the PEAR Menu class to create a custom menu.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once 'HTML/Menu.php';

    // Class definition
    class htmlmenu1Request extends YDRequest {

        // Class constructor
        function htmlmenu1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // The data for our menu
            $data = array(
                1 => array(
                    'title' => 'Menu item 1', 
                    'url' => '/item1.php',
                    'sub' => array(
                        11 => array('title' => 'Menu item 1.1', 'url' => '/item1.1.php'),
                        12 => array(
                            'title' => 'Menu item 1.2', 
                            'url' => '/item1.2.php',
                            'sub' => array(
                                121 => array('title' => 'Menu item 1.2.1', 'url' => '/item1.2.1.php'),
                                122 => array('title' => 'Menu item 1.2.2', 'url' => '/item1.2.2.php')
                            )
                        )
                    )
                ),
                2 => array(
                    'title' => 'Menu item 2', 
                    'url' => '/item2.php',
                    'sub' => array(
                        21 => array('title' => 'Menu item 2.1', 'url' => '/item2.1.php'),
                        22 => array('title' => 'Menu item 2.2', 'url' => '/item2.2.php')
                    )
                )
            );

            // Instantiate the menu object, we presume that $data contains menu structure
            $menu = & new HTML_Menu( $data, 'sitemap' );

            // Output the menu
            $menu->show();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>