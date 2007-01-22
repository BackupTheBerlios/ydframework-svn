<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

        This library is free software; you can redistribute it and/or
        modify it under the terms of the GNU Lesser General Public
        License as published by the Free Software Foundation; either
        version 2.1 of the License, or (at your option) any later version.

        This library is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
        Lesser General Public License for more details.

        You should have received a copy of the GNU Lesser General Public
        License along with this library; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

    */

    /**
     *  @addtogroup YDForm Core - Form
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDForm.php');

    /**
     *	This is the class that define a switch menu form element
     *
     *  @ingroup YDForm
     */
    class YDFormElement_SwitchMenu extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_SwitchMenu class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The menu elements.
         */
        function YDFormElement_SwitchMenu( $form = 'YDForm', $name = 'SMenu', $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'switchmenu';
			
			// Get elements
			$this->_elements = $options;
        }


        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'name' => $this->_form . '_' . $this->_name
            );

            $attribs = array_merge( $this->_attributes, $attribs );
            
            // Get the HTML
            $out = '';

			// if js code was not loaded we must load
            if ( ! defined( 'YD_SWMENU_MAINSCRIPT' ) ) {
            	$out .= trim( '<script type="text/javascript">' );
            	$out .= trim( 'function hideSMenu( menudiv, obj ){' );
            	$out .= trim( '	var ar = document.getElementById( menudiv ).getElementsByTagName( "span" );' );
            	$out .= trim( '		if( document.getElementById( obj ).style.display != "block" ){' );
            	$out .= trim( '			for ( var i = 0; i < ar.length; i++ ){' );
            	$out .= trim( '				if ( ar[i].className == "submenu" )' );
            	$out .= trim( '					ar[i].style.display = "none";' );
            	$out .= trim( '			}' );
            	$out .= trim( '			document.getElementById( obj ).style.display = "block";' );
            	$out .= trim( '		}else{' );
            	$out .= trim( '			document.getElementById( obj ).style.display = "none";' );
            	$out .= trim( '		}' );
            	$out .= trim( '}' );
            	$out .= trim( '</script>' );
                define( 'YD_SWMENU_MAINSCRIPT', 1 );
            }

			// create menu div
			$out .= '<div'. YDForm::_convertToHtmlAttrib( $attribs ) . '>';
			
			// create menu id
			$IDmenu = $this->getAttribute('id');
			$i = 1;
			
			// cycle menus
			foreach ( $this->_elements as $_title => $_elements ){

				// create submenu id
				$IDsubmenu = $IDmenu . '_' . $i++;
							
				// create menu div
				$out .= "<div class=\"menutitle\" onclick=\"hideSMenu('" . $IDmenu . "', '" . $IDsubmenu . "')\">" . $_title . "</div>";

				// create span that includes all menu subitems
				$out .= '<span class="submenu" id="' . $IDsubmenu . '" style="display:none">';
				$out .= implode( '<br>', $_elements );
				$out .= '</span>';
        	}
			
			// add final menu div tag
			$out .= '</div>';

			return $out;
		}


        /**
         *	This function adds custom css to the template
         *
         *	@param $template		The template object 
         */
		function addCss( & $template ){

			$template->addCss( '.menutitle{ cursor:pointer;margin-bottom: 5px;background-color:#ECECFF;color:#000000;width:140px;padding:2px;text-align:center;font-weight:bold;border:1px solid #000000; }' );
			$template->addCss( '.submenu{ margin-bottom: 0.5em; }' );
			
		}


        /**
         *	This function adds a new menu item
         *
         *	@param $name		The name of the menu
         */
		function addMenu( $name ){

			// add menu to elements
			$this->_elements[ $name ] = array();
		}


        /**
         *	This function adds a new element to a menu item
         *
         *	@param $menu		The name of the menu to assign the element
         *	@param $obj			The element form element object (or a simple string)
         */
		function addElement( $menu, $obj ){
		
			// if menu don't exist we should create one
			if ( !isset( $this->_elements[ $menu ] ) ) $this->_elements[ $menu ] = array();

			// if element is a form object we must get its html code
			if ( !is_string( $obj )) $obj = $obj->toHtml();

			// add this element to menu
			array_push( $this->_elements[ $menu ], $obj );		
		}


    }

?>