{include file="header.tpl"}

    <h3>Adding some users</h3>

    <p>We are going to use the <b>user</b> class to add some users, so first we get an instance of the class.</p>
    
<pre>
$user = YDDatabaseObject::getInstance( 'user' );
</pre>

    <p>Now we can add some users:</p>
    
<pre>
$user->name = 'User A';
$user->email = 'usera@host.com';
$user->country = 'Brazil';
$user->is_admin = 1;
$user->birthday = 19700516;
$user->insert(); // {$insert1_sql}

$user->reset();
$user->name = 'User B';
$user->email = 'userb@host.com';
$user->country = 'Brazil';
$user->is_admin = 0;
$user->birthday = 19500516;
$user->insert(); // {$insert2_sql}

$user->reset();
$user->name = 'User C';
$user->email = 'userc@host.com';
$user->country = 'Colombia';
$user->birthday = 19811120;
$user->insert(); // {$insert3_sql}
</pre>

    <p>As you can see we defined all values for users A and B but didn't defined the <b>is_admin</b> value for user C, so he's not added to the INSERT.</p>
    
    <p>Notice that the <b>is_admin</b> field is not the real column name in the users table, it's an alias. But the real column name is used in the query.</p>
    
    <p>Now let's search for <b>User A</b>.</p>
    
<pre>
$user->reset();
$user->name = 'User A';
$user->find(); 
$results = $user->getResults();
YDDebugUtil::dump( $results ); 
</pre>

    <p>The result:</p>
    
    <pre>{$find1_results}</pre>

    <p>We have more values retrieved. The <b>birth_year</b> is a <b>select</b> defined by <b>registerSelect</b>. They are only added to SELECT queries and can retrieve any kind of information because they are defined with expressions. We can get the <b>birth_year</b> expression and any other information from fields or selects using the <b>getField</b> and <b>getSelect</b> methods:</p>
    
<pre>
$birth_year = & $user->getSelect( 'birth_year' );
YDDebugUtil::dump( $birth_year->getName() . ' = ' . $birth_year->getExpression() ); 
</pre>

    <pre>{$birth_year_sql}</pre>
    
    <p>There is also an <b>age</b> value returned. It's returned by the <b>birthday</b> callback. Let's get the callback name:</p>
    
    <pre>
$birthday = & $user->getField( 'birthday' );
YDDebugUtil::dump( 'callback = ' . $birthday->getCallback() ); 
</pre>
    
    <pre>{$birthday_callback}</pre>
    
    <p>The callback adds <b>age</b> to the object when the <b>birthday</b> value is defined by the <b>set</b> method. This happens when we add values to the object with <b>setValues</b>. The <b>setValues</b> method is also executed when fetching results from a query, so when we retrieved the results of the search, the <b>age</b> value was defined.</p>
    
    <p>Let's add another <b>birthday</b> to the user and see what happens.</b>

<pre>
$user->set( 'birthday', 19600516 ); // 10 year older
$age = $user->get( 'age' );
YDDebugUtil::dump( 'age = ' . $age ); 
</pre>

    <pre>{$age_value}</pre>

    <p>As you can see there is also a <b>get</b> method that returns a value in the object.</p>
    
    <p>Let's update the new <b>birthday</b> for this user:</p>
    
<pre>
$user->update(); // {$update1_sql}
</pre>

    <p>The <b>age</b> and <b>birth_year</b> values are not added to the UPDATE because they are not registered fields.</b>

    <p>&nbsp;</p>
    <p><a href="index.php?do=admins&YD_DEBUG={$YD_DEBUG}">Next</a></p>
    
{include file="footer.tpl"}
