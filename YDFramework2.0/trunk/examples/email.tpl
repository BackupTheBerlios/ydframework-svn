<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

	<h3>YDEmail test page</h3>

	{if $formValid}

		{if $result}
			<p>The email to <b>{$form.email.value}</b> was sent successfully!</p>
		{else}
			<p style="color: red">The email to <b>{$form.email.value}</b> was NOT sent successfully!</p>
		{/if}

	{else}

		{$form_html}

	{/if}

	<p>
		<a href="{$YD_SELF_SCRIPT}">try again</a>
		|
		<a href="index.php">other samples</a>
	</p>

</body>

</html>
