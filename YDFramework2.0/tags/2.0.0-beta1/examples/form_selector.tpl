<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

	<script language="JavaScript">

		function addItem( item ) {
			window.opener.AddText( '[$smarty.get.field]', '[ldelim][$smarty.get.tag][rdelim]', item, '[ldelim]/[$smarty.get.tag][rdelim]' );
			window.close()
			return false;
		}

	</script>

</head>

<body>

	<h3>Select [$YD_GET.tag] for [$YD_GET.field]</h3>
	
	<p>Tag: [$YD_GET.tag]</p>

	[if sizeof( $items ) > 0]
	
		[foreach from=$items item=item]

			<a href="javascript:void( addItem( '[$item->getBasename()|@addslashes]' ) )">[$item->getBasename()]</a><br>

		[/foreach]

	[else]

		<p>No item(s) available yet.</p>

	[/if]

</body>

</html>
