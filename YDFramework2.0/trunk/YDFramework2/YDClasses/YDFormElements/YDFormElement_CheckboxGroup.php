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
    include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Checkbox.php');

    // Custom selectall name
    YDConfig::set( 'YD_FORMELEMENT_CHKGROUP_SELALL', null, false ); 

    /**
     *	This is the class that define a checkbox form element.
     *
     *  @ingroup YDForm
     */
    class YDFormElement_CheckboxGroup extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Checkbox class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the element.
         */
        function YDFormElement_CheckboxGroup( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'checkboxgroup';

            // set default separator and default position
            $this->_separator = '<br />';
            $this->_position  = 'right';

            // parse separator and position from array
            if ( isset ( $attributes['sep'] ) ) {

                // find horizontal and left tags
                if ( is_int( strpos( $attributes[ 'sep' ], 'h' ) ) ) $this->_separator = ' ';
                if ( is_int( strpos( $attributes[ 'sep' ], 'l' ) ) ) $this->_position  = 'left';
                unset( $attributes[ 'sep' ] );

            }

            // we can even have a custom separator
            if ( isset ( $attributes[ 'separator' ] ) ){
                $this->_separator = $attributes[ 'separator' ];
                unset( $attributes[ 'separator' ] );
            }

            // Add the subitems
            $this->_items = array();
            foreach ( $this->_options as $key=>$val ) {
                $item = new YDFormElement_Checkbox( $form, $name . '[' . $key . ']', $val, $attributes );
                $this->_items[ $key ] = $item;

                // delete attribute. otherwise we would get a 'sep' attribute in html
                $item->delAttribute( 'sep' );
            }

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate that the label should be appended
            $this->_placeLabel = 'before';

            $this->_addSelectAll = false;
            $this->_addSelectAll_chk_attributes = array();
            $this->_addSelectAll_label_attributes = array();
            

            // check columns definition
            if ( isset ( $attributes[ 'columns' ] ) ){
                $this->_columns = $attributes[ 'columns' ];
                unset( $attributes[ 'columns' ] );
            }else{
                $this->_columns = 1;
            }
        }


        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            foreach ( $this->_items as $item ) {
                if ( $itm->isModified() === true ) {
                    return true;
                }
            }
            return false;
        }

        /**
         *	This function sets the value for the element.
         *
         *	@param	$val	(optional) The value for this object as array or a key integer for selection.
         */
        function setValue( $val=array() ) {

            // check if is numeric
            if ( is_integer( $val ) ){
                $this->_items[ intval( $val ) ]->setValue( 1 );
            }elseif ( is_array( $val ) ){
                foreach ( $val as $k=>$v ) {

                    // if checkbox checkbox exist, set value
                    if ( isset( $this->_items[$k] ) ){
                        $this->_items[$k]->setValue( $v );
                    }
                }
            }elseif( isset( $this->_items[ strval( $val ) ] ) ){
				$this->_items[ strval( $val ) ]->setValue( 1 );
			}
        }


        /**
         *	This function defines the columns to export
         *
         *	@param	$total	Total of columns
         */
        function setColumns( $total ) {
			$this->_columns = $total;
        }


        /**
         *	This function sets the attribute for the element.
         *
         *	@param	$attribute	Attribute to change
         *	@param	$value   	Attribute value
         */
        function setAttribute( $attribute, $value ) {
            foreach ( $this->_items as $k=>$v ) {
                $this->_items[$k]->setAttribute( $attribute, $value );
            }
        }


        /**
         *	This function sets checkboxgroup with 'select all' button
         *
         *	@param	$onBottom			(Optional) Boolean that defines if button should be added on bottom (TRUE) or on top (FALSE)
         *	@param	$chk_attributes		(Optional) Attributes to pass to the select all checkbox
         */
        function addSelectAll( $onBottom = true, $chk_attributes = array() ) {
            $this->_addSelectAll = true;
            $this->_addSelectAll_onBottom = $onBottom;
            $this->_addSelectAll_chk_attributes = $chk_attributes;
        }


        /**
         *	This function disables checkboxes
         *
         *	@param	$options	Value, Array of values to disable
         */
        function disable( $options ) {

            // if null disable all elements
            if ( is_null ( $options ) ){
                foreach ( $this->_items as $k => $item )
                    if ( isset( $this->_items[$k] ) ) $this->_items[$k]->disable();
                return;
            }

            // check if options is array
            if ( ! is_array( $options ) ) $options = array( $options );

            // disable specific elements
            foreach( $options as $k )
                if ( isset( $this->_items[$k] ) ) $this->_items[$k]->disable();
        }


        /**
         *      This function returns the value of the element.
         *
         *      @returns        Value of this object.
         */
        function getValue() {
            $values = array();
            foreach ( $this->_items as $key=>$item ) {
                $values[ $key ] = $item->getValue();
            }
            return $values;
        }

        /**
         *	This function sets the raw value for the element.
         *
         *	@param	$val	(optional) The value for this object.
         */
        function setRawValue( $val='' ) {
            foreach ( $val as $k=>$v ) {
                $this->_items[$k]->setRawValue( $v );
            }
        } 

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Output the HTML checkboxes
            $output = array();

            // cycle all items to get their html
            foreach ( $this->_items as $item )
                if ( $this->_position == 'right' ) $output[] = $item->toHtml() . '&nbsp;<label for="' . $item->_attributes['id'] . '">' . $item->_label . '</label>';
                else                               $output[] = '<label for="' . $item->_attributes['id'] . '">' . $item->_label . '</label>&nbsp;' . $item->toHtml();

            // Add the <nobr> tags
            foreach ( $output as $key => $val ) {
                $output[$key] = '<nobr>' . $val . '</nobr> ';
            }

			// check if we have more than one item enabled, otherwise the 'select all' will not be displayed
			$checkboxesEnabled = 0;
			foreach( $this->_items as $item ){
				if ( $item->isDisabled() == false ){
					$checkboxesEnabled ++;
					if ( $checkboxesEnabled == 2 ) break; 
				}
			}

			// init the 'select all' html
			$selall_html = '';

			// check if: user wants a 'select all', we have more than one item in checkboxgroup, we have more than one item enabled
			if ( $this->_addSelectAll && count( $this->_items ) > 1 && $checkboxesEnabled > 1 ){ 

				// compute button code
				$selall = new YDFormElement_Checkbox( $this->_form, $this->getAttribute( 'id' ) . 'sall' );
				$selall->setAttribute( 'onclick', 'for (var i=0;i<document.forms[\''. $this->_form . '\'].elements.length;i++) if (document.forms[\''. $this->_form . '\'].elements[i].type==\'checkbox\' && !document.forms[\''. $this->_form . '\'].elements[i].disabled && /' . $this->_name . '\[[a-zA-Z0-9]+\]/.test( document.forms[\''. $this->_form . '\'].elements[i].name ) ) document.forms[\''. $this->_form . '\'].elements[i].checked = this.checked;' );

				// if all checkboxes are selected, the 'select all' will be selected too
				$selall->setValue(1);
				foreach( $this->getValue() as $elem => $value )
					if ( $value != 1 ){ $selall->setValue(0); break; }

				// get global 'select all' parameter
				$selall_label = YDConfig::get( 'YD_FORMELEMENT_CHKGROUP_SELALL' );
				if( is_null( $selall_label ) ){
					$selall_label = t( 'select all' );
				}
	
				// compute button label
				if ( $this->_position == 'right' ) $selall_html = '<span ' . YDForm::_convertToHtmlAttrib( $this->_addSelectAll_chk_attributes ) . '>' . $selall->toHTML() . '&nbsp;<label for="' . $selall->getAttribute( 'id' ) . '">' . $selall_label . '</label></span>';
				else                               $selall_html = '<span ' . YDForm::_convertToHtmlAttrib( $this->_addSelectAll_chk_attributes ) . '><label for="' . $selall->getAttribute( 'id' ) . '">' . $selall_label . '</label>&nbsp;' . $selall->toHTML() . '</span>';
			}

			// check if we don't want columns format
			if ( $this->_columns == 1 ){
			
				// if we don't have a 'select all' just return checkboxes html
				if ( $selall_html == '' ){
					return implode( $this->_separator, $output );

				// if 'select all' is at the end
				}else if ( $this->_addSelectAll_onBottom ){
					return implode( $this->_separator, $output ) . $this->_separator . $selall_html;

				// if 'select all' is at the beggining
				}else{
					return $selall_html . $this->_separator . implode( $this->_separator, $output );
				}
			
			// create a html table
			}else{
			
				// init table header
				$table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
			
				// compute width of each column
				$width = 'width="' . intval( 100 / $this->_columns ) . '%"';
			
				// if 'select all' is defined to be on top, add a row with 'select all' column and other cols empty
				if ( $selall_html != '' && ! $this->_addSelectAll_onBottom ){
				
					$table .= '<tr>';
					$table .= '<td ' . $width . '>' . $selall_html . '</td>';
					
					for( $k = 1; $k < $this->_columns; $k++ )
						$table .= '<td ' . $width . '>&nbsp;</td>';
					
					$table .= '</tr>';
				}
	
				// create option checkboxes content			
				for ( $i = 0; isset( $output[ $i ] ); ){
				
					$table .= '<tr>';
				
					for( $k = 0; $k < $this->_columns; $k++ ){
						
						// if we have a checkbox just add it. otherwise add an empty char
						if ( isset( $output[ $i ] ) ) $table .= '<td ' . $width . '>' . $output[ $i ] . '</td>';
						else                          $table .= '<td ' . $width . '>&nbsp;</td>';
					
						$i++;
					}

					$table .= '</tr>';
					
				}

				// if 'select all' is defined to be on bottom, add empty cols and a last column with 'select all' code
				if ( $selall_html != '' && $this->_addSelectAll_onBottom ){
				
					$table .= '<tr>';
					
					for( $k = 1; $k < $this->_columns; $k++ )
						$table .= '<td ' . $width . '>&nbsp;</td>';

					$table .= '<td ' . $width . '>' . $selall_html . '</td>';
					$table .= '</tr>';
				}
					
				// end table
				$table .= '</table>';
					
				return $table;			
			}

        }

    }

?>