<?php

    include_once( dirname( __FILE__ ) . '/includes/config.php' );

    class index extends YDDatabaseObjectRequest {
        
        function index() {
            $this->YDDatabaseObjectRequest();
        }
        
        function actionGroups() {
            
            $group = YDDatabaseObject::getInstance( 'group' );
            
            $group->executeSql( 'TRUNCATE `' . $group->getTable() . '`' );
            
            $group->name = 'Group A';
            $group->active = 1;
            $group->insert();
            
            $this->template->assign( 'group1', $group );
            $this->template->assign( 'group1_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->reset();
            $group->name = 'Group B';
            $group->active = 1;
            $group->insert();
            
            $this->template->assign( 'group2', $group );
            $this->template->assign( 'group2_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->reset();
            $group->name = 'Group C';
            $group->active = 1;
            $group->insert();
            
            $this->template->assign( 'group3', $group );
            $this->template->assign( 'group3_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->reset();
            $group->name = 'Group D';
            $group->active = 0;
            $group->insert();
            
            $this->template->assign( 'group4', $group );
            $this->template->assign( 'group4_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->reset(); // resets all data
            $group->find();
            
            $results = array();
            while( $group->fetch() ) {
                $results[] = YDDebugUtil::r_dump( $group->getValues() );
            }
            
            $this->template->assign( 'find1_results', implode( '', $results ) );
            $this->template->assign( 'find1_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->reset(); // resets all data
            $group->active = 0;
            $group->find();
            
            $results = array();
            while( $group->fetch() ) {
                $results[] = YDDebugUtil::r_dump( $group->getValues() );
            }
            
            $this->template->assign( 'find2_results', implode( '', $results ) );
            $this->template->assign( 'find2_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->active = 1;
            $group->update();
            $this->template->assign( 'group4_update_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->delete();
            $this->template->assign( 'group4_delete_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $group->reset(); // resets all data
            $group->find();
            $results = YDDebugUtil::r_dump( $group->getResults() );
            $this->template->assign( 'find3_results', $results );
            
            $group->reset();
            $group->find();
            $results = YDDebugUtil::r_dump( $group->getResultsAsAssocArray( 'id', 'name' ) );
            $this->template->assign( 'find4_results', $results );
            
            $group->reset();
            $group->find();
            $results = YDDebugUtil::r_dump( $group->getResultsAsAssocArray( 'id', array( 'name', 'active' ) ) );
            $this->template->assign( 'find5_results', $results );
            
            $this->template->display( 'groups' );
            
        }
        
        function actionUsers() {
            
            $user = YDDatabaseObject::getInstance( 'user' );
            
            $user->executeSql( 'TRUNCATE `' . $user->getTable() . '`' );
            
            $user->name = 'User A';
            $user->email = 'usera@host.com';
            $user->country = 'Brazil';
            $user->is_admin = 1;
            $user->birthday = 19700516;
            $user->insert(); // {$insert1_sql}
            $this->template->assign( 'insert1_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user->reset();
            $user->name = 'User B';
            $user->email = 'userb@host.com';
            $user->country = 'Brazil';
            $user->is_admin = 0;
            $user->birthday = 19500516;
            $user->insert(); // {$insert2_sql}
            $this->template->assign( 'insert2_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user->reset();
            $user->name = 'User C';
            $user->email = 'userc@host.com';
            $user->country = 'Colombia';
            $user->birthday = 19811120;
            $user->insert(); // {$insert3_sql}
            $this->template->assign( 'insert3_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user->reset();
            $user->name = 'User A';
            $user->find();
            $results = $user->getResults();
            $this->template->assign( 'find1_results', YDDebugUtil::r_dump( $results ) );
            
            $birth_year = & $user->getSelect( 'birth_year' );
            $this->template->assign( 'birth_year_sql', $birth_year->getName() . ' = ' . $birth_year->getExpression() );
            
            $birthday = & $user->getField( 'birthday' );
            $this->template->assign( 'birthday_callback', 'callback = ' . $birthday->getCallback() );
            
            $user->set( 'birthday', 19600516 ); // 10 year older
            $this->template->assign( 'age_value', 'age = ' . $user->get( 'age' ) );
            
            $user->update();
            $this->template->assign( 'update1_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $this->template->display( 'users' );
            
        }
        
        function actionAdmins() {
            
            $admin = YDDatabaseObject::getInstance( 'admin' );
            $this->template->assign( 'find1_results', YDDebugUtil::r_dump( $admin->getValues() ) );
            
            $admin->find();
            $this->template->assign( 'find2_results', YDDebugUtil::r_dump( $admin->getResults() ) );
            
            $admin->set( 'is_admin', 0 );
            $this->template->assign( 'change1_results', YDDebugUtil::r_dump( $admin->getValues() ) );
            
            $admin->is_admin = 0;
            $this->template->assign( 'change2_results', YDDebugUtil::r_dump( $admin->getValues() ) );
            
            $this->template->display( 'admins' );
            
        }
        
        function actionPhones() {
            
            $phone = YDDatabaseObject::getInstance( 'phone' );
            $user = YDDatabaseObject::getInstance( 'user' );

            $phone->executeSql( 'TRUNCATE `' . $phone->getTable() . '`' );
            
            $phone = YDDatabaseObject::getInstance( 'phone' );

            $phone->user_id = 1;
            $phone->number = '1111-1111';
            $phone->ord = 1;
            $phone->insert(); // {$insert1_sql}
            $this->template->assign( 'insert1_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->number = '1222-2222';
            $phone->ord = 2; 
            $phone->insert(); // {$insert2_sql}
            $this->template->assign( 'insert2_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->number = '1333-3333';
            $phone->ord = 3;
            $phone->insert(); // {$insert3_sql}
            $this->template->assign( 'insert3_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->user_id = 2;
            $phone->number = '2444-4444';
            $phone->ord = 1;
            $phone->insert(); // {$insert4_sql}
            $this->template->assign( 'insert4_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->number = '2555-5555';
            $phone->ord = 2; 
            $phone->insert(); // {$insert5_sql}
            $this->template->assign( 'insert5_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->number = '2666-6666';
            $phone->ord = 3;
            $phone->insert(); // {$insert6_sql}
            $this->template->assign( 'insert6_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->user_id = 3;
            $phone->number = '3777-7777';
            $phone->ord = 1;
            $phone->insert(); // {$insert7_sql}
            $this->template->assign( 'insert7_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->number = '3888-8888';
            $phone->ord = 2; 
            $phone->insert(); // {$insert8_sql}
            $this->template->assign( 'insert8_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $phone->number = '3999-9999';
            $phone->ord = 3;
            $phone->insert(); // {$insert9_sql}
            $this->template->assign( 'insert9_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user->id = 1;
            $user->find( 'phone' );
            $results = $user->getResults();
            $this->template->assign( 'find1_results', YDDebugUtil::r_dump( $results ) );
            
            $user->resetAll();
            $user->id = 1;
            $user->find( 'phone' );
            $user_results = $user->getResults( true );
            $phone_results = $user->phone->getResults( true );
            $this->template->assign( 'find2_user_results', YDDebugUtil::r_dump( $user_results ) );
            $this->template->assign( 'find2_phone_results', YDDebugUtil::r_dump( $phone_results ) );
            
            $user = YDDatabaseObject::getInstance( 'user' );
            $user->load( 'phone' ); // now we have a phone object in $user
            
            $user->resetQuery(); // resets default query statements
            $user->phone->resetQuery();
            
            $user->addSelect( 'id', 'name', 'country' );
            $user->phone->addSelect( 'number', 'ord' );

            $user->addWhere( $user->getTable() . ".country LIKE 'b%'" );
            $user->addOrder( $user->phone->getTable() . '.ord' );
            $user->setLimit( 2 );
            
            $user->find( 'phone' );
            $this->template->assign( 'find3_results', YDDebugUtil::r_dump( $user->getResults() ) );
            
            $this->template->display( 'phones' );
        }
        
        function actionUsersGroups() {
            
            $user = YDDatabaseObject::getInstance( 'user' );
            $user_group = YDDatabaseObject::getInstance( 'user_group' );

            $user_group->executeSql( 'TRUNCATE `' . $user_group->getTable() . '`' );
            
            $user_group->user_id = 1;
            $user_group->group_id = 1;
            $user_group->joined = 20050515;
            $user_group->active = 1;
            $user_group->insert(); // {$insert1_sql}
            $this->template->assign( 'insert1_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user_group->group_id = 2;
            $user_group->joined = 20050513;
            $user_group->active = 1;
            $user_group->insert(); // {$insert2_sql}
            $this->template->assign( 'insert2_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user_group->group_id = 3;
            $user_group->joined = 20050514;
            $user_group->active = 0;
            $user_group->insert(); // {$insert3_sql}
            $this->template->assign( 'insert3_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user_group->user_id = 2;
            $user_group->group_id = 1;
            $user_group->joined = 20050412;
            $user_group->active = 0;
            $user_group->insert(); // {$insert4_sql}
            $this->template->assign( 'insert4_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user_group->group_id = 3;
            $user_group->joined = 20050830;
            $user_group->active = 1;
            $user_group->insert(); // {$insert5_sql}
            $this->template->assign( 'insert5_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user_group->user_id = 3;
            $user_group->group_id = 2;
            $user_group->joined = 20040101;
            $user_group->active = 1;
            $user_group->insert(); // {$insert6_sql}
            $this->template->assign( 'insert6_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user_group->group_id = 3;
            $user_group->joined = 20051225;
            $user_group->active = 1;
            $user_group->insert(); // {$insert7_sql}
            $this->template->assign( 'insert7_sql', end( $GLOBALS['YD_SQL_QUERY'] ) );
            
            $user->id = 1;
            $user->find( 'group' );
            $this->template->assign( 'find1_results', YDDebugUtil::r_dump( $user->getResults() ) );
            
            $user->resetAll();
            $user->id = 1;
            $user->find( 'group' );
            
            $results = array();
            while ( $user->fetch() ) {
                $results[] = array ( 'user_id'      => $user->id,
                                     'user_name'    => $user->name,
                                     'group_id'     => $user->group->id,
                                     'group_name'   => $user->group->name,
                                     'group_active' => $user->group->active,
                                     'user_active'  => $user->user_group->active,
                                     'user_joined'  => $user->user_group->joined );
            }
            
            $this->template->assign( 'find2_results', YDDebugUtil::r_dump( $results ) );
            
            $user->resetAll();
            $user->id = 3;
            $user->group->active = 1;
            $user->user_group->active = 1;
            
            $user->find( 'group' );
            $this->template->assign( 'find3_results', YDDebugUtil::r_dump( $user->getResults() ) );
            
            $this->template->display( 'users_groups' );
            
        }
        
        function actionUsersAll() {
            
            $user = YDDatabaseObject::getInstance( 'user' );
            
            $user->id = 1;
            $user->findAll();
            
            $this->template->assign( 'find1_results', YDDebugUtil::r_dump( $user->getResults() ) );
            
            $user->resetAll();
            $user->id = 1;
            $user->find( 'phone', 'group' );
            
            $this->template->assign( 'find2_results', YDDebugUtil::r_dump( $user->getResults() ) );
            
            $this->template->display( 'users_all' );
            
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>