<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

</head>

<body>

	[if $YD_ACTION == 'default']

		<h3>Notes</h3>

		<p><a href="[$YD_SELF_SCRIPT]?do=AddNote">Add a new note</a></p>

		[if $entries]

			[foreach from=$entries item=entry]
				<p>
				<b>[$entry.title]</b>
				<a href="[$YD_SELF_SCRIPT]?do=DeleteNote&id=[$entry.id]">delete</a>
				<br>
				[$entry.body]
				</p>
			[/foreach]

		[else]
			<p>No notes were found.</p>
		[/if]

	[/if]

	[if $YD_ACTION == 'addnote']

		<h3>Add a new note</h3>

		[if $form.errors]
			<p style="color: red"><b>Errors during processing:</b>
				[foreach from=$form.errors item=error]
					<br>[$error]
				[/foreach]
			</p>
		[/if]

		<form [$form.attribs]>
			<p>
				[$form.title.label]
				<br>
				[$form.title.html]
			</p>
			<p>
				[$form.body.label]
				<br>
				[$form.body.html]
			</p>
			<p>
				[$form.cmdSubmit.html]
			</p>
		</form>

	[/if]

</body>

</html>
