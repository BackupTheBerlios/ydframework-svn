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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

	// set components path
	YDConfig::set( 'YD_DBOBJECT_PATH', YD_DIR_HOME_ADD . '/YDCMComponent', false );

	// add YDF libs needed by this class
	require_once( YD_DIR_HOME_ADD . '/YDDatabaseObject/YDDatabaseObject.php' );

/*

CREATE TABLE ydcmforum_categories (
   category_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   category_title varchar(100),
   category_position mediumint(8) UNSIGNED NOT NULL,
   PRIMARY KEY (category_id)
);

INSERT INTO ydcmforum_categories VALUES ( 1, 'First category', 1 );
INSERT INTO ydcmforum_categories VALUES ( 2, 'Second category', 2 );
INSERT INTO ydcmforum_categories VALUES ( 3, 'Empty category', 3 );

CREATE TABLE ydcmforum_forums (
   forum_id smallint(5) UNSIGNED NOT NULL,
   forum_category_id mediumint(8) UNSIGNED NOT NULL,
   forum_name varchar(150),
   forum_description text,
   forum_status tinyint(4) DEFAULT '0' NOT NULL,
   forum_position mediumint(8) UNSIGNED DEFAULT '1' NOT NULL,
   forum_totaltopics mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   forum_totalposts mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   forum_lastpost_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (forum_id)
);

INSERT INTO ydcmforum_forums VALUES ( 1, 1, 'forum of cat 1', 'forum description', 1, 1, 10, 200, 3);
INSERT INTO ydcmforum_forums VALUES ( 2, 2, 'forum of cat 2', 'forum cat2',        1, 1, 10, 200, 2);
INSERT INTO ydcmforum_forums VALUES ( 3, 1, 'sec forum of cat 1', 'forum num3',    1, 1, 10, 200, 1);



CREATE TABLE ydcmforum_topics (
   topic_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   topic_forum_id mediumint UNSIGNED DEFAULT '0' NOT NULL,
   topic_title char(60) NOT NULL,
   topic_user_id mediumint(8) DEFAULT '0' NOT NULL,
   topic_topicdate datetime NOT NULL,
   topic_totalviews mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_totalreplies mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   topic_status tinyint(3) DEFAULT '0' NOT NULL,
   topic_lastpost_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (topic_id)
);

INSERT INTO ydcmforum_topics VALUES ( 1, 1, 'topic title', 4, '2006-10-10 10:10', 10, 2, 1, 2 );
INSERT INTO ydcmforum_topics VALUES ( 2, 2, 'topic of f2', 6, '2006-10-10 10:10', 10, 2, 1, 1 );


CREATE TABLE ydcmforum_posts (
   post_id mediumint UNSIGNED NOT NULL auto_increment,
   post_topic_id mediumint UNSIGNED DEFAULT '0' NOT NULL,
   post_user_id mediumint DEFAULT '0' NOT NULL,
   post_postdate DATETIME NOT NULL,
   post_editdate int,
   post_content TEXT,
   PRIMARY KEY (post_id)
);



INSERT INTO ydcmforum_posts VALUES ( 1, 1, 4, '2006-10-10 10:10', null, "example" );
INSERT INTO ydcmforum_posts VALUES ( 2, 2, 6, '2006-10-12 10:12', null, "second post" );
INSERT INTO ydcmforum_posts VALUES ( 3, 1, 7, '2006-10-13 10:13', null, "third post" );


CREATE TABLE ydcmforum_announcements (
   announcement_id mediumint UNSIGNED NOT NULL auto_increment,
   announcement_user_id mediumint DEFAULT '0' NOT NULL,
   announcement_postdate DATETIME NOT NULL,
   announcement_title TEXT,
   announcement_content TEXT,
   announcement_totalviews mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
   announcement_onstart int,
   PRIMARY KEY (announcement_id)
);


INSERT INTO ydcmforum_announcements VALUES ( 1, 4, '2006-10-11 10:11', "Help: Read One",   "content txt", 101, 0 );
INSERT INTO ydcmforum_announcements VALUES ( 2, 4, '2006-10-12 10:12', "Help: Read Two",   "content txt", 102, 0 );
INSERT INTO ydcmforum_announcements VALUES ( 3, 4, '2006-10-13 10:13', "Help: Read Third", "content txt", 103, 1 );
INSERT INTO ydcmforum_announcements VALUES ( 4, 4, '2006-10-14 10:14', "Help: Read Four",  "content txt", 104, 1 );

*/

	// constants to define announcement show types. Smaller is the most important
	define( 'YD_FORUM_ANNOUNCEMENTS_ONFORUMS',            30 );
	define( 'YD_FORUM_ANNOUNCEMENTS_ONHOMEPAGEANDFORUMS', 20 );
	define( 'YD_FORUM_ANNOUNCEMENTS_ONHOMEPAGE',          10 );

    class YDCMForum_announcements extends YDDatabaseObject {
    
        function YDCMForum_announcements() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMForum_announcements' );

			// register fields
			$this->registerKey( 'announcement_id', true );
			$this->registerField( 'announcement_user_id' );
			$this->registerField( 'announcement_postdate' );
			$this->registerField( 'announcement_title' );
			$this->registerField( 'announcement_content' );
			$this->registerField( 'announcement_totalviews' );
			$this->registerField( 'announcement_onstart' );

			// init relation with user
            $rel = & $this->registerRelation( 'author', false, 'ydcmuser' );
			$rel->setLocalKey( 'announcement_user_id' );
            $rel->setForeignKey( 'user_id' );
            $rel->setForeignVar( 'author' );
		}


        /**
         *  This method return an element
         *
         *  @param $is  Id to search for
         *
         *  @returns    Element as array
         */
		function getElement( $id ){
		
			$this->reset();
			$this->set( 'announcement_id', intval( $id ) );
			$this->find();
			return $this->getValues();
		}


        /**
         *  This method returns all elements that should be displayed in the homepage
         *
         *  @returns    Element as array
         */
		function getAllOnHomepage(){
		
			$this->reset();
			$this->where( 'announcement_onstart = ' . YD_FORUM_ANNOUNCEMENTS_ONHOMEPAGE . ' OR announcement_onstart = ' . YD_FORUM_ANNOUNCEMENTS_ONHOMEPAGEANDFORUMS );
			return $this->_getElements();
		}


        /**
         *  This method returns all elements that should be displayed in forums
         *
         *  @returns    Element as array
         */
		function getAllOnForums(){
		
			$this->reset();
			$this->where( 'announcement_onstart = ' . YD_FORUM_ANNOUNCEMENTS_ONFORUMS . ' OR announcement_onstart = ' . YD_FORUM_ANNOUNCEMENTS_ONHOMEPAGEANDFORUMS );
			return $this->_getElements();
		}


        /**
         *  Private helper method to search elements
         *
         *  @returns    Element as array
         */
		function _getElements(){

			$this->order( $this->getTable() . '.announcement_postdate DESC' );
			$this->findAll();
			return $this->getResults();
		}

	}



    class YDCMForum_posts extends YDDatabaseObject {
    
        function YDCMForum_posts() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMForum_posts' );

			// register fields
			$this->registerKey( 'post_id', true );
			$this->registerField( 'post_topic_id' );
			$this->registerField( 'post_user_id' );
			$this->registerField( 'post_postdate' );
			$this->registerField( 'post_editdate' );
			$this->registerField( 'post_content' );

			// init relation with user
            $rel = & $this->registerRelation( 'author', false, 'ydcmuser' );
			$rel->setLocalKey( 'post_user_id' );
            $rel->setForeignKey( 'user_id' );
            $rel->setForeignVar( 'author' );
		}


        /**
         *  This method returns an element
         *
         *  @param $id  Id to search for
         *
         *  @returns    Element as array
         */
		function getElement( $id ){
		
			$this->reset();
			$this->set( 'post_id', intval( $id ) );
			$this->find();
			return $this->getValues();
		}


        /**
         *  This method returns an posts of a topic
         *
         *  @param $topic_id  (optional) Topic id. If null will return all elements
         *
         *  @returns    Elements as array
         */
		function getElements( $topic_id = null ){

			// check if forum_id is an array
			if ( is_array( $topic_id ) && isset( $topic_id[ 'topic_id' ] ) && is_numeric( $topic_id[ 'topic_id' ] ) ){
				$topic_id = intval( $topic_id[ 'topic_id' ] );
			}

			// reset previous info
			$this->reset();

			// if topic is set let's search post of that topic
			if ( is_numeric( $topic_id ) ){

				$this->set( 'post_topic_id', intval( $topic_id ) );
				$this->order( $this->getTable() . '.post_id ASC' );
			}

			$this->findAll();
			return $this->getResults();
		}


        /**
         *  This method returns posts of a topic in a recordset form
         *
         *  @param $topic_id  (optional) Topic id. If null will return all elements
         *  @param $page      (optional) Page to get in set
         *
         *  @returns    Elements as array
         */
		function getElementsAsRecordSet( $topic_id, $page = 1 ){
		
			return new YDRecordSet( $this->getElements( $topic_id ), $page, 2 );
		}


        /**
         *  This method creates a form for post management
         *
         *  @param $quote_id  (optional) Post id to insert in content textarea.
         *
         *  @returns    YDForm object
         */
		function & addFormNew( $quote_id = null ){
		
			// create form and add elements
			$form = new YDForm( 'ydcmforumpost' );
			$form->addElement( 'textarea', 'content', 'Content' );
			$form->addElement( 'button', 'cmdLogin', 'Submit' );

			// if we are replying to someone, let's get the content
			if ( is_numeric( $quote_id ) ){

				// get replying post
				$posts = new YDCMForum_posts();
				$post  = $posts->getElement( $quote_id );

				// apply default to content textarea
				$form->setDefault( 'content', $post[ 'post_content' ] );
			}

			return $form;
		}


	}


	// last user login date.
	YDConfig::set( 'YD_FORUM_LASTLOGINDATE', YDStringUtil::formatDate( time(), 'datetimesql' ), false );


    class YDCMForum_topics extends YDDatabaseObject {
    
        function YDCMForum_topics() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMForum_topics' );

			// register fields
			$this->registerKey( 'topic_id', true );
			$this->registerField( 'topic_forum_id' );
			$this->registerField( 'topic_title' );
			$this->registerField( 'topic_user_id' );
			$this->registerField( 'topic_topicdate' );
			$this->registerField( 'topic_totalviews' );
			$this->registerField( 'topic_totalreplies' );
			$this->registerField( 'topic_status' );
			$this->registerField( 'topic_lastpost_id' );

			// init relation with forum to get information about the forum this topic belongs
            $rel = & $this->registerRelation( 'forum', false, 'ydcmforum_forums' );
			$rel->setLocalKey( 'topic_forum_id' );
            $rel->setForeignKey( 'forum_id' );
            $rel->setForeignVar( 'forum' );

			// init relation with forum to get information about the forum this topic belongs
            $rel = & $this->registerRelation( 'category', false, 'ydcmforum_categories' );
			$rel->setLocalKey( 'forum_category_id' );
            $rel->setForeignKey( 'category_id' );
            $rel->setForeignVar( 'category' );

			// init relation with author user to get more information about the author
            $rel = & $this->registerRelation( 'author', false, 'ydcmuser' );
			$rel->setLocalKey( 'topic_user_id' );
            $rel->setForeignKey( 'user_id' );
            $rel->setForeignVar( 'author' );

			// init relation with post to get more information about the last post
            $rel = & $this->registerRelation( 'lastpostinfo', false, 'ydcmforum_posts' );
			$rel->setLocalKey( 'topic_lastpost_id' );
            $rel->setForeignKey( 'post_id' );
            $rel->setForeignVar( 'lastpostinfo' );

			// init relation with user to get information about the last post user
            $rel = & $this->registerRelation( 'lastpostauthor', false, 'ydcmuser' );
			$rel->setLocalKey( 'post_user_id' );
            $rel->setForeignKey( 'user_id' );
            $rel->setForeignVar( 'lastpostauthor' );

			// register type: "closed", "sticky", "poll", "hot", "new"
			$this->registerSelect( 'topic_type', 'IF(topic_status=0,"closed",IF(topic_status=1,"sticky",IF(topic_status=3,"poll",IF(topic_totalreplies>20,"hot","normal"))))' );

			// register old: "old" if last post was created before last visit, or "" (empty) if new
			$this->registerSelect( 'topic_old',  'IF(post_postdate<' . $this->escapeSql( YDConfig::get( 'YD_FORUM_LASTLOGINDATE' ) ) . ',"old","")' );

			// TODO: register concat

			// init counters cache
			$this->_cache_totals = null;
		}


        /**
         *  This method returns all topics
         *
         *  @param $forum_id  (optional) Forum id to search.
         *
         *  @returns    Array with elements
         */
		function getElements( $forum_id = null ){
		
			// check if forum_id is an array
			if ( is_array( $forum_id ) && isset( $forum_id[ 'forum_id' ] ) && is_numeric( $forum_id[ 'forum_id' ] ) ){
				$forum_id = intval( $forum_id[ 'forum_id' ] );
			}

			$this->reset();

			if ( is_numeric( $forum_id ) ){

				$this->set( 'topic_forum_id', intval( $forum_id ) );
				$this->order( $this->getTable() . '.topic_status ASC, ' . $this->getTable() . '.topic_id ASC' );
			}

			$this->findAll();
			return $this->getResults();
		}


        /**
         *  This method returns all elements in a recorset form
         *
         *  @param $forum_id  (optional) Forum id to search.
         *  @param $page      (optional) Page to retrive in set.
         *
         *  @returns    YDForm object
         */
		function getElementsAsRecordSet( $forum_id, $page = 1 ){
		
			return new YDRecordSet( $this->getElements( $forum_id ), $page, 20 );
		}


        /**
         *  This method returns a specific element
         *
         *  @param $id  Topic id
         *
         *  @returns    Element as array
         */
		function getElement( $id ){
		
			$this->reset();
			$this->set( 'topic_id', intval( $id ) );
			$this->findAll();
			return $this->getValues();
		}


        /**
         *  This method returns total of topics
         *
         *  @returns    Total as integer
         */
		function getTotalTopics(){

			if ( is_null( $this->_cache_totals ) ) $this->_cache_totals = $this->getTotals();

			return $this->_cache_totals[ 'totaltopics' ];
		}

        /**
         *  This method returns total of posts
         *
         *  @returns    Total as integer
         */
		function getTotalPosts(){

			if ( is_null( $this->_cache_totals ) ) $this->_cache_totals = $this->getTotals();

			return $this->_cache_totals[ 'totalposts' ];
		}


        /**
         *  Helper method to retrieve totals
         *
         *  @returns    Totals as array
         */
		function getTotals(){
		
			$this->reset();
			return $this->_db->getRecord( 'SELECT COUNT(topic_id) as totaltopics, COUNT(topic_totalreplies) as totalposts FROM ' . $this->getTable() );
		}
	}




    class YDCMForum_forums extends YDDatabaseObject {
    
        function YDCMForum_forums() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMForum_forums' );

			// register fields
			$this->registerKey( 'forum_id', true );
			$this->registerField( 'forum_category_id' );
			$this->registerField( 'forum_name' );
			$this->registerField( 'forum_description' );
			$this->registerField( 'forum_status' );
			$this->registerField( 'forum_position' );
			$this->registerField( 'forum_totaltopics' );
			$this->registerField( 'forum_totalposts' );
			$this->registerField( 'forum_lastpost_id' );

			// init relation with category to get more information about the category
            $rel = & $this->registerRelation( 'category', false, 'ydcmforum_categories' );
			$rel->setLocalKey( 'forum_category_id' );
            $rel->setForeignKey( 'category_id' );
            $rel->setForeignVar( 'category' );

			// init relation with post to get more information about the last post
            $rel = & $this->registerRelation( 'lastpostinfo', false, 'ydcmforum_posts' );
			$rel->setLocalKey( 'forum_lastpost_id' );
            $rel->setForeignKey( 'post_id' );
            $rel->setForeignVar( 'lastpostinfo' );

			// init relation with user to get information about the last post user
            $rel = & $this->registerRelation( 'lastpostauthor', false, 'ydcmuser' );
			$rel->setLocalKey( 'post_user_id' );
            $rel->setForeignKey( 'user_id' );
            $rel->setForeignVar( 'lastpostauthor' );

			// init relation with topics to get more information about the last post topic
            $rel = & $this->registerRelation( 'lasttopicinfo', false, 'ydcmforum_topics' );
			$rel->setLocalKey( 'post_topic_id' );
            $rel->setForeignKey( 'topic_id' );
            $rel->setForeignVar( 'lasttopicinfo' );

			// register old: "old" if last post was created before last visit, or "" (empty) if new
			$this->registerSelect( 'forum_old',  'IF(post_postdate<' . $this->escapeSql( YDConfig::get( 'YD_FORUM_LASTLOGINDATE' ) ) . ',"normalold","normal")' );

			// TODO: rename to register CONCAT

			$this->_elements = null;
		}


        /**
         *  This method returns all elements
         *
         *  @param $category_id  (optional) Category to search.
         *  @param $use_cache    (optional) Use result cache. True by default.
         *
         *  @returns    Elements as array
         */
		function getElements( $category_id = null, $use_cache = true ){
		
			// if we want to use cache, we must get topics from cache instead of querying db again
			if ( $use_cache ){
			
				// get all forums and convert to an associative array using category_id as key
				if ( is_null( $this->_elements ) ) $this->_elements = YDArrayUtil::convertToNested( $this->getElements( null, false ), 'forum_category_id' );

				// check if category exists
				if ( isset( $this->_elements[ intval( $category_id ) ] ) ) return $this->_elements[ $category_id ];
				else                                                       return array();
			}

			// reset previous values		
			$this->reset();

			// check if category_id is an array
			if ( is_array( $category_id ) && isset( $category_id[ 'category_id' ] ) && is_numeric( $category_id[ 'category_id' ] ) ){
				$category_id = intval( $category_id[ 'category_id' ] );
			}

			// check if we want a specific category
			if ( is_numeric( $category_id ) ){

				$this->set( 'forum_category_id', intval( $category_id ) );
				$this->order( $this->getTable() . '.forum_position ASC' );
			}

			// get elements
			$this->findAll();

			// return results
			return $this->getResults();
		}


        /**
         *  This method returns a specific element
         *
         *  @param $id  Category to search.
         *
         *  @returns    Element as array
         */
		function getElement( $id ){
		
			$this->reset();
			$this->set( 'forum_id', intval( $id ) );
			$this->findAll();
			return $this->getValues();
		}


	}



    class YDCMForum_categories extends YDDatabaseObject {
    
        function YDCMForum_categories() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMForum_categories' );

			// register fields
			$this->registerKey( 'category_id', true );
			$this->registerField( 'category_title' );
			$this->registerField( 'category_position' );
		}


        /**
         *  This method returns all elements
         *
         *  @returns    Elements as array
         */
		function getElements(){

			$this->reset();
			$this->order( $this->getTable() . '.category_position ASC' );
			$this->findAll();
			return $this->getResults();
		}


        /**
         *  This method returns a specifi element
         *
         *  @param $id  Category to search.
         *
         *  @returns    Element as array
         */
		function getElement( $id ){

			$this->reset();
			$this->set( 'category_id', intval( $id ) );
			$this->findAll();
			return $this->getValues();
		}


        /**
         *  This method creates a form for category management
         *
         *  @returns    Element as array
         */
		function & addFormNew(){
			return $this->addFormDetails( null, false );
		}


        /**
         *  Helper method to manage form handling
         *
         *  @param $id    Category id
         *  @param $edit  Boolean flag the defines if we are editing or creating
         *
         *  @returns    YDForm element
         */
		function & addFormDetails( $id, $edit ){
		
			YDInclude( 'YDForm.php' );

			$this->_form = new YDForm( 'YDCMForum_category' );
			$this->_form->addElement( 'text', 'title', t( 'ydcmforum_categories label name' ) );
			$this->_form->addElement( 'text', 'position', t( 'ydcmforum_categories label position' ) );

			if ( $edit == true ) $this->_form->addElement( 'submit', 'editit', 'Save' );
			else                 $this->_form->addElement( 'submit', 'addit',  'Add' );

			$this->_form->addFormRule( array( & $this, '_checkcategory' ), array( $edit, $id ) );

			return $this->_form;
		}


        /**
         *  Internal method to check if a category already exists 
         */
		function _checkcategory( $values, $options ){

			list( $edit, $id ) = $options;

			$this->reset();

			$this->set( 'title', $values[ 'title' ] );

			// check if we are editing.
			if ( $edit == true ){
				$this->where( 'id != ' . intval( $id ) );
			}

			if ( $this->find() == 0 ) return true;

			return array( 'title' => _( 'forum category title already exists' ) );
		}



        /**
         *  This method will save a category form in db
         *
         *  @param $formvalues   (optional) Form values
         *
         *  @returns    YDForm element
         */
		function saveFormNew( $formvalues = null ){
		
			return $this->insertForm( $this->_form, array(), $formvalues );
		}


        /**
         *  This method will delete a category from db
         *
         *  @param $category_id   Category id
         *
         *  @returns    YDResult object
         */
		function deleteElement( $category_id ){
		
			YDInclude( 'YDResult.php' );
		
			$this->reset();
			$this->set( 'id', intval( $category_id ) );
			$result = $this->delete();
			
			if ( $result ){ return YDResult::ok( t( 'forum category deleted' ), $result ); }
			else{          return YDResult::fatal( t( 'impossible to delete forum category' ), $result ); }
		}


	}



?>