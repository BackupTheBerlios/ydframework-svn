<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

	<style>
		body { font-family: "Lucida Sans Unicode", "Lucida Sans", "Lucida Grande", Arial, Helvetica, sans-serif; margin: 20px; background-color: #FFFFFF; }
		a { color: darkred; }
		.text { font-size: 80%; padding-left: 8px; }
		.textSmall { font-size: 70%; padding-left: 8px; }
		.cell { font-size: 80%; padding: 4px; padding-left: 8px; padding-right: 8px; }
		.titleBig { color: navy; font-weight: normal; font-size: 120%; padding-left: 8px; }
		.titleSmall { color: navy; font-weight: normal; font-size: 100%; padding: 2px; padding-top: 15px; padding-left: 8px; }
		.rowDark { background-color: #EEEEBB; }
		.rowLight { background-color: #FFFFCC;}
	</style>

</head>

<body>

	<p class="titleBig">XML/RPC Interface</p>

	<p class="text">URL: <a href="[$xmlRpcUrl]">[$xmlRpcUrl]</a></p>

	<p class="titleSmall">XML-RPC interface methods</p>

	<table border="0" width="100%" cellpadding="0"  cellspacing="0">
		[foreach from=$methods item=methodInfo key=method]
			<tr class="[cycle values=rowDark,rowLight advance=no]">
				<td class="cell"><b>
					[if $methodInfo.paramsIn]
						[$method](
						[$methodInfo.paramsIn|@implode:' '|lower]
						)
					[else]
						[$method]()
					[/if]
				</b></td>
				<td align="right" class="cell">
					returns:
					[if $methodInfo.paramsOut]
						[$methodInfo.paramsOut|lower]
					[else]
						none
					[/if]
				</td>
			</tr>
			<tr class="[cycle values=rowDark,rowLight]">
				<td colspan="2" class="cell">
					[if $methodInfo.help]
						[$methodInfo.help]
					[else]
						No help available.
					[/if]
				</td>
			</tr>
		[/foreach]
	</table>

	<p class="titleSmall">XML-RPC interface capabilities</p>

	<table border="0" width="100%" cellpadding="3"  cellspacing="0">
		[foreach from=$capabilities item=info key=key]
			<tr class="[cycle values=rowDark,rowLight advance=no]">
				<td class="cell">[$key]</td>
				<td class="cell"><a href="[$info.specUrl]" target="_blank">[$info.specUrl]</a></td>
				<td class="cell" align="right">version [$info.specVersion]</td>
			</tr>
		[/foreach]
	</table>

	<p class="textSmall">
		This page is automatically generated by <a target="_blank"
		href="[$YD_FW_HOMEPAGE]">[$YD_FW_NAMEVERS]</a>
	</p>

</body>

</html>
