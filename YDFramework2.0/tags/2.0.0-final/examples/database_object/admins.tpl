{include file="header.tpl"}

    <h3>Protected fields</h3>

    <p>The <b>admin</b> class is not a representation of another table in the database, but an extension of the <b>user</b> class with <b>protected</b> values that cannot be changed. In this case, the <b>is_admin</b> field is <b>protected</b> with value <b>is_admin = 1</b>.</p>
    
<pre>
$admin = YDDatabaseObject::getInstance( 'admin' );
$values = $admin->getValues();
YDDebugUtil::dump( $values );
</pre>

    <pre>{$find1_results}</pre>

    <p>As you can see, <b>is_admin = 1</b>. You may try to change it, but it'll always go back to the original value defined in the class construction.</p>
    
    <p>Let's see who is admin.</p>
    
<pre>
$admin->find();
$results = $admin->getResults();
YDDebugUtil::dump( $results );
</pre>

    <pre>{$find2_results}</pre>

    <p>Let's try to change the <b>is_admin</b> value.</p>
    
<pre>
$admin->set( 'is_admin', 0 );
$values = $admin->getValues();
YDDebugUtil::dump( $values );
</pre>

    <pre>{$change1_results}</pre>
    
    <p>No changes. Let's try to modify it directly.</p>
    
<pre>
$admin->is_admin = 0;
$values = $admin->getValues();
YDDebugUtil::dump( $values );
</pre>

    <pre>{$change2_results}</pre>
    
    <p><b>Protected</b> fields have values that are not changeable so you can have more control over your code.</p>

    <p>&nbsp;</p>
    <p><a href="index.php?do=phones&YD_DEBUG={$YD_DEBUG}">Next</a></p>
    
{include file="footer.tpl"}
