{include file="header.tpl"}

    <h3>Working with many-to-many relationships</h3>

    <p>This type of relation needs an additional YDDatabaseObject class (table) to handle the cross-reference between the local and foreign classes. When we load this type of relation, two classes are instantiated: the <b>foreign</b> and <b>cross</b> classes.</p>
    
    <p>In this example, we have the <b>user</b> and <b>group</b> classes linked by the <b>user_group</b> class. Let's add some users to some groups using this class.</p>
    
<pre>
$user_group = YDDatabaseObject::getInstance( 'user_group' );

$user_group->user_id = 1;
$user_group->group_id = 1;
$user_group->joined = 20050515;
$user_group->active = 1;
$user_group->insert(); // {$insert1_sql}

$user_group->group_id = 2;
$user_group->joined = 20050513;
$user_group->active = 1;
$user_group->insert(); // {$insert2_sql}

$user_group->group_id = 3;
$user_group->joined = 20050514;
$user_group->active = 0;
$user_group->insert(); // {$insert3_sql}

$user_group->user_id = 2;
$user_group->group_id = 1;
$user_group->joined = 20050412;
$user_group->active = 0;
$user_group->insert(); // {$insert4_sql}

$user_group->group_id = 3;
$user_group->joined = 20050830;
$user_group->active = 1;
$user_group->insert(); // {$insert5_sql}

$user_group->user_id = 3;
$user_group->group_id = 2;
$user_group->joined = 20040101;
$user_group->active = 1;
$user_group->insert(); // {$insert6_sql}

$user_group->group_id = 3;
$user_group->joined = 20051225;
$user_group->active = 1;
$user_group->insert(); // {$insert7_sql}
</pre>

    <p>Now we can create a new <b>user</b> object and list all groups of a specific user, for example.</p>
    
<pre>
$user = YDDatabaseObject::getInstance( 'user' );
$user->id = 1;
$user->find( 'group' );
$results = $user->getResults();
YDDebugUtil::dump( $results );
</pre>

    <pre>{$find1_results}</pre>
    
    <p>As you can see, we have information from the <b>group</b> and <b>user_group</b> objects. We can access each object from <b>$user</b> like we would do in a normal relation.</p>
    
{literal}<pre>
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

YDDebugUtil::dump( $results );
</pre>{/literal}

    <pre>{$find2_results}</pre>

    <p>It's possible to search relations using the related object's values to filter the query. For example, I want the groups from User C were he is active in the group and the group is active too.</p>
    
<pre>
$user = YDDatabaseObject::getInstance( 'user' );
$user->load( 'group' ); // load the relation objects

$user->id = 3;
$user->group->active = 1;
$user->user_group->active = 1;

$user->find( 'group' );
$results = $user->getResults();
YDDebugUtil::dump( $results );
</pre>
    
    <pre>{$find3_results}</pre>

    <p>&nbsp;</p>
    <p><a href="index.php?do=usersall&YD_DEBUG={$YD_DEBUG}">Next</a></p>
    
{include file="footer.tpl"}
