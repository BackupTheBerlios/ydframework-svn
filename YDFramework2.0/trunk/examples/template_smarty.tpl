<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

	{$YD_FW_NAMEVERS}
	
	<p><b>Using an object</b></p>
	<ul>
		<li>{$browser->agent}</li>
		<li>{$browser->browser}</li>
		<li>{$browser->version}</li>
		<li>{$browser->platform}</li>
		<li>{$browser->dotnet|@dump}</li>
		<li>{$browser->getBrowserLanguages()|@dump}</li>
		<li>{$browser->getLanguage()}</li>
	</ul>
		
	<p><b>Using an array</b></p>
	<ul>
		<li>{$array.agent}</li>
		<li>{$array.browser}</li>
		<li>{$array.version}</li>
		<li>{$array.platform}</li>
		<li>{$array.dotnet|@dump}</li>
	</ul>

</body>

</html>
