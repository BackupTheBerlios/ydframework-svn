<html>

<head>
	<title>{$title}</title>
</head>

<body>

	{if is_array( $books )}
		<table>
			<tr>
				<th>Author</th>
				<th>Title</th>
			</tr>
			{foreach from=$books item=val}
				<tr>
					<td>{$val.author}</td>
					<td>{$val.title}</td>
				</tr>
			{/foreach}
		</table>
	{else}
		<p>There are no books to display.</p>
	{/if}

</body>

</html>