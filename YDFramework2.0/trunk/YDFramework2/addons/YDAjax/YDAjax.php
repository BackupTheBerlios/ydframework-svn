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
     *  @addtogroup YDAjax Addons - Ajax
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }


    /**
     *  This config defines the ajax response enconding
     *  Default: iso-8859-15.
     */
    YDConfig::set( 'YD_AJAX_ENCODING', "iso-8859-15", false );


    /**
     *  This config defines the prefix for javascript functions and variables
     *  Default: __ydf.
     */
    YDConfig::set( 'YD_AJAX_PREFIX', "__ydf", false );


    /**
     *  This config defines custom debug value
     *  Default: false
     */
    YDConfig::set( 'YD_AJAX_DEBUG', false, false );


    /**
     *  Include the needed libraries. 
     */
	require_once( dirname( __FILE__ ) . '/xajax.inc.php');


    /**
     *  Class definition for the YDAjax addon.
     *
     *  @ingroup YDAjax
     */
    class YDAjax extends xajax{

        /**
         *	This is the class constructor for the YDAjax class.
         *
         *	@param $template		Template object.
         */
        function YDAjax( & $template, $url_string = null ){

            // Setup the module
            $this->_author = 'Francisco Azevedo';
            $this->_version = '3.0b';
            $this->_copyright = '(c) Copyright 2002-2006 Francisco Azevedo';
            $this->_description = 'This class makes ajax easy for YDF developers';

			// prefix string used in js function names
			$this->prefix = YDConfig::get( 'YD_AJAX_PREFIX' );

			// initialize xajax object
			$this->xajax( "", $this->prefix, YDConfig::get( 'YD_AJAX_ENCODING' ) );
			
			// initilize all possible forms (4)
			$this->form_0 = null; $this->form_1 = null;
			$this->form_2 = null; $this->form_3 = null;

			// initilize template
			$this->template = & $template;
			
			// response object
			$this->response = new YDAjaxResponse( YDConfig::get( 'YD_AJAX_ENCODING' ) );

			// by default we don't use effects (then js effects lib won't be loaded)
			$this->effects = array();

			// autocompleter code
			$this->autocompleterCode = '';
			$this->autocompleterCss  = '';
			$this->autocompleterCodeFunctions = array();

            // init wysiwyg editors
            $this->wysiwyg_editorpath  = dirname( __FILE__ ) . '/editors/YDAjaxEditor.php';
            $this->wysiwyg_editorclass = 'YDAjaxEditor';
            $this->wysiwyg_forms       = array();

			// check url
			if( is_null( $url_string ) ){
				$url_string = YDRequest::getNormalizedUri();
			}

			// compute default ajax header
			$html  = "var xajaxRequestUri     = \"" . $url_string . "\";";

			$debug = YDConfig::get( 'YD_AJAX_DEBUG' ) ? "true" : "false";

			// check debug option
			$html .= "var xajaxDebug          = " . $debug . ";";
			$html .= "var xajaxStatusMessages = " . $debug . ";";
			$html .= "var xajaxWaitCursor     = " . $debug . ";";

			// use post
			$html .= "var xajaxDefinedGet  = 0;";
			$html .= "var xajaxDefinedPost = 1;";

			// get standard xajax code
			$html .= file_get_contents( dirname( __FILE__ ) . '/js/xajax.js' ) . "\n";

			// include generic effects lib .. yes, it must be ALWAYS included
			$html .= file_get_contents( dirname( __FILE__ ) . '/js/prototype.lite.js_moo.fx.js_moo.fx.pack.js' ) . "\n";

			// add default header to template
			$this->template->addJavascript( $html, true, false );
			
			// array to store events already added
			$this->_eventsAdded = array();
		}


        /**
         *	This function adds custom javascript to a response.
         *
         *	@param $js		Javascript code.
         *	@param $at_end	(Optional) Boolean that defines if code should be added only at the response end. Default: false
         */
		function addScript( $js, $at_end = false ){

			return $this->response->addScript( $js, $at_end );
		}


        /**
         *	This method returns the template object
         */
		function & getTemplate(){
			return $this->template;
		}


        /**
         *	This function sets YDAjax to automagically handle a waiting message
         *
         *	@param $html        (Optional) Html to use in the message.
         *	@param $options     (Optional) Js options for the html object
         *	@param $effectStart (Optional) YDAjaxEffect object on page load (by default a simple 'hide' will be used)
         *	@param $effectShow  (Optional) YDAjaxEffect object when submitting requests (by default a simple 'show' will be used)
         *	@param $effectHide  (Optional) YDAjaxEffect object on response (by default a simple 'hide' will be used)
         */
		function useWaitingMessage( $html = '<h3>&nbsp; Please wait ... &nbsp;<\/h3>', $options = array(), $effectStart = null, $effectShow = null, $effectHide = null ){

			// if we already had invoke this method, then we don't need to add another js code for message creation
			if ( isset( $this->wtID ) ) return;
		
			// create js variable name for html message element
			$this->wtID = "waitingmessage";

			// check custom options
			if ( !isset( $options['style.backgroundColor'] ) ) $options['style.backgroundColor'] = '#cccccc';
			if ( !isset( $options['style.border'] ) )          $options['style.border']          = '1px solid #110000';
			if ( !isset( $options['style.width'] ) )           $options['style.width']           = '200px';

			// static values
			$options['style.cursor']     = "default";
			$options['style.position']   = "absolute";
			$options['style.top']        = '40%';
			$options['style.left']       = '50%';
			$options['style.marginLeft'] = '-' . round( intval( $options['style.width'] ) / 2) . 'px';
			$options['style.zIndex']     = 9999;
			$options['innerHTML']        = '<center>' . $html . '</center>';

			// create js for html element creation
			$waitingMessageCode  = "var " . $this->wtID . " = document.createElement('div');";

			// append div box to document body
			$waitingMessageCode .= "document.body.appendChild(" . $this->wtID . ");";
			
			// add id
			$waitingMessageCode .= $this->wtID . ".id = '" . $this->wtID ."id';";
			
			// create start effect
			if ( is_null( $effectStart ) )
				$effectStart = new YDAjaxEffect( '', 'hide', '', 0 );

			// append start code
			$waitingMessageCode .= $effectStart->getJSHead( $this->wtID ."id" ) . $effectStart->getJSBody( $this->wtID ."id" );
			
			// add custom options
			foreach( $options as $name => $value )
				$waitingMessageCode .= $this->wtID . "." . $name . " = '" . $value . "';";

			// add waiting message code (that creates the message div) to template
			$this->template->addJavascript( $waitingMessageCode, false, true );

			// create show effect
			if ( is_null( $effectShow ) )
				$effectShow = new YDAjaxEffect( '', 'show', '', 0 );

			// create hide effect
			if ( is_null( $effectHide ) )
				$effectHide = new YDAjaxEffect( '', 'hide', '', 0 );

			// create js functions to show/hide div
			$waitingMessageCodeFunction  = "xajax.loadingFunction     = function(){" . $effectShow->getJSHead( $this->wtID . "id" ) . $effectShow->getJSBody( $this->wtID ."id" ) . "}\n";
			$waitingMessageCodeFunction .= "xajax.doneLoadingFunction = function(){" . $effectHide->getJSHead( $this->wtID . "id" ) . $effectHide->getJSBody( $this->wtID ."id" ) . "}\n";

			// add waiting message code (that invokes the load/done show/hide) to template 'onload' section
			$this->template->addJavascript( $waitingMessageCodeFunction, false, false );
		}


        /**
         *	This function sets YDAjax to use a custom js function when invoke/close an ajax connection
         *
         *	@param $onopen      Javascript function to invoke when opening the box
         *	@param $onclose     Javascript function to invoke when closing the box
         */
		function useWaitingCode( $onopen, $onclose ){

			// create js functions to show/hide div
			$waitingMessageCodeFunction  = "xajax.loadingFunction     = " . $onopen  . ";\n";
			$waitingMessageCodeFunction .= "xajax.doneLoadingFunction = " . $onclose . ";\n";

			// add waiting message code (that invokes the load/done show/hide) to template 'onload' section
			$this->template->addJavascript( $waitingMessageCodeFunction, false, false );
		}


		// internal method to get a form of an element
		function & __getForm( $name ){

			if ( is_object( $this->form_0 ) && $this->form_0->isElement( $name ) ) return $this->form_0;
			if ( is_object( $this->form_1 ) && $this->form_1->isElement( $name ) ) return $this->form_1;
			if ( is_object( $this->form_2 ) && $this->form_2->isElement( $name ) ) return $this->form_2;
			if ( is_object( $this->form_3 ) && $this->form_3->isElement( $name ) ) return $this->form_3;

			$null = null;

			return $null;
		}


        /**
         *	This private method returns true if a form name exists
         *
         *	@param $formName		form name string
         */		
		function __isForm( $formName ){
		
			if ( is_object( $this->form_0 ) && $this->form_0->getName() == $formName ) return true;
			if ( is_object( $this->form_1 ) && $this->form_1->getName() == $formName ) return true;
			if ( is_object( $this->form_2 ) && $this->form_2->getName() == $formName ) return true;
			if ( is_object( $this->form_3 ) && $this->form_3->getName() == $formName ) return true;
			
			return false;
		}


        /**
         *	This method adds a form
         *
         *	@param $form		YDForm object
         */		
		function addForm( & $form ){
		
			if ( is_null( $this->form_0 ) ){ $this->form_0 = & $form; return true; }
			if ( is_null( $this->form_1 ) ){ $this->form_1 = & $form; return true; }
			if ( is_null( $this->form_2 ) ){ $this->form_2 = & $form; return true; }
			if ( is_null( $this->form_3 ) ){ $this->form_3 = & $form; return true; }

			return false;
		}


        /**
         * This method checks if current context is response
         *
         *	@returns  Boolean true if on response. False otherwise
         */		
		function isOnResponse() {
			return $this->canProcessRequests();
		}


        /**
         * This method gets the actual DOM id of an element by first seeing if it is a form element.
         *
         *  @param $id        Form element name or a generic id string.
         */
         function getActualId( $id ) {
         
            // try to get a form containing this element
            $form = $this->__getForm( $id );

            // if $id is not a form element return string
            if ( is_null( $form ) ) return $id;

            // if is a form element, get element
            $elem = & $form->getElement( $id );

            // return real element id
            return $elem->getAttribute( 'id' );
         }


        /**
         *	This method assigns the current autocompleter with a result array
         *
         *	@param $result		Autocompleter results array
         */		
		function addCompleterResult( $result ){
			
			// if element is not an array we must create one
			if ( !is_array( $result ) ) $result = array( $result );
		
			// test it result is empty
			if ( empty( $result ) ) return $this->addScript( 'autocompleterClose();' );

			// compute js variable name for results array
			$jsvar = $this->prefix . "atresulttmp";

			// assign js array values
			$js = "var " . $jsvar . " = new Array('" . implode( "','", $result ) . "');";

			// send array and method to display autocompleter
			return $this->addScript( $js . 'autocompleterOpen(' . $jsvar . ');' );
		}


        /**
         *	This method assigns a server function and arguments to a form element event.
         *
         *	@param $formElementName		Form element name (string) or js function.
         *	@param $serverFunction		Array with class and method.
         *	@param $arguments			(Optional) Arguments for this function call
         *	@param $event				(Optional) Html event name (auto-detection by default when using null).
         *	@param $options				(Optional) Custom options.
         *	@param $effects				DEPRECATED (Optional) Effect or array of effects to execute on event (before ajax call).
         */		
		 function addEvent( $formElementName, $serverFunction, $arguments = null, $event = null, $options = null, $effects = null ){ 

			// compute event signature
			$sig = $formElementName . serialize( $serverFunction );

			// check if event already added
			if( in_array( $sig, $this->_eventsAdded ) ){
				return;
			}

			// add signature to list
			$this->_eventsAdded[] = $sig;

			// if formElementName is "*" we want to define a default event (only before responses)
			if ( $formElementName === "*" )
				return $this->registerCatchAllFunction( array( $serverFunction[1], $serverFunction[0], $serverFunction[1] ) );

		 	if( !is_array( $options ) ) $options = array( $options );
	 
			// serverFunction must be an array with a class and the method (get function name)
			$functionName = $this->computeFunction( $formElementName, $serverFunction, $arguments, $options, $effects );

			// if element don't have a form we must return
			if( is_null( $form = $this->__getForm( $formElementName ) ) ) return;

			// get form element
			$formElement = & $form->getElement( $formElementName );

			// if event is null we must check the form element type to compute the default event
			if( is_null( $event ) ) $event = $formElement->getJSEvent();

			// get previous atribute
			$previous = is_null( $formElement->getAttribute( $event ) ) ? '' : trim( $formElement->getAttribute( $event ) );

			// if previous atribute don't have ';' in the end we should add it
			if (strlen( $previous ) > 0 && $previous[ strlen( $previous ) - 1 ] != ';' ) $previous .= ';';

			if ( in_array( 'replace', $options ) ){
				if ( !$this->isOnResponse() ) return $formElement->setAttribute( $event, $functionName );
				else                      return $this->addScript( $formElement->setJS( $functionName, $event, $options ) );
			}

			if ( in_array( 'prepend', $options ) ){
				if ( !$this->isOnResponse() ) return $formElement->setAttribute( $event, $functionName . $previous );
				else	  				  return $this->addScript( $formElement->setJS( $functionName . $previous, $event, $options ) );
			}
			
			if ( !$this->isOnResponse() ) $formElement->setAttribute( $event, $previous . $functionName );
			else                      $this->addScript( $formElement->setJS( $previous . $functionName, $event, $options ) );

			// cycle methods for last one			
			foreach( $this->aFunctions as $sFunction => $bExists ){}

			// if last one exists, add code to template
			if ( isset( $sFunction ) ){
				$this->template->addJavascript( $this->_wrap( $sFunction, $this->aFunctionRequestTypes[$sFunction] ) );
			}
		}


        /**
         *	This method will compute the JS needed to execute the ajax call
         *
         *	@param $formElementName		Form element name
         *	@param $serverFunction		Server function call
         *	@param $arguments			Arguments that the call will sent
         *	@param $options				Custom options
         *	@param $effects				Custom effects
         */	
		function computeFunction( $formElementName, $serverFunction, $arguments = null, $options = null, $effects = null ){ 

			// register function in xajax
			$this->registerFunction( array( $serverFunction[1], $serverFunction[0], $serverFunction[1] ) );

			if( !$this->isOnResponse() ) $serverFunctionName = $serverFunction[1];
			else           	             $serverFunctionName = $serverFunction;

			// check options array
			if ( !is_array( $options ) ) $options = array( $options );
			
			// get function name
			$functionName = $this->prefix . $serverFunctionName . '(' . $this->_computeFunctionArguments( $arguments, $options ) . ');';

			// process effects
			if ( !is_null( $effects ) ){
		
				// if is one effect, we should create an array of effects
				if ( !is_array( $effects ) ) $effects = array( $effects );

				// cycle effects and create js
				foreach( $effects as $effect ){
				
					if ( !in_array( $effect->getVariable(), $this->effects ) ){

						// get form of the element
						$form = $this->__getForm( $effect->getId() );

						// check if element is a form element
						if ( !is_null ( $form ) ){

							// compute element id
							$element = & $form->getElement( $effect->getId() );
					
							// get id
							$id = $element->getAttribute( 'id' );
						
							// if before response, add effect js to template. otherwise add it to response
							if ( !$this->isOnResponse())	$this->effects[ $effect->getVariable() ] = $effect->getJSHead( $id );
							else						$this->response->addScript( $effect->getJSHead( $id ) );
						
						}
						else{
							if ( !$this->isOnResponse() )	$this->effects[ $effect->getVariable() ] = $effect->getJSHead();
							else						$this->response->addScript( $effect->getJSHead() );
							}
					}

					// function is now the effect js created
					$functionName .= $effect->getJSBody( $id );
				}
			}

			// if formElementName is a js function we are not assigning an event to a form element but creating a js function
			if ( ereg ( "^(.*)(\(.*\))$", $formElementName, $res ) ){

				// if we are before reponse we must add function to template
				if ( !$this->isOnResponse() ) //return $this->customjs[ $res[0] ] = $functionName;
				return $this->template->addJavascript( "function " . $res[0] . "{" . $functionName . "}\n", true );
				
				// create js variable to handle ajax request
				$js  = $this->prefix . $serverFunction .'=function(){return xajax.call("' . $serverFunction .'", arguments, 1);};';

				// create custom js function
				$js .= $res[1] . '=function'. $res[2] .'{' . $functionName . '};';

				// add all js code to response
				return $this->response->addScript( $js );
			}

			
			if( $this->isOnResponse() ){
			
				// create js variable to handle ajax request
				$js  = $this->prefix . $serverFunction . '=function(){return xajax.call("' . $serverFunction . '", arguments, 1);};';

				// create custom js function
				$js .= $this->prefix . $serverFunction . 'get=function(){' .  $functionName . '};';

				// add all js code to response
				$this->response->addScript( $js );
				
				return $this->prefix . $serverFunction . 'get()';
			}

			return $functionName;
		}

		
		
        /**
         *	This method adds an effect to a response
         *
         *	@param $effect		Effect object
         */	
		function addEffect( $effect ){

			// initialize js to send
			$js = '';

			// get form of the element where effect is applyied
			$form = $this->__getForm( $effect->getId() );

			// test if effect id is a form element
			if ( !is_null( $form ) ){
				
				// get element
				$elem = & $form->getElement( $effect->getId() );		

				// element id
				$id = $elem->getAttribute( 'id' );
			}else{
			
				// if effect id is not a form element then its a html id
				$id = $effect->getId();
			}

			// if some html id has not already an effect applyed we must send effect header
			if ( !in_array( $effect->getVariable(), $this->effects ) )
				$js = $effect->getJSHead( $id );

			$js .= $effect->getJSBody( $id );

			// if effect is added before a response we must included it on "onload" effects
			if ( !$this->isOnResponse() )  $this->effects[ $effect->getVariable() ] = $effect->getJSBody( $id );
			else                       $this->response->addScript( $js );
		}


        /**
         *	This method will get function arguments and parse them
         *  Function arguments can be simple integers, strings, variables, form elements and complete forms
         *
         *	@param $arguments		Arguments
         *	@param $options			Custom options
         */	
		function _computeFunctionArguments( $arguments, $options ){
			
			// if there are not arguments return empty string
			if ( is_null( $arguments ) ) return "";

			// if there's only one argument or option, create arrays
			if ( !is_array( $arguments ) ) $arguments = array( $arguments );
			if ( !is_array( $options ) )   $options   = array( $options );

			// initialize arguments
			$args = array();

			// cycle arguments to create js function to get values
			foreach ( $arguments as $arg ){

				// if argument is numeric just add it and continue
				if ( is_numeric( $arg ) ) { $args[] = $arg; continue; }

				// if is a javascript variable, just add it
				if ( ereg ( "^var (.*)$", $arg, $res ) ){ $args[] = $res[1]; continue; }
				
				// check if is a hardcoded form name
				if ( ereg ( "^form (.*)$", $arg, $res ) ) { $args[] = $this->__getJSFormElements( $res[1] ); continue; }

				// check if is a form name
				if ( $this->__isForm( $arg ) ){ $args[] = $this->__getJSFormElements( $arg ); continue; }
				
				// check if is a form element name
				if ( !is_null( $form = $this->__getForm( $arg ) ) ){
				
					// get element object
					$elem = & $form->getElement( $arg );
					
					// get js function name to execute
					$args[] = $this->__getJSFormElement( $elem, $options );
					
					continue;
				 }

				// otherwise is a js static string
				$args[] = "'" . htmlentities( $arg ) . "'";
			}

			// convert arguments (we don't want " and ' on code)
			return implode( ",", $args );
		}


       /**
         *	This is a helper method to compute all JS that is needed to get all elements of a form
         *
         *	@param $formName			Form name
         */		
		function __getJSFormElements( $formName ){

			$js = '';

			// check if form has wysiwyg editors
			if ( isset( $this->wysiwyg_forms[ $formName ] ) ){

                // include editor lib
                require_once( $this->wysiwyg_editorpath );

                // wysiwyg editors require to copy their html to a textarea before submit. Use JScopy static method
                foreach( $this->wysiwyg_forms[ $formName ] as $formElementID )
                    $js .= call_user_func( array( $this->wysiwyg_editorclass, 'JScopy' ), $formElementID );//YDAjaxEditor::JScopy( $formElementID );
			}
	
			// now return form values
			$js .= "return xajax.getFormValues('" . $formName . "');";

			// compute js function name
			$jsfunction = $this->prefix . 'getForm' . $formName;

			// add javascript function code to custom js (to be included in template head)
			if ( !$this->isOnResponse() ) //$this->customjs[$jsfunction . '()' ] = $js;
									  $this->template->addJavascript( "function " . $jsfunction . '()' . "{" . $js . "}\n", true );
			else                      $this->response->addScript( $jsfunction . '=function(){' . $js . '}' );
					
			// add function name to arguments list
			return $jsfunction . '()';
		}


       /**
         *	This is a helper method to compute all JS that is needed to get a form element
         *
         *	@param $formElement			Form element object
         *  @param $options             The options for the form element
         */		
		function __getJSFormElement( $formElement, $options ){

			$js = '';

			// check if this element is a wysiwyg editor
			if ( in_array( $formElement->getAttribute( 'id' ), $this->wysiwyg_ids ) ){

				// include editor lib
				require_once( dirname( __FILE__ ) . '/editors/YDAjaxEditor.php' );

				// wysiwyg editor require to copy their html to a textarea before submit
				$js .= YDAjaxEditor::JScopy( $formElement->getAttribute( 'id' ) );
			}

			// add JS return value
			$js .= $formElement->getJS( $options );					

			// compute javascript function name
			$jsfunction = $this->prefix . 'get' . $formElement->getName();

			// add javascript function code to custom js (to be included in template head)
			if ( !$this->isOnResponse() ) //$this->customjs[$jsfunction . '()' ] = $js;
									  $this->template->addJavascript( "function " . $jsfunction . '()' . "{" . $js . "}\n", true );
			else                      $this->response->addScript( $jsfunction . '=function(){' . $js . '}' );
					
			// add function name to arguments list
			return $jsfunction . '()';
		}



        /**
         *	This method will process all events added
         */	
		function processEvents(){

			// check autocompleters
			$this->__computeAutocompletersCode();

			// process all requests and exit
			return $this->processRequests();
		}


		// internal method to add all autocompleter code
		function __computeAutocompletersCode(){

			// check global flag that defines if there are autocompleters in all forms
			if ( !isset( $GLOBALS['YD_AJAX_AUTOCOMPLETERS'] ) ) return;

			// load js code
			$this->autocompleterCode = file_get_contents( dirname( __FILE__ ) . '/js/autocompleter.js' ) . "\n";

			// add css to template
			$this->template->addCss( file_get_contents( dirname( __FILE__ ) . '/js/autocompleter.css' ) );

			// find autocompleters
			foreach( $GLOBALS['YD_AJAX_AUTOCOMPLETERS'] as $formElementName ){
			
				// get form of this element
				$form = $this->__getForm( $formElementName );

				// get element
				$formElement = & $form->getElement( $formElementName );

				// compute variable name for this autocompleter
				$variable = $this->prefix . "at" . $formElement->getName();

				// compute html text id
				$textID = $formElement->getAttribute( 'id' );
			
				// compute div id
				$divID = $textID . '_div';

				// get function call
				$serverFunctionName = $formElement->getAjaxCall();

				// get ajax arguments
				$arguments = $formElement->getAjaxArguments();

				// get ajax effects
				$effects = $formElement->getAjaxEffects();

				// add js code for autocompleter
				$this->autocompleterCodeFunctions[] = $variable . " = new AutoSuggest('" . $textID . "','" . $divID . "', null);";
				$this->autocompleterCodeFunctions[] = $variable . ".ajax = function(){currentAjaxAutocompleter=this;" . $this->computeFunction( $textID, $serverFunctionName, $arguments, $effects ) . "}";
			}
		}


        /**
         *	This method add a result to a form element
         *
         *	@param $formElementName		Form element name or a generic id string.
         *	@param $result				Result string or array.
         *	@param $attribute			(Optional) Optional atribute to apply result (auto-detection by default when using null).
         *	@param $options				(Optional) Aditional options.
         */	
		function addResult( $formElementName, & $result, $attribute = null, $options = array() ){

			// if result is a string we must parse it. Javascript strings cannot contain new lines
			if ( is_string( $result ) ){

				// escape string
				$result = addslashes( $result );
				$result = str_replace( "\n", " ", $result );
				$result = str_replace( "\r", " ", $result );
			}

			// if is not a form element, assign result to a html id
			$form = & $this->__getForm( $formElementName );

			// if $formElementName is really an form element
			if ( !is_null( $form ) ){

				// if is a form element, get element
				$elem = & $form->getElement( $formElementName );

				// assign result to element
				return $this->response->addScript( $elem->setJS( $result, $attribute, $options ) );
			}

			// if $formElementName is a simple html ID
			// if atribute is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'innerHTML';

			// if result is an array we should export to a valid js string
			if (is_array( $result )) $result = str_replace( "\n", "<br>", var_export( $result, true ) );

			// compute complete result
			$result = 'document.getElementById("' . $formElementName . '").' . $attribute . ' = "' . $result . '";';
			
			// assign result to form element using the id
			return $this->response->addScript( $result );
		}



        /**
         *	This method adds wysiwyg support to an form element
         *
         *	@param $formElementId		Form element name or a simple html id
         *	@param $formname		    (Optional) Form name or null ( for autodiscovery )
         */	
		function addEditorSupport( $formElementId, $formname = null ){
		
			// check if form name is defined. If yes, convert formElemenId in form name
			if ( is_null( $formname ) ){
				$form = $this->__getForm( $formElementId );

				$formname = $form->getName();
				$elementID = $elem->getAttribute( 'id' );
			}

			// add editor
			$this->wysiwyg_forms[ $formname ][] = $formElementId;
		}


        /**
         *	This method defined the path and classname to editor JScopy static method
         *
         *	@param $path		File path
         *	@param $classname	Class name to invoke
         */	
        function setEditor( $path, $classname ){

            $this->wysiwyg_editorpath  = $path;
            $this->wysiwyg_editorclass = $classname;
        }


        /**
         *	This method will process all results added in a response
         */	
		function processResults(){

			// return XML to client browser
			return $this->response->getXML();
		}


        /**
         *	This method adds a js alert message to ajax response
         *
         *	@param $message			Message to display.
         */		
		function addAlert( $message ){
			
			$this->response->addAlert( $message );
		}


        /**
         *	This static method sends a ajax response message
         *
         *	@param $message			Message to display or array.
         */		
		function message( $message = "YDAjax error"){

			// create ajax response
			$response = new YDAjaxResponse( YDConfig::get( 'YD_AJAX_ENCODING' ) );

			// if message is an array we should export it
			if ( is_array( $message ) ) $message = var_export( $message, true );

			// add message to alert
			$response->addAlert( $message );

			// return response
			return $response;
		}


        /**
         *	This static method sends a ajax response script
         *
         *	@param $code			Javascript code
         */		
		function script( $code ){

			// create ajax response
			$response = new YDAjaxResponse( YDConfig::get( 'YD_AJAX_ENCODING' ) );

			// add message to alert
			$response->addScript( $code );

			// return response
			return $response;
		}

	}



    /**
     *  Class definition for the YDAjax response.
     */
	class YDAjaxResponse extends xajaxResponse{
	
		function YDAjaxResponse( $encoding ){
			$this->xajaxResponse( $encoding );
			$this->_end_scripts = array();
		}

		
        /**
         *	This function adds custom javascript to a response.
         *
         *	@param $js		Javascript code.
         *	@param $at_end	(Optional) Boolean that defines if code should be added only at the response end. Default: false
         */
		function addScript( $js, $at_end = false ){

			if ( $at_end ){
				$this->_end_scripts[] = $js;
			}else{
				parent::addScript( $js );
			}
		}


        /**
         *	This function private method returns all xml needed in ajax
         */
		function getXML(){
		
			foreach( $this->_end_scripts as $js )
				parent::addScript( $js );
			
			return parent::getXML();
		}
	}



    /**
     *  Class definition for YDAjax effects.
     */
    class YDAjaxEffect extends YDBase{
	
	
        /**
         *	YDAjaxEffect constructor
         *
         *	@param $id			Html element id.
         *	@param $name		Effect name (String).
         *	@param $method		Effect method (String). For effects 'hide' and 'show' method is not needed
         *	@param $duration	(Optional) Duration in ms.
         */	
		function YDAjaxEffect( $id, $name, $method = '', $duration = 400 ){

			$this->id       = $id;
			$this->name     = strtolower( $name );
			$this->method   = $method;
			$this->duration = $duration;
			
			// create js object variable name for this effect
			$this->js = YDConfig::get( 'YD_AJAX_PREFIX' ) . $this->name . $this->id;
		}
	

        /**
         *	This method returns the effect name
         */	
		function getName(){
		
			return $this->name;
		}


        /**
         *	This method returns the js variable name
         */	
		function getVariable(){
		
			return $this->js;
		}


        /**
         *	This method return the id
         */	
		function getID(){
		
			return $this->id;
		}
	

        /**
         *	This method return the js effect header
         */	
		function getJSHead( $id = null ){
			
			// if custom id is not defined we must use the standard id
			if ( is_null( $id ) ) $id = $this->id;
			
			// switch effect type
			switch( $this->name ){
			
				case 'fadesize' :	$header = $this->js . ' = new fx.FadeSize("' . $id . '", {duration: ' . $this->duration . '});';
									break;

				case 'resize' :		$header = $this->js . ' = new fx.Resize("' . $id . '", {duration: ' . $this->duration . '});';
									break;

				case 'opacity' :	$header = $this->js . ' = new fx.Opacity("' . $id . '", {duration: ' . $this->duration . '});';
									break;
									
				case 'show' :
				case 'hide' :
				case 'focus' :      $header = '';
                                    break;

				default :           die( 'Effect type ' . $this->name . ' is not supported by YDAjaxEffect' );
			}

			// return js header
			return $header;
		}


        /**
         *	This method returns the js effect body
         */	
		function getJSBody( $id = null ){

			// if custom id is not defined we must use the standard id
			if ( is_null( $id ) ) $id = $this->id;

			switch( $this->name ){
			
				// return the method invocation for standard effects
				case 'fadesize' :
				case 'resize' :
				case 'opacity' : return $this->js . '.' . $this->method . ';';
				case 'show' :    return 'document.getElementById("' . $id . '").style.display = "block";';
				case 'hide' :    return 'document.getElementById("' . $id . '").style.display = "none";';
				case 'focus' :   return 'document.getElementById("' . $id . '").focus();';
			}
		}


	}

?>
