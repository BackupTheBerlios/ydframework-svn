<?php

	// include global parameters
    include_once( dirname( __FILE__ ) . '/../cm.php' );

	// include libs
    YDInclude( 'YDRequest.php' );
	YDInclude( 'YDCMPermission.php' );


    // Class definition
    class permissions extends YDRequest {

        // Class constructor
        function permissions() {

			// init parent class
            $this->YDRequest();

			// create a permissions object
			$this->perms = new YDCMPermission();

			// register system permissions (read: define here all possible permissions)
			$this->perms->registerAction( 'YDCMPage',  'create' );
			$this->perms->registerAction( 'YDCMPage',  'delete' );
			$this->perms->registerAction( 'YDCMGroup', 'add' );
			$this->perms->registerAction( 'YDCMGroup', 'edit' );

            print(" We have registered the next permissions :                        <br>
                    'YDCMPage',  'create'                                            <br>
                    'YDCMPage',  'delete'                                            <br>
                    'YDCMGroup', 'add'                                               <br>
                    'YDCMGroup', 'edit'                                              <br><br>");


            print(" In our database we have the next permission system:              <br>
                    INSERT INTO `ydcmpermission` VALUES (2,'YDCMRootmenu','delete'); <br>
                    INSERT INTO `ydcmpermission` VALUES (2,'YDCMPage','delete');     <br>
                    INSERT INTO `ydcmpermission` VALUES (2,'YDCMPage','create');     <br>
                    INSERT INTO `ydcmpermission` VALUES (5,'YDCMPage','delete');     <br>
                    INSERT INTO `ydcmpermission` VALUES (5,'YDCMPage','create');     <br><br>");
        }


        // Default action
        function actionDefault() {

			// check if userobject 2 can 'YDCMPage'+'delete'
			YDDebugUtil::dump( $this->perms->can( 2, 'YDCMPage', 'delete' ), '$this->perms->can( 2, "YDCMPage", "delete" );  // check if userobject 2 can "YDCMPage"+"delete"' );

			// check if userobject 5 can 'YDCMPage'+'add'
			YDDebugUtil::dump( $this->perms->can( 5, 'YDCMPage', 'add' ), '$this->perms->can( 5, "YDCMPage", "add" );  // check if userobject 5 can "YDCMPage"+"add"' );

			// check if userobject 2 can 'YDCMRootmenu'+'delete'
			YDDebugUtil::dump( $this->perms->can( 2, 'YDCMRootmenu', 'delete' ), '$this->perms->can( 2, "YDCMRootmenu", "delete" );  // action is in DB but its NOT registered' );

			// get permissions of userobject 2
			YDDebugUtil::dump( $this->perms->getPermissions( 2 ), '$this->perms->getPermissions( 2 );' );

			// get translated permissions of userobject 5
			YDDebugUtil::dump( $this->perms->getPermissions( 5, true ), '$this->perms->getPermissions( 5, true );  // translated permissions' );

			// get registered system actions
			YDDebugUtil::dump( $this->perms->getRegisteredActions(), '$this->perms->getRegisteredActions();  // get registered system actions' );

			// delete all actions of userobject 10
			YDDebugUtil::dump( $this->perms->deletePermissions( 10 ), '$this->perms->deletePermissions( 10 );  // try to delete all permissions of userobject 10. userobject do NOT exist' );

			// add permission 'YDCMPage'+'create' to userobject 8
			YDDebugUtil::dump( $this->perms->addPermission( 8, 'YDCMPage', 'create' ), '$this->perms->addPermission( 8, "YDCMPage", "create" );  // add permission "YDCMPage"+"create" to userobject 8' );

			// add permission 'YDCMPage'+'publish' to userobject 8
			YDDebugUtil::dump( $this->perms->addPermission( 8, 'YDCMPage', 'publish' ), '$this->perms->addPermission( 8, "YDCMPage", "publish" );  // "YDCMPage"+"publish" is not registered' );
		}


		function actionAlias(){
		
            print(" Alias registered :                       <br>
                    'createcontent' => 'YDCMPage', 'create'  <br>
                    'justdelete'    => 'YDCMPage', 'delete'  <br><br>");

			// register some alias
			$this->perms->registerAlias( 'createcontent', 'YDCMPage', 'create' );
			$this->perms->registerAlias( 'justdelete',    'YDCMPage', 'delete' );


			// check if userobject 2 can 'YDCMPage'+'delete'
			YDDebugUtil::dump( $this->perms->can( 2, 'YDCMPage', 'delete' ), '$this->perms->can( 2, "YDCMPage", "delete" );  // check if userobject 2 can "YDCMPage"+"delete"' );

			// check if userobject 2 can 'YDCMPage'+'justdelete'
			YDDebugUtil::dump( $this->perms->can( 2, 'YDCMPage', 'justdelete' ), '$this->perms->can( 2, "YDCMPage", "justdelete" );  // check if userobject 2 can "YDCMPage"+"justdelete"' );


			// add a new alias that point to an alias :)
            print(" Added new alias (points to a valid alias) :      <br>
                    'mycustomdelete'    => 'YDCMPage', 'justdelete'  <br><br>");

			$this->perms->registerAlias( 'mycustomdelete', 'YDCMPage', 'justdelete' );

			// check if userobject 2 can 'YDCMPage'+'mycustomdelete'
			YDDebugUtil::dump( $this->perms->can( 2, 'YDCMPage', 'mycustomdelete' ), '$this->perms->can( 2, "YDCMPage", "mycustomdelete" );  // check if userobject 2 can "YDCMPage"+"mycustomdelete"' );


			// get all registered alias
			YDDebugUtil::dump( $this->perms->getRegisteredAlias(), '$this->perms->getRegisteredAlias();  // get all registered alias' );
		}


		function actionAlwaysRegistered(){

            print(" Registered 'always valid' permissions    <br>
                    'YDCMPage', 'closeit'                    <br><br>");

			// register some always registered
			$this->perms->registerAlwaysValidAction( 'YDCMPage', 'closeit' );

			// check if userobject 2 can 'YDCMPage'+'closeit'
			YDDebugUtil::dump( $this->perms->can( 2, 'YDCMPage', 'closeit' ), '$this->perms->can( 2, "YDCMPage", "closeit" );  // check if userobject 2 can "YDCMPage"+"closeit"' );

		}

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
