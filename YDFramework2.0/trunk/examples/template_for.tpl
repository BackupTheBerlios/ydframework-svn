<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

	{for start=0 stop=10 step=2 value=current}
		We are on number {$current}<br/>
	{/for}

	<hr/>

	{for start=10 stop=0 step=2 value=current}
		We are on number {$current}<br/>
	{/for}

</body>

</html>
