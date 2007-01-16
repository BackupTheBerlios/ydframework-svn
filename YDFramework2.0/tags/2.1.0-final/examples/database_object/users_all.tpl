{include file="header.tpl"}

    <h3>Joining more relations in one query</h3>

    <p>There is a way of making a single query for all relations. You can use the <b>findAll</b> method to join all relations defined, or use the <b>find</b> method with relations names as arguments.</p>
    
    <p>Let's do that with User A.</p>
    
<pre>
$user = YDDatabaseObject::getInstance( 'user' );
$user->id = 1;
$user->findAll();

$results = $user->getResults();
YDDebugUtil::dump( $results );
</pre>

    <pre>{$find1_results}</pre>
    
    <p>The second way:</p>

<pre>
$user = YDDatabaseObject::getInstance( 'user' );
$user->id = 1;
$user->find( 'phone', 'group' );

$results = $user->getResults();
YDDebugUtil::dump( $results );
</pre>

    <pre>{$find2_results}</pre>
    
    <p>That's it, enjoy!</p>
    <p>&nbsp;</p>
    <p><a href="index.php?YD_DEBUG={$YD_DEBUG}">Back to start</a></p>
    
{include file="footer.tpl"}