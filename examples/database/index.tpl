<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

</head>

<body>

	<h3>[$title]</h3>

	[if $YD_ACTION == 'default']

		<p><a href="[$YD_SELF_SCRIPT]?do=AddNote">Add a new note</a></p>

		[if $entries]

			[foreach from=$entries item=entry]
				<p>
				<b>[$entry.notetitle]</b>
				[ <a href="[$YD_SELF_SCRIPT]?do=EditNote&id=[$entry.noteid]">edit</a> | 
				<a href="[$YD_SELF_SCRIPT]?do=DeleteNote&id=[$entry.noteid]">delete</a> ]
				<br>
				[$entry.notecontents]
				</p>
			[/foreach]

		[else]
			<p>No notes were found.</p>
		[/if]

	[/if]

	[if $YD_ACTION == 'addnote' || $YD_ACTION == 'editnote']
		[$form]
	[/if]

</body>

</html>
