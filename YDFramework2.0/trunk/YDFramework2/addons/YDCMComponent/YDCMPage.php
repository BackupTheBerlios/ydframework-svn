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

    /*
     *  @addtogroup YDCMComponent Addons - CMComponent
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

//	YDInclude( 'YDCMComponent.php' );

	// include YDF libs
    YDInclude( 'YDCMComponent.php' );
//    YDInclude( 'YDCMTree.php' );

	// add local translation directory
//	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/YDCMPage/' );

	// set page form name
//	YDConfig::set( 'YDCMPAGE_FORMPAGE', 'YDCMPageForm', false );

    /**
     *  @ingroup YDCMComponent
     */
    class YDCMPage extends YDCMComponent{
    
        function YDCMPage() {

			// init DB object
            $this->YDCMComponent( 'YDCMPage' );

/*            $this->_author = 'unknown author';
            $this->_version = 'unknown version';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMPage' );

            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a page component';
*/
            // define custom fields
//			$this->registerKey( 'component_id', false );
//			$this->registerField( 'current_version' );
			$this->registerField( 'page_html',        false, 'textarea', _('ydcmuser label html'),        array( 'cols' => 50, 'rows' => 5 ) );
//			$this->registerField( 'xhtml' );
//			$this->registerField( 'template_pack' );
			$this->registerField( 'page_template',    false, 'select',   _('ydcmuser label template') );
			$this->registerField( 'page_metatags',    false, 'select',   _('ydcmuser label metatags'),    array(), array( 0 => _('no'), 1 => _('yes') ) );
			$this->registerField( 'page_description', false, 'textarea', _('ydcmuser label description'), array( 'cols' => 50, 'rows' => 5 ) );
			$this->registerField( 'page_keywords',    false, 'textarea', _('ydcmuser label keywords'),    array( 'cols' => 50, 'rows' => 5 ) );

/*

	        $form->addElement( 'text',           'reference',            t('ydcmuser label reference'),      array('size' => 25, 'maxlength' => 35) );
	        $form->addElement( 'text',           'title',                t('ydcmuser label title'),          array('size' => 70, 'maxlength' => 70) );
	        $form->addElement( 'textarea',       'content',              t('ydcmuser label content') );
	        $form->addElement( 'select',         'access',               t('ydcmuser label access'),         array(), $access);
	        $form->addElement( 'select',         'state',                t('ydcmuser label state'),          array(), array(1 => t('yes'), 0 => t('no'), 2 => t('schedule')) );
	        $form->addElement( 'datetimeselect', 'published_date_start', t('ydcmuser label published_date_start') );
	        $form->addElement( 'datetimeselect', 'published_date_end',   t('ydcmuser label published_date_end'));
	        $form->addElement( 'select',         'template',             t('ydcmuser label template'),       array(), $templates->visitors_templates());
	        $form->addElement( 'select',         'metatags',             t('ydcmuser label metatags'),       array(), array( 0 => t('no'), 1 => t('yes') ) );
	        $form->addElement( 'textarea',       'description',          t('ydcmuser label description'),    array( 'cols' => 50, 'rows' => 5 ) );
	        $form->addElement( 'textarea',       'keywords',             t('ydcmuser label keywords'),       array( 'cols' => 50, 'rows' => 5 ) );
	        $form->addElement( 'select',         'searcheable',          t('ydcmuser label searcheable'),         array(), array( 0 => t('no'), 1 => t('yes') ) );
*/


			// virtual relation with ydcmcomp
/*       		$rel = & $this->registerRelation( 'ydcmcomp', false, 'ydcmcomp' );
			$rel->setLocalKey( 'ydcmpage.component_id' );
			$rel->setForeignKey( 'ydcmcomp.component_id' );

			// virtual relation with ydcmtree
       		$rel = & $this->registerRelation( 'ydcmtree', false, 'ydcmtree' );
			$rel->setLocalKey( 'ydcmpage.component_id' );
			$rel->setForeignKey( 'ydcmtree.content_id' );
*/
		}

		
/*
		function getElement( $id ){

			$this->reset();
			$this->load( 'ydcmcomp' );
			$this->ydcmcomp->set( 'reference', $id );
//			$this->set( 'component_id', intval( $id ) );
			$this->findAll();
			return $this->getValues( false, false, false, false );
		}
*/

        /**
         *  This function will render this element (to be used in a menu)
         *
         *  @returns    Html link
         */
/*		function render( $component, & $url ){

			$url->setQueryVar( 'component', $component[ 'type' ] );
			$url->setQueryVar( 'id',        $component[ 'content_id' ] );

			return '<a href="' . $url->getUrl() . '">' . $component[ 'title' ] . '</a>';
		}

		function renderContent( $id ){
		
			$element = $this->getElement( $id );
		
			return $element[ 'html' ];
		}
*/

        /**
         *  This function returns the page form (form admin edition)
         *
         *  @returns    YDForm object
         */
		function _getForm(){

			YDInclude( 'YDForm.php' );

			// get template and language object
			$templates = new YDCMTemplates();
			$languages = new YDCMLanguages();

			// create access options
			$access = array( 0 => t('public'), 1 => t('private') );

			// create 'template pack' options
			$template_pack = array(	1	=> t('use templatepack') . ' (' . $templates->template_pack() . ')',
									0	=> t('use custom template') );
	
			// create form object
			$form = new YDForm( 'page' );

	        $form->addElement( 'text',           'reference',            t('ydcmuser label reference'),      array('size' => 25, 'maxlength' => 35) );
	        $form->addElement( 'text',           'title',                t('ydcmuser label title'),          array('size' => 70, 'maxlength' => 70) );
	        $form->addElement( 'textarea',       'content',              t('ydcmuser label content') );
	        $form->addElement( 'select',         'access',               t('ydcmuser label access'),         array(), $access);
	        $form->addElement( 'select',         'state',                t('ydcmuser label state'),          array(), array(1 => t('yes'), 0 => t('no'), 2 => t('schedule')) );
	        $form->addElement( 'datetimeselect', 'published_date_start', t('ydcmuser label published_date_start') );
	        $form->addElement( 'datetimeselect', 'published_date_end',   t('ydcmuser label published_date_end'));
	        $form->addElement( 'select',         'template',             t('ydcmuser label template'),       array(), $templates->visitors_templates());
	        $form->addElement( 'select',         'metatags',             t('ydcmuser label metatags'),       array(), array( 0 => t('no'), 1 => t('yes') ) );
	        $form->addElement( 'textarea',       'description',          t('ydcmuser label description'),    array( 'cols' => 50, 'rows' => 5 ) );
	        $form->addElement( 'textarea',       'keywords',             t('ydcmuser label keywords'),       array( 'cols' => 50, 'rows' => 5 ) );
	        $form->addElement( 'select',         'searcheable',          t('ydcmuser label searcheable'),         array(), array( 0 => t('no'), 1 => t('yes') ) );

			// parent of new page is 0 by default
			$form->setDefault( 'language_id', $languages->adminDefault() );

			// add form rules
			$form->addRule( 'reference',      'required',       t('reference_required') );
			$form->addRule( 'reference',      'alphanumeric',   t('reference_alphanumeric') );
			$form->addRule( 'reference',      'maxwords',       t('reference_maxwords'),  1 );
			$form->addRule( 'reference',      'maxlength',      t('reference_maxlength'), 100 );
			$form->addRule( 'title',          'required',       t('title_required') );
			$form->addRule( 'title',          'maxlength',      t('title_maxlength'), 255 );
			$form->addRule( 'content_id',     'required',       t('content_id_required') );
			$form->addRule( 'content_id',     'numeric',        t('content_id_numeric') );
			$form->addRule( 'parent_id',      'required',       t('parent_id_required') );
			$form->addRule( 'parent_id',      'numeric',        t('parent_id_numeric') );
			$form->addRule( 'html',           'maxlength',      t('html_maxlength'),  50000 );
			$form->addRule( 'xhtml',          'maxlength',      t('xhtml_maxlength'), 50000 );
			$form->addRule( 'template_pack',  'in_array',       t('template_pack_invalid'), array( 0, 1 ) );
			$form->addRule( 'template',       'in_array',       t('template_invalid'),      array_keys( $templates->visitors_templates() ) );
			$form->addRule( 'metatags',       'in_array',       t('metatags_invalid'),      array( 0, 1 ) );
			$form->addRule( 'description',    'maxlength',      t('description_maxlength'), 2000 );
			$form->addRule( 'keywords',       'maxlength',      t('keywords_maxlength'),    2000 );

			return $form;
		}



        /**
         *  This function adds/updates a page (if content_id is 0 will add a page, otherwise will update)
         *
         *  @param $formvalues  Array with page attributes from the standard form
         *
         *  @returns    content_id updated or new content_id
         */
		function savePageForm( $formvalues = array() ){

			// get page YDForm object
			$form = $this->getFormPage();

			// check form validation
			if ( !$form->validate( $formvalues ) )
				return $form->getErrors();

			// init node values
			$node = array();			
			$node[ 'type' ]        = 'YDCMPage';
			$node[ 'reference' ]   = $form->getValue( 'reference' );
			$node[ 'state' ]       = $form->getValue( 'state' );
			$node[ 'access' ]      = $form->getValue( 'access' );
			$node[ 'searcheable' ] = $form->getValue( 'searcheable' );

			// convert published date start/end timestamp to a db datetime format
			$node[ 'published_date_start' ] = YDStringUtil::formatDate( $form->getValue( 'published_date_start' ), 'datetimesql' );
			$node[ 'published_date_end' ]   = YDStringUtil::formatDate( $form->getValue( 'published_date_end' ),   'datetimesql' );

			// get id of this node
			$id = $form->getValue( 'content_id' );

			// if content_id is 0 we are trying to add a new node, otherwise we only need to update it
			if ( $id == 0 ) $id = $this->addNode(    $node, intval( $form->getValue( 'parent_id' ) ) );
			else                  $this->updateNode( $node, intval( $form->getValue( 'content_id' ) ) );
			
			// create page
			$page = array();

			$page[ 'content_id' ]    = $id;
			$page[ 'language_id' ]   = $form->getValue( 'language_id' );
			$page[ 'title' ]         = $form->getValue( 'title' );
			$page[ 'html' ]          = $form->getValue( 'html' );
			$page[ 'xhtml' ]         = $form->getValue( 'xhtml' );
			$page[ 'template_pack' ] = $form->getValue( 'template_pack' );
			$page[ 'template' ]      = $form->getValue( 'template' );
			$page[ 'metatags' ]      = $form->getValue( 'metatags' );
			$page[ 'description' ]   = $form->getValue( 'description' );
			$page[ 'keywords' ]      = $form->getValue( 'keywords' );

			// TODO: care about version control
			$page[ 'current_version' ] = 1;
						
			// add page
			$this->resetValues();
			
			$this->setValues( $page );

			$this->insert();
			
			return $id;
		}


        /**
         *  This function will delete a page node (and not its children)
         *
         *  @param $id  Node id
         */
		function deleteElement( $id ){

			// delete all element from this node
			$this->resetValues();

			$this->content_id = intval( $id );
			$this->delete();
			
			// delete node from tree
			$this->deleteNode( $id );
		}

    }
?>