<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

	{literal}<style>
		.bbtextarea { width: 650px; height: 240px; }
		a { color: darkred; text-decoration: none; cursor: pointer; }
		.bbtoolbar { background-color: #DEDEDE; padding: 2px; }
	</style>{/literal}

</head>

<body>

	<h3>{$title}</h3>

	{if $formValid}

		<p>Welcome to <b>{$form[name][value]}</b>!</p>

		<p>Description1: <blockquote>{$form[desc1][value]}</blockquote></p>
		<p>Description2: <blockquote>{$form[desc2][value]}</blockquote></p>
		<p>Description3: <blockquote>{$form[desc3][value]}</blockquote></p>

	{else}
	
		{if $form[errors]}
			<p style="color: red"><b>Errors during processing:</b>
			{foreach from=$form[errors] item=error}
				<br>{$error}
			{/foreach}
			</p>
		{/if}

		<form {$form[attribs]}>
			<p>
				{$form[name][label]}
				<br>
				{$form[name][html]}
			</p>
			<p>
				{$form[desc1][label]}
				<br>
				{$form[desc1][html]}
			</p>
			<p>
				{$form[desc2][label]}
				<br>
				{$form[desc2][html]}
			</p>
			<p>
				{$form[desc3][label]}
				<br>
				{$form[desc3][html]}
			</p>
			<p>
				{$form[cmdSubmit][html]}
			</p>
		</form>

	{/if}

	<p>
		<a href="{$YD_SELF_SCRIPT}">try again</a>
		|
		<a href="index.php">other samples</a>
	</p>

</body>

</html>
