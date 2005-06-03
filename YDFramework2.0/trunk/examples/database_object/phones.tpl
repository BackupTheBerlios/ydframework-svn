{include file="header.tpl"}

    <h3>Let's start working with relations</h3>

    <p>The <b>user</b> class have a defined relation called <b>phone</b>. This relation links the <b>user</b> class with the <b>phone</b> class by the <b>user_id</b>. Let's add some phone numbers to the table.</p>
    
<pre>
$phone = YDDatabaseObject::getInstance( 'phone' );

$phone->user_id = 1;
$phone->number = '1111-1111';
$phone->ord = 1;
$phone->insert(); // {$insert1_sql}

$phone->number = '1222-2222';
$phone->ord = 2; 
$phone->insert(); // {$insert2_sql}

$phone->number = '1333-3333';
$phone->ord = 3;
$phone->insert(); // {$insert3_sql}

$phone->user_id = 2;
$phone->number = '2444-4444';
$phone->ord = 1;
$phone->insert(); // {$insert4_sql}

$phone->number = '2555-5555';
$phone->ord = 2; 
$phone->insert(); // {$insert5_sql}

$phone->number = '2666-6666';
$phone->ord = 3;
$phone->insert(); // {$insert6_sql}

$phone->user_id = 3;
$phone->number = '3777-7777';
$phone->ord = 1;
$phone->insert(); // {$insert7_sql}

$phone->number = '3888-8888';
$phone->ord = 2; 
$phone->insert(); // {$insert8_sql}

$phone->number = '3999-9999';
$phone->ord = 3;
$phone->insert(); // {$insert9_sql}
</pre>

    <p>Now that all users have phone numbers, let's use the <b>user</b> class to retrieve the <b>phone</b> relation with the user data. We can do this by passing the relations we want to link to the <b>find</b> method.</p>

<pre>
$user = YDDatabaseObject::getInstance( 'user' );
$user->id = 1;
$user->find( 'phone' );
$results = $user->getResults();
YDDebugUtil::dump( $results );
</pre>

    <p>The results:</p>

    <pre>{$find1_results}</pre>
    
    <p>Notice that the array returned prefixes all the information of the related objects with the variable name defined in the relation. But we can still retrieve arrays of the different objects.</p>
    
<pre>
$user->resetAll(); // resets all objects
$user->id = 1;
$user->find( 'phone' );

$user_results = $user->getResults( true );
$phone_results = $user->phone->getResults( true );

YDDebugUtil::dump( $user_results );
YDDebugUtil::dump( $phone_results );
</pre>

    <p>The <b>user</b> results:</p>
    
    <pre>{$find2_user_results}</pre>
    
    <p>The results are returned three times because this was a relation search and User A have three phone numbers. If you know what you are doing, you can return only one array using <b>getValues</b>.</p>
    
    <p>The <b>phone</b> results:</p>
    
    <pre>{$find2_phone_results}</pre>
    
    <p>There are other ways of retrieving these results, like using <b>fetch</b> and <b>getValues</b> or by simply getting the objects values directly. It's up to the developer.</p>
    
    <p>There are also some ways of filtering the search so you can build more complex queries.</p>

<pre>
$user = YDDatabaseObject::getInstance( 'user' );
$user->load( 'phone' ); // now we have a phone object in $user

$user->resetQuery(); // resets default query statements
$user->phone->resetQuery();

$user->select( 'id', 'name', 'country' );
$user->phone->select( 'number', 'ord' );

$user->where( $user->getTable() . ".country LIKE 'b%'" );
$user->order( $user->phone->getTable() . '.ord' );
$user->limit( 2 );

$user->find( 'phone' );
$results = $user->getResults();
YDDebugUtil::dump( $results );
</pre>

    <p>The results:</p>

    <pre>{$find3_results}</pre>
    
    <p>The <b>resetQuery</b> method clears all the default query elements for the following query. By default, all fields of the related objects in the search are added to the select statement, so executing a <b>resetQuery</b> will clear all of them and you can add only the fields you want to retrieve.</p>
    
    <p>The example above only uses <b>select</b>, <b>where</b>, <b>order</b> and <b>limit</b> but there is also <b>group</b>, <b>having</b> and the possibility of adding values to the objects fields so your filters can be more complex.</p>

    <p>&nbsp;</p>
    <p><a href="index.php?do=usersgroups&YD_DEBUG={$YD_DEBUG}">Next</a></p>
    
{include file="footer.tpl"}
