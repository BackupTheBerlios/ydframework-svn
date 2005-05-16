{include file="header.tpl"}

    <h3>Adding some groups</h3>

    <p>We are going to use the <b>group</b> class to add some groups, so first we get an instance of the class.</p>
    
<pre>
$group = YDDatabaseObject::getInstance( 'group' );
</pre>

    <p>Then we define it's values and use the <b>insert</b> method to add a new group to the database.</p>

<pre>
$group->name = 'Group A';
$group->active = 1;
$group->insert(); // {$group1_sql}
</pre>
    
    <p>"{$group1->name}" - id = {$group1->id} and active = {$group1->active}</p>
    
<pre>
$group->reset(); // resets all data
$group->name = 'Group B';
$group->active = 1;
$group->insert(); // {$group2_sql}
</pre>
    
    <p>"{$group2->name}" - id = {$group2->id} and active = {$group2->active}</p>
    
<pre>
$group->reset(); // resets all data
$group->name = 'Group C';
$group->active = 1;
$group->insert(); // {$group3_sql}
</pre>
    
    <p>"{$group3->name}" - id = {$group3->id} and active = {$group3->active}</p>
    
<pre>
$group->reset(); // resets all data
$group->name = 'Group D';
$group->active = 0;
$group->insert(); // {$group4_sql}
</pre>
    
    <p>"{$group4->name}" - id = {$group4->id} and active = {$group4->active}</p>

    <p>We can search all groups defined above using the <b>find</b> method, cycle the results with <b>fetch</b> and retrieve each one values using the <b>getValues</b> method.</p>
    
{literal}<pre>
$group->reset(); // resets all data
$group->find();

while( $group->fetch() ) {
    YDDebugUtil::dump( $group->getValues() );
}
</pre>{/literal}

    <p>The SQL executed:</p>
    
    <pre>{$find1_sql}</pre>
    
    <p>The result:</p>
    
    <pre>{$find1_results}</pre>

    <p>We can retrieve only the inactive groups by setting <b>active = 0</b> and searching the groups:</p>
    
{literal}<pre>
$group->reset(); // resets all data
$group->active = 0;
$group->find();

while( $group->fetch() ) {
    YDDebugUtil::dump( $group->getValues() );
}
</pre>{/literal}

    <p>The SQL executed:</p>
    
    <pre>{$find2_sql}</pre>

    <p>The result:</p>
    
    <pre>{$find2_results}</pre>
    
    <p>Let's update this group to <b>active = 1</b>:</p>
    
<pre>
$group->active = 1;
$group->update(); // {$group4_update_sql}
</pre>
    
    <p>Now, let's delete it:</p>
    
<pre>
$group->delete(); // {$group4_delete_sql}
</pre>

    <p>Let's reset it's values and try to execute a <b>delete</b> to see what happens.</p>
    
<pre>
$group->reset();
$group->delete();
</pre>
    
    {$group1->reset()}{$group1->delete()}

    <p>This is a protection defined with YDConfig called <b>YD_DBOBJECT_DELETE</b>. A similar config is available for UPDATEs that doesn't have conditions called <b>YD_DBOBJECT_UPDATE</b>. The default for both configs is not letting the query be executed.</p>

    <p>Now let's list the remaining groups but now we will use the <b>getResults</b> method to retrieve a single array with all results.</p>
    
<pre>
$group->reset();
$group->find();
$results = $group->getResults();
YDDebugUtil::dump( $results );
</pre>

    <p>The result:</p>
    
    <pre>{$find3_results}</pre>
    
    <p>We can also retrieve an associative array using <b>getResultsAsAssocArray</b>. Let's do that setting the keys with the <b>id</b> value and the values with the <b>name</b> value.</p>
    
<pre>
$group->reset();
$group->find();
$results = $group->getResultsAsAssocArray( 'id', 'name' );
YDDebugUtil::dump( $results );
</pre>

    <p>The result:</p>
    
    <pre>{$find4_results}</pre>
    
    <p>We can pass an array of values to <b>getResultsAsAssocArray</b> to return more data:</p>
    
    <pre>
$group->reset();
$group->find();
$results = $group->getResultsAsAssocArray( 'id', array( 'name', 'active' ) );
YDDebugUtil::dump( $results );
</pre>
    
    <p>The result:</p>
    
    <pre>{$find5_results}</pre>

    <p>&nbsp;</p>
    <p><a href="index.php?do=users&YD_DEBUG={$YD_DEBUG}">Next</a></p>
    
    
{include file="footer.tpl"}