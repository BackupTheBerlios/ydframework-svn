<table width="100%"  border="0">
  <tr>
    <td align="right">
	<span class="field_previous" onClick="ticketReload('{$recordset->getCurrentField()}', '{$recordset->getSortdirection()}', '{$recordset->pagePrevious}');">{t w="previous"}</span>
    {foreach from=$recordset->pages item="page"}
		{if ($page != $recordset->page)}
			<span class="field_page" onClick="ticketReload('{$recordset->getCurrentField()}', '{$recordset->getSortdirection()}', '{$page}');">{$page}</span>
		{else}
			<span class="field_page_current">{$page}</span>
		{/if}
	{/foreach}
	<span class="field_next" onClick="ticketReload('{$recordset->getCurrentField()}', '{$recordset->getSortdirection()}', '{$recordset->pageNext}');">{t w="next"}</span>
	</td>
  </tr>
</table>
<br />
        <table width="100%" border="0">

            <tr class="rowHeader">
                {foreach from=$recordset->getFields() item="field"}
					{if ($field != $recordset->getCurrentField())}
	                    <td height="24" align="right" class="fieldHeader" onClick="ticketReload('{$field}', '{$recordset->getSortdirection(true)}', '{$recordset->page}');">{t w=$field}&nbsp;</td>
					{else}
	                    <td height="24" align="right" class="fieldHeader_order" onClick="ticketReload('{$field}', '{$recordset->getSortdirection(true)}', '{$recordset->page}');">{t w=$field}&nbsp;</td>
					{/if}
                {/foreach}
            </tr>


        {foreach from=$recordset->set item="record"}
            <tr class="{cycle values='row1,row2'}">
                {foreach from=$record item="val"}
                    <td height="24" align="right">{$val}&nbsp;</td>
                {/foreach}
            </tr>
        {/foreach}

</table>
