<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

	<script language="JavaScript">

		function addItem( item ) {ldelim}
			window.opener.AddText( '{$_TPL[GET][field]}', '[{$_TPL[GET][tag]}]', item, '[/{$_TPL[GET][tag]}]' );
			window.close()
			return false;
		{rdelim}

	</script>

</head>

<body>

	<h3>Select {$YD_GET.tag} for {$YD_GET.field}</h3>
	
	<p>Tag: {$YD_GET.tag}</p>

	{if sizeof( $items ) > 0}
		{foreach from=$items item=item}
			<a href="javascript:void( addItem( '{$item|addslashes}' ) )">{$item}</a><br>
		{/foreach}
	{else}
		<p>No item(s) available yet.</p>
	{/if}

</body>

</html>
