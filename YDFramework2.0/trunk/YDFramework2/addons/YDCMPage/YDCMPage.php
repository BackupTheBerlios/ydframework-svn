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

	// add local translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );

	// set page form name
	YDConfig::set( 'YDCMPAGE_FORMPAGE', 'YDCMPageForm', false );


    class YDCMPage extends YDCMComponent {
    
        function YDCMPage() {

			// init component
            $this->YDCMComponent( 'YDCMPage' );

            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a page component';

            // define custom fields
			$this->registerField( 'current_version' );
			$this->registerField( 'title' );
			$this->registerField( 'html' );
			$this->registerField( 'xhtml' );
			$this->registerField( 'template_pack' );
			$this->registerField( 'template' );
			$this->registerField( 'metatags' );			
			$this->registerField( 'description' );
			$this->registerField( 'keywords' );			

			// we don't have custom relations
		}
		

        /**
         *  This function returns the page form
         *
         *  @returns    YDForm object
         */
		function getFormPage(){
		
			YDInclude( 'YDForm.php' );
			YDInclude( 'YDCMTemplates.php' );
		
			// create access options
			$access = array( 0 => t('public'), 1 => t('private') );

			// get all possible visitors templates
			$templates = new YDCMTemplates();

			// create 'template pack' options
			$template_pack = array(	1	=> t('use templatepack') .' ('. $templates->template_pack() .')',
									0	=> t('use custom template') );
	
			// create form object
			$form = new YDForm( YDConfig::get( 'YDCMPAGE_FORMPAGE' ) );

	        $form->addElement( 'text',           'reference',            t('page_reference'),      array('size' => 25, 'maxlength' => 35) );
	        $form->addElement( 'text',           'title',                t('page_title'),          array('size' => 70, 'maxlength' => 70) );
	        $form->addElement( 'hidden',         'content',              t('page_content') );
	        $form->addElement( 'select',         'access',               t('page_access'),         array(), $access);
	        $form->addElement( 'select',         'published',            t('page_published'),      array(), array(1 => t('yes'), 0 => t('no'), 2 => t('schedule')) );
	        $form->addElement( 'datetimeselect', 'published_date_start', t('page_startdate') );
	        $form->addElement( 'datetimeselect', 'published_date_end',   t('page_enddate'));
	        $form->addElement( 'select',         'template_pack',        '',                       array(), $template_pack );
	        $form->addElement( 'select',         'template',             t('page_template'),       array(), $templates->visitors_templates());
	        $form->addElement( 'select',         'metatags',             t('page_metatags'),       array(), array(0 => t('no'), 1 => t('yes')));
	        $form->addElement( 'textarea',       'description',          t('page_description'),    array('cols' => 50, 'rows' => 5) );
	        $form->addElement( 'textarea',       'keywords',             t('page_keywords'),       array('cols' => 50, 'rows' => 5) );
	        $form->addElement( 'select',         'search',               t('page_search'),         array(), array(0 => t('no'), 1 => t('yes')) );
	        $form->addElement( 'hidden',         'content_id' );
	        $form->addElement( 'hidden',         'parent_id' );

			// parent of new page is 0 by default
			$form->setDefault( 'content_id', 0 );
			$form->setDefault( 'parent_id', 0 );

			return $form;
		}


        /**
         *  This function returns the page sttributes
         *
         *  @returns    Node id
         */
		function getNode( $id ){
		
			// delete previous stored values
			$this->resetValues();

			// define our id to find		
			$this->content_id = intval( $id );
			
			// find node
			$this->findAll();
			
			// return node attributes without relation prefixs
			return $this->getValues( false, false, false, false );
		}

    }
?>