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
                    {$record.name}&nbsp;
                </td>
                <td width="200">
                    {$record.group_name}&nbsp;
                </td>
            </tr>
        {/foreach}
        </table>
        {$browsebar}
    {else}
        <p>No results were found.</p>
    {/if}