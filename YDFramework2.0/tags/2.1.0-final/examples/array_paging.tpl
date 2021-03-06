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
        {foreach from=$recordset->set item="record"}
            <tr>
                <td width="200">
                    <a href="{$record->getBasename()}">{$record->getBasename()}</a>
                </td>
            </tr>
        {/foreach}
        </table>
        {$browsebar}
    {else}
        <p>No files were found.</p>
    {/if}

</body>

</html>