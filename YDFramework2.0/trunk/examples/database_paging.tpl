{capture assign="browsebar"}
	{if $recordset->pages}
		<p>
		{if ! $recordset->isFirstPage}
			<a href="{$recordset->getPreviousUrl()}">previous</a>
		{else}
			previous
		{/if}
		{foreach from=$recordset->pages item="page"}
			{if $page == $recordset->page}
				{$page}
			{else}
				<a href="{$recordset->getPageUrl($page)}">{$page}</a>
			{/if}
		{/foreach}
		{if ! $recordset->isLastPage}
			<a href="{$recordset->getNextUrl()}">next</a>
		{else}
			next
		{/if}
		</p>
	{/if}
{/capture}

<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

	<h3>Database paging example</h3>

	{if $recordset->set}
		<p>
			Showing page {$recordset->page} of {$recordset->totalPages} pages
			({$recordset->totalRowsOnPage} row(s))
		</p>
		{$browsebar}
		<table border="1" width="400">
		{foreach from=$recordset->set item="record"}
			<tr>
				{foreach from=$record item="val"}
					<td width="200">{$val}</td>
				{/foreach}
			</tr>
		{/foreach}
		</table>
		{$browsebar}
	{else}
		<p>No records were found.</p>
	{/if}

</body>

</html>