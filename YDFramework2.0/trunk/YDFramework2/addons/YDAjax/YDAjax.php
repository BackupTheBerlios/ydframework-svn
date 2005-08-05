<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Include the needed libraries. 
	YDInclude( dirname( __FILE__ ) . '/xajax.inc.php');

    /**
     *  Class definition for the YDAjax addon.
     */
    class YDAjax extends xajax{

        /**
         *	This is the class constructor for the YDAjax class.
         *
         *	@param $requestURI		Request URI. default to current page.
         *	@param $prefix		Prefix used in ajax functions.
         *	@param $debug		Debug mode.
         */
        function YDAjax( $requestURI = null, $prefix = "__ydf", $debug = null) {

			// prefix is used in some methods
			$this->prefix = $prefix;

			// if debug not defined use ydf configuration
			if (is_null( $debug )){
			
				// test if debug is off
				if (YDConfig::get("YD_DEBUG") == 0){
					$debug = false;
					$this->statusMessagesOff();
				}else{
					$debug = true;
					$this->statusMessagesOn();
				}
				
			}

			// if url is null, we want ydf url. if empty string xajax creates it, otherwise it's a custom url
			if (is_null($requestURI)){	YDInclude( 'YDUrl.php' );
							$url = new YDUrl( YD_SELF_URI );
							$requestURI = $url->getUrl();
			}
		
			// create ajax object
			$this->xajax( $requestURI, $this->prefix, $debug );
			
			// initilize form
			$this->form = null;
			
			// custom javascript (we need more than the javascript provided by xajax
			$this->customjs = array();
		}
		

		// internal method. replace "<head>" with "<head> and all ajax javascript more our custom javascript"
		function assignJavascript($tpl_source, &$smarty){
		    return eregi_replace("<head>", "<head>\n". $this->getJavascript() . $this->customJavascript(), $tpl_source);
		}


        /**
         *	This class assigns ajax with a template object
         *
         *	@param $template		Template object.
         */
		function setTemplate( & $template ){
			$template->register_outputfilter( array( &$this, "assignJavascript") );
		}


        /**
         *	This class assigns ajax with a form object
         *
         *	@param $template		Template object.
         */
		function setForm( & $form ){
			$this->form = $form;
		}

		
		// internal method. returns the extra javacript
		function customJavascript(){

			if (empty($this->customjs)) return '';
			
			return '<script type="text/javascript">' . "\n" . implode("\n", array_values($this->customjs)) . "\n" . '</script>';
		}
		
		
        /**
         *	This class enables/disables debug
         *
         *	@param $flag		Boolean argument
         */
		function setDebug( $flag = true){
			
			if ($flag == true)	$this->debugOn();
			else				$this->debugOff();
		}


        /**
         *	This class enables debug and it's an alias to setDebug
         */
		function dump(){
			
			$this->debugOn();
		}


        /**
         *	This class enables/disables a browser setStatusMessage (experimental)
         *
         *	@param $flag		Boolean argument
         */
		function setStatusMessage( $flag = true ){

			if ($flag == true)	$this->statusMessagesOn();
			else				$this->statusMessagesOff();
		}
		
		
        /**
         *	This method assign a server function and arguments to a form element event.
         *
         *	@param $formElementName	Form element name (string).
         *	@param $serverFunction	Name of the function in the server (string).
         *	@param $arguments		Default arguments for this function (string or array of strings).
         *	@param $event			Event name (auto-detection by default).
         */		
		 function registerElement( $formElementName, $serverFunction, $arguments = null, $event = null){
		 
		 	if (!isset($formElementName)) die("You must define a formElementName in registerElement");
		 
			// register function in xajax
			$this->registerFunction( $serverFunction );

			// initialize arguments
			$args = array();

			// compute arguments string
			if (is_null($arguments)) $arguments = "";
			else{	
					if (!is_array( $arguments )) $arguments = array( $arguments );
				
					foreach ($arguments as $arg){

						// if argument is a number just add it and continue
						if (is_numeric( $arg )) { $args[] = $arg; continue; }
					
						// if there's a form defined, test if this argument is an element
						if (!is_null( $this->form ) && $this->form->isElement( $arg )){
						
							// get element object
							$elem = & $this->form->getElement( $arg );

							// get element type to invoke the custom js form get the value
							switch( $elem->getType() ){
								case 'text' :			$args[] = $this->getValueText( $elem );				break;
								case 'password' :		$args[] = $this->getValuePassword( $elem );			break;
								case 'textarea' :		$args[] = $this->getValueTextarea( $elem );			break;
								case 'radio' :			$args[] = $this->getValueRadio( $elem );			break;
								case 'checkbox' :		$args[] = $this->getValueCheckbox( $elem );			break;
								case 'dateselect' :		$args[] = $this->getValueDateSelect( $elem );		break;
								case 'datetimeselect' :	$args[] = $this->getValueDateTimeSelect( $elem );	break;
								case 'timeselect' :		$args[] = $this->getValueTimeSelect( $elem );		break;
								case 'span' :			$args[] = $this->getValueSpan( $elem );				break;
																							
								default : die ('Element type "'. $elem->getType() .'" is not supported as dynamic argument');
							}
							
							continue;
						}
						
						// if it's not a form element just parse the argument string (we don't want " and ' on code)
						$args[] = "'". htmlentities( $arg ) ."'";
					}
						
					// convert arguments (we don't want " and ' on code)
					$arguments = implode( ",", $args );
			}

			// get element object
			$formElement = & $this->form->getElement( $formElementName );
		
			// if event is null we must check the form element type to compute the default event
			if (is_null( $event )){
			
				// get form element type  TODO: use $formElement->getType() instead
				switch ( $formElement->_type ){

					case 'submit' : trigger_error('Submit buttons cannot be used in ajax because they force submittion.'); break;
					case 'image' :  trigger_error('Images cannot be used in ajax because they force submittion. Use "img" element instead.'); break;
					case 'img' :		$event = 'onClick';  break;					
					case 'button' :		$event = 'onClick';  break;					
					case 'dateselect' :	$event = 'onChange'; break;
					case 'radio' :		$event = 'onChange'; break;
					case 'checkbox' :	$event = 'onChange'; break;
					case 'text' :		$event = 'onChange'; break;
					case 'textarea' :	$event = 'onChange'; break;
					case 'bbtextarea' :	$event = 'onChange'; break;
					case 'password' :	$event = 'onChange'; break;
					
					// if element doesn't exist return a php error
					default : trigger_error('Element type ('. $formElement->_type .') is not supported in YDAjax'); break;
				}
			}
			
			// add event and function name to element attributes
			$formElement->setAttribute($event, $this->sWrapperPrefix . $serverFunction .'('. $arguments .')');

			// xajax process request
			$this->processRequests();
			return;			
		}
		
		
		// internal method to create the needed js function to retrieve a text value
		function getValueText( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function to retrieve this text element value (it will replace possible js functions for this element because we cannot have 2 functions with the same name)
			$this->customjs[$jsfunction] = 'function '. $jsfunction .'(){ return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].value; }';

			// return function invocation
			return $jsfunction ."()";
		}
		

		// internal method to create the needed js function to retrieve a password value
		function getValuePassword( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'function '. $jsfunction .'(){ return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].value; }';

			// return function invocation
			return $jsfunction ."()";
		}
		

		// internal method to create the needed js function to retrieve a textarea value
		function getValueTextarea( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'function '. $jsfunction .'(){ return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].value; }';

			// return function invocation
			return $jsfunction ."()";
		}
		
		
		// internal method to create the needed js function to retrieve a span value
		function getValueSpan( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'function '. $jsfunction .'(){ return document.getElementById("'. $element->getForm() .'_'. $element->getName().'").innerHTML; }';

			// return function invocation
			return $jsfunction ."()";
		}		
		

		// internal method to create the needed js function to retrieve a dateselect value
		function getValueDateSelect( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'function '. $jsfunction .'(){' ."\n";
			$this->customjs[$jsfunction] .= '	var day   = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[day]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var month = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[month]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var year  = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[year]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var mydate = new Date( year, month, day ); ' . "\n";
			$this->customjs[$jsfunction] .= '	return mydate.getTime() / 1000; }';

			// return function invocation
			return $jsfunction ."()";
		}


		// internal method to create the needed js function to retrieve a timeselect value
		function getValueTimeSelect( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'function '. $jsfunction .'(){' ."\n";
			$this->customjs[$jsfunction] .= '	var hours    = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[hours]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var minutes  = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[minutes]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var mydate = new Date( 1970, 1, 1, hours, minutes ); ' . "\n";
			$this->customjs[$jsfunction] .= '	return mydate.getTime() / 1000; }';

			// return function invocation
			return $jsfunction ."()";
		}		


		// internal method to create the needed js function to retrieve a datetimeselect value
		function getValueDateTimeSelect( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'function '. $jsfunction .'(){' ."\n";
			$this->customjs[$jsfunction] .= '	var day      = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[day]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var month    = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[month]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var year     = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[year]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var hours    = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[hours]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var minutes  = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[minutes]"].value;' ."\n";
			$this->customjs[$jsfunction] .= '	var mydate = new Date( year, month, day, hours, minutes ); ' . "\n";
			$this->customjs[$jsfunction] .= '	return mydate.getTime() / 1000; }';

			// return function invocation
			return $jsfunction ."()";
		}				
		

		// internal method to create the needed js function to retrieve a checkbox value
		function getValueCheckbox( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'function '. $jsfunction .'(){' ."\n";
			$this->customjs[$jsfunction] .=	'	if (document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].checked) ' ."\n";
			$this->customjs[$jsfunction] .=	'		return 1; ' ."\n";
			$this->customjs[$jsfunction] .=	'	return 0; }' ."\n";

			// return function invocation
			return $jsfunction ."()";
		}
		

		// internal method to create the needed js function to retrieve a radio value
		function getValueRadio( & $element ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName();
			
			// add custom js function
			$this->customjs[$jsfunction]  = 'function '. $jsfunction .'(){ ' ."\n";
			$this->customjs[$jsfunction] .= '	var __ydftmp = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'"]; ' ."\n";
			$this->customjs[$jsfunction] .= '	for (counter = 0; counter < __ydftmp.length; counter++) ' ."\n";
			$this->customjs[$jsfunction] .= '		if (__ydftmp[counter].checked) return __ydftmp[counter].value; ' ."\n";
			$this->customjs[$jsfunction] .= '	return false; } ' ."\n";

			// return function invocation
			return $jsfunction ."()";
		}		
		


}




	
class YDAjaxResponse extends xajaxResponse{
	
