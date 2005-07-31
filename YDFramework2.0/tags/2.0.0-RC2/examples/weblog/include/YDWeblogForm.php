<?php

    // The form class to use for the weblog
    class YDWeblogForm extends YDForm {

        // Class constructor
        function YDWeblogForm( $name, $method='post', $action='', $target='_self', $attributes=array() ) {

            // Initialize the parent
            $this->YDForm( $name, $method, $action, $target, $attributes );

            // Set the required
            $this->setHtmlRequired( '', ' <font color="red">(' . t( 'required' ) . ')</font>' );

            // Register the form element
            $this->registerElement( 'wlbbtextarea', 'YDWeblogFormElement_BBTextArea' );
            $this->registerElement( 'wladmintextarea', 'YDFormElement_AdminTextArea' );

        }

    }

    // A BB Text Area form element
    class YDWeblogFormElement_BBTextArea extends YDFormElement_BBTextArea {

        // Class constructor
        function YDWeblogFormElement_BBTextArea( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement_BBTextArea( $form, $name, $label, $attributes, $options );

            // Create the toolbar
            $this->clearButtons();
            $this->addModifier( 'b', 'bold' );
            $this->addModifier( 'i', 'italic' );
            $this->addSimplePopup( 'url',   'url',   t( 'ta_enter_url' ) . ':',   'http://' );
            $this->addSimplePopup( 'email', 'email', t( 'ta_enter_email' ) . ':' );
            $this->addSimplePopup( 'img',   'img',   t( 'ta_enter_image' ) . ':', 'http://' );

        }

    }

    // Custom bbtext area
    class YDFormElement_AdminTextArea extends YDWeblogFormElement_BBTextArea {

        // Class constructor
        function YDFormElement_AdminTextArea( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDWeblogFormElement_BBTextArea( $form, $name, $label, $attributes, $options );

            // Create the base url
            $baseurl = 'images.php?do=selectorImages&field=' . rawurlencode( $form . '_' . $name );

            // Parameters for the window
            $params = 'width=880,height=660,location=0,menubar=0,resizable=1,scrollbars=1,status=1,titlebar=1';

            // Create the toolbar
            $this->clearButtons();
            $this->addModifier( 'b', 'bold' );
            $this->addModifier( 'i', 'italic' );
            $this->addSimplePopup( 'url',   'url',   t( 'ta_enter_url' ) . ':',   'http://' );
            $this->addSimplePopup( 'email', 'email', t( 'ta_enter_email' ) . ':' );
            $this->addPopupWindow( $baseurl, strtolower( t('select_image') ), 'YDImageSelector', $params );

        }

    }

?>