<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

</head>

<body>

	<h3>Upload test page</h3>

	[if $formValid]

		<p>The file <b>[$form.file1.value.name]</b>
		([$form.file1.value.size|fmtfilesize])
		was uploaded successfully!</p>		

	[else]

		[$form_html]

	[/if]

	<p>
		<a href="[$YD_SELF_SCRIPT]">try again</a>
		|
		<a href="index.php">other samples</a>
	</p>

</body>

</html>
