<html>

<head>

	<title>Authentication sample</title>

</head>

<body>

	<h3>Welcome to the login screen.</h3>
	
	<p>You can login as the user <b>pieter</b> with the password <b>kermit</b>.</p>

	{if $form.errors}
		<p style="color: red">
			{foreach from=$form.errors item=error}
				{$error}<br>
			{/foreach}
		</p>
	{/if}

	<form {$form.attribs}>
		<p>
			{$form.loginName.label}
			<br>
			{$form.loginName.html}
		</p>
		<p>
			{$form.loginPass.label}
			<br>
			{$form.loginPass.html}
		</p>
		<p>
			{$form.cmdSubmit.html}
		</p>
	</form>

</body>

</html>
