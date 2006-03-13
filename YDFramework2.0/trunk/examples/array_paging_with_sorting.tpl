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

    <h3>Array paging example</h3>

    {if $recordset->set}
        <p>
            Showing page {$recordset->page} of {$recordset->totalPages} pages
            ({$recordset->totalRowsOnPage} row(s))
        </p>
        {$browsebar}
        <table border="1" width="400">
        <tr>
            <th>
                <a href="{$recordset->getSortUrl('variable_name')}">Variable Name</a> 
                {if $recordset->sortfield eq 'variable_name'}({$recordset->sortdirection}){/if}
            </th>
            <th>
                <a href="{$recordset->getSortUrl('value')}">Value</a>
                {if $recordset->sortfield eq 'value'}({$recordset->sortdirection}){/if}
            </th>
        </tr>
        {foreach from=$recordset->set item="record"}
            <tr>
                <td width="200">{$record.variable_name}</td>
                <td width="200">{$record.value}</td>
            </tr>
        {/foreach}
        </table>
        {$browsebar}
    {else}
        <p>No files were found.</p>
    {/if}

</body>

</html>