<?php
/**
* 
* Template for testing token assignment.
* 
* @version $Id: test.tpl.php,v 1.2 2003/09/24 16:04:55 pmjones Exp $
*
*/
?>
<p><?php echo $variable1 ?></p>
<p><?php echo $variable2 ?></p>
<p><?php echo $variable3 ?></p>
<p><?php echo $key0 ?></p>
<p><?php echo $key1 ?></p>
<p><?php echo $key2 ?></p>
<p><?php echo $reference1 ?></p>
<p><?php echo $reference2 ?></p>
<p><?php echo $reference3 ?></p>
<ul>
<?php foreach ($set as $key => $val) echo "<li>$key = $val</li>\n" ?>
</ul>
