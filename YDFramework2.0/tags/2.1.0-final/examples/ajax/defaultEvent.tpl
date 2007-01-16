<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
<head><title></title></head>
<body>
<p><br />
  This example demonstrates how to create and handle default events (read: events that are not registered by your YDAjax code)</p>
<p>{$form}</p>
<p>Custom event: 
  <label>
  <input type="submit" name="Submit" value="This event is a registered JS event: customEvent('Hello YDF!')" onclick="customEvent('Hello YDF!')" />
  </label>
</p>
<p>&nbsp;</p>
<p>Hardcoded JS function '__someOtherEvent' to simulate some hardcoded ajax call 'someOtherAjaxCall' :</p>
{literal}
<script>
	function __someOtherEvent(){return xajax.call("someOtherAjaxCall", arguments, 1);}  
</script>
<pre id="line1">
function __someOtherEvent(){return xajax.call(&quot;someOtherAjaxCall&quot;, arguments, 1);}  </pre>
{/literal}

<p>Event not registered:
  <label>
  <input type="submit" name="Submit2" value="This event will be catched as a default event: __someOtherEvent( 'firstArg', 'secondArg', 'allArgsYouwant' )" onclick="__someOtherEvent( 'firstArg', 'secondArg', 'allArgsYouwant' )" />
  </label>
</p>

<p>&nbsp;</p>
<p>Another JS function '__hackEvent' to simulate an hardcoded ajax call 'myCall' :</p>
{literal}
<script>
	function __hackEvent(){return xajax.call("myCall", arguments, 1);}  
</script>
<pre id="line1">
function __hackEvent(){return xajax.call(&quot;myCall&quot;, arguments, 1);}  </pre>
{/literal}

<p>Event not registered:
  <label>
  <input type="submit" name="Submit2" value="This event will be catched as a default event: __hackEvent( 'justOneArgument' )" onclick="__hackEvent( 'justOneArgument' )" />
  </label>
</p>

</body>
</html>
