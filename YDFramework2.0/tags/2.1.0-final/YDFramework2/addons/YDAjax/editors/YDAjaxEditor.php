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


    /**
     *  This config defines the url path to fckeditor.js (excluding filename). Eg: "/web/fckeditor/"
     *  Default: "/fckeditor/"
     */
    YDConfig::set( 'YD_AJAXEDITOR_FCKEDITOR_Url', "/fckeditor/", false );


    /**
     *  This config defines the editor skin name
     *  Default: "office2003"
     */
    YDConfig::set( 'YD_AJAXEDITOR_FCKEDITOR_Skin', "silver", false );


    /**
     *  This config defines if the toolbar should start expanded
     *  Default: true
     */
    YDConfig::set( 'YD_AJAXEDITOR_FCKEDITOR_ToolbarStartExpanded', true, false );


    /**
     *  This config defines the toolbar scheme to use, eg, 'Default', 'Basic'
     *  Default: 'Default'
     */
    YDConfig::set( 'YD_AJAXEDITOR_FCKEDITOR_ToolbarSet', 'Default', false );


    /**
     *  Class definition for the YDAjaxEditor.
     */
    class YDAjaxEditor{

        /**
         *	This is the method returns the JS initialization of the editor
         *
         */
        function JSinit( $htmlID ){
		
			// start by creating an FCKeditor object
			$js  = "oFCKeditor = new FCKeditor( '" . $htmlID ."' );";
			
			// set the editor path
			$js .= "oFCKeditor.BasePath = '" . YDConfig::get( 'YD_AJAXEDITOR_FCKEDITOR_Url' ) . "';";
			
			// set editor skin path
			$js .= "oFCKeditor.Config['SkinPath'] = oFCKeditor.BasePath + 'editor/skins/" . YDConfig::get( 'YD_AJAXEDITOR_FCKEDITOR_Skin' ) . "/';";

			// set toolbar expanded on start
			$js .= YDConfig::get( 'YD_AJAXEDITOR_FCKEDITOR_ToolbarStartExpanded' ) ? "oFCKeditor.Config['ToolbarStartExpanded'] = true;" : "oFCKeditor.Config['ToolbarStartExpanded'] = false;";

			// set toolbar scheme
			$js .= "oFCKeditor.ToolbarSet = '" . YDConfig::get( 'YD_AJAXEDITOR_FCKEDITOR_ToolbarSet' ) . "';";

			// on fckeditor we add a replace method (that will replace the textarea)
			$js .= "oFCKeditor.ReplaceTextarea();";

			return $js;
		}

	

        /**
         *	This is the method returns the JS mechanism of content copy from editor to textarea
         */
		function JScopy( $htmlID ){

			return "document.getElementById('" . $htmlID . "').value = FCKeditorAPI.GetInstance('" . $htmlID . "').GetXHTML(true);";
		}

	}

?>