		function YDAjaxResponse(){
			$this->xajaxResponse();
		}
		
		
        /**
         *	This method assign a server result to a form element in the client side.
         *
         *	@param $formName			Name of the form.
         *	@param $formElementName		Name of the form element.
         *	@param $formElementType		Form element type.
         *	@param $result				Result value.
         *	@param $attribute			Custom attribute (auto-detection by default).
         *	@param $option				Other options.
         */		
		function assignResult( $formName, $formElementName, $formElementType, $result, $attribute = null, $options = array()){
	
			// depending on the type we must invoke the custom function for the element
			switch( strtolower($formElementType) ){

				case 'submit' :
				
				case 'button' :			$this->assignButton(    $formName, $formElementName, $result, $attribute, $options ); break;
				case 'checkbox' :		$this->assignCheckbox(  $formName, $formElementName, $result, $attribute, $options ); break;
				case 'dateselect' :		$this->assignDateSelect($formName, $formElementName, $result, $attribute, $options ); break;
				case 'datetimeselect' :	$this->assignDateTimeSelect( $formName, $formElementName, $result, $attribute, $options ); break;
				case 'timeselect' :		$this->assignTimeSelect(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'bbtextarea' :		$this->assignBBtextarea(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'img' :			$this->assignImg(      $formName, $formElementName, $result, $attribute, $options ); break;
				case 'hidden' :			$this->assignHidden(   $formName, $formElementName, $result, $attribute, $options ); break;
				case 'text' :			$this->assignText(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'textarea' :		$this->assignTextarea( $formName, $formElementName, $result, $attribute, $options ); break;
				case 'radio' :			$this->assignRadio(    $formName, $formElementName, $result, $attribute, $options ); break;
				case 'password' :		$this->assignPassword( $formName, $formElementName, $result, $attribute, $options ); break;
				case 'span' :			$this->assignSpan(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'image' :			$this->addAlert('Input type image cannot be used. Please use a img instead.'); break;

				// if element doesn't exist return an js alert
				default : $this->addAlert('Element type "'. $formElementType .'" is not supported in YDAjax'); break;
			}
			
			
		}


        /**
         *	This method assigns a server result to a element using just the id
         *
         *	@param $id			Html element id.
         *	@param $result		Result value.
         *	@param $attribute	Custom attribute (auto-detection by default).
         *	@param $option		Other options (not used).
         */	
		function assignId( $id, $result, $attribute = null, $options = array()){
		
			// if event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'innerHTML';
		
			// escape string
			$result = addslashes( $result );

			// assign result to form element using the id
			$this->addScript('document.getElementById("'. $id .'").'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a BBtextarea element
		function assignBBtextarea( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}
		
		
		// internal method to assign a value to a img element
		function assignImg( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'src';

			// assign result image
			$this->addScript('document.'. $formName .'_'. $formElementName .'.'. $attribute .' = "'. $result .'";');
		}		

		
		// internal method to assign a value to a text element
		function assignText( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a span element
		function assignSpan( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'innerHTML';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element using the id
			$this->addScript('document.getElementById("'. $formName .'_'. $formElementName .'").'. $attribute .' = "'. $result .'";');
		}
		
				
		// internal method to assign a value to a textarea element
		function assignTextarea( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}
		

		// internal method to assign a value to a radio element
		function assignRadio( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// we must cycle all radio instances of this radio element
			$this->addScript('__ydftmp = document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"];
							 for (counter = 0; counter < __ydftmp.length; counter++)
							 	if (__ydftmp[counter].value == "'. $result .'") __ydftmp[counter].checked = true;
								else __ydftmp[counter].checked = false;');
		}		
		

		// internal method to assign a value to a password element
		function assignPassword( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}		
		

		// internal method to assign a value to a hidden element
		function assignHidden( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a dateselect element
		function assignDateSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// if result is a timestamp get values
			if (is_integer( $result )){
			
				$parsed = getdate( $result );
				$day    = $parsed['mday'];
				$month  = $parsed['mon'];
				$year   = $parsed['year'];

			}else $this->addAlert("DateSelect elements in YDAjax only support timestamps in assignResult");
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[day]"].'.   $attribute .' = "'. $day .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[month]"].'. $attribute .' = "'. $month .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[year]"].'.  $attribute .' = "'. $year .'";');
		}		
		


		// internal method to assign a value to a datetimeselect element
		function assignDateTimeSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// if result is a timestamp get values
			if (is_integer( $result )){
			
				$parsed  = getdate( $result );
				$day     = $parsed['mday'];
				$month   = $parsed['mon'];
				$year    = $parsed['year'];
				$hours   = $parsed['hours'];
				$minutes = $parsed['minutes'];

			}else $this->addAlert("DateSelect elements in YDAjax only support timestamps in assignResult");
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[day]"].'.      $attribute .' = "'. $day     .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[month]"].'.    $attribute .' = "'. $month   .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[year]"].'.     $attribute .' = "'. $year    .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[hours]"].'.    $attribute .' = "'. $hours   .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[minutes]"].'.  $attribute .' = "'. $minutes .'";');
		}		

		
		// internal method to assign a value to a timeselect element
		function assignTimeSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// if result is a timestamp get values
			if (is_integer( $result )){
			
				$parsed  = getdate( $result );
				$hours   = $parsed['hours'];
				$minutes = $parsed['minutes'];

			}else $this->addAlert("TimeSelect elements in YDAjax only support timestamps in assignResult");
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[hours]"].'.   $attribute .' = "'. $hours   .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[minutes]"].'. $attribute .' = "'. $minutes .'";');
		}	
		

		// internal method to assign a value to a button element
		function assignButton( $formName, $formElementName, $result, $attribute, $options = array()){

			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
			
			// check and convert $result
			$result = htmlspecialchars( $result );
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}
		
		
		// internal method to assign a value to a checkbox element
		function assignCheckbox( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'checked';

			if ($attribute != 'checked')
				return $this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');

			// if attribute is 'checked' and result is true, check this checkbox
			if (is_bool($result) && $result == true)
				return $this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].checked = true;');

			// if attribute is 'checked' and result is false, clean checkbox selection
			if (is_bool($result) && $result == false)
				return $this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].checked = false;');

			// if attribute is 'checked' and result is 'toggle', checkbox will have the opposite value
			if ($result == "toggle")
				return $this->addScript('var __ydftmp = document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"];
										 if (__ydftmp.checked == false) {__ydftmp.checked = true;} else {__ydftmp.checked = false;}');


		}
		
		
}
?>
