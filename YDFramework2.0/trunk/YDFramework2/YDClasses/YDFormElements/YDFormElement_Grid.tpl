
<table width="100%" border="0">
  <tr>
    <td align="right">
	<span class="field_previous" onClick="document.getElementById('gr_page').value='{$recordset->pagePrevious}';ticketReload();">&laquo; {t w="previous"}</span>
	&nbsp;
    {foreach from=$recordset->pages item="page"}
		{if ($page != $recordset->page)}
			<span class="field_page" onClick="document.getElementById('gr_page').value='{$page}';ticketReload();">{$page}</span>
		{else}
			<span class="field_page_current">{$page}</span>
		{/if}
	{/foreach}
	&nbsp;
	<span class="field_next" onClick="document.getElementById('gr_page').value='{$recordset->pageNext}';ticketReload();">{t w="next"} &raquo;</span>
	</td>
  </tr>
</table>
<br />
        <table width="100%" border="0">
            <tr class="rowHeader">
                {foreach from=$columns item="field" key="key"}
					{if ($field.sortable == false)}
	                    <td height="24" align="right" class="fieldHeader_off">{t w=$key}&nbsp;</td>
					{elseif ($key != $defaults.grid_column)}
	                    <td height="24" align="right" class="fieldHeader" onClick="document.getElementById('gr_column').value='{$key}'; document.getElementById('gr_direction').value = 1-document.getElementById('gr_direction').value; ticketReload();">{t w=$key}&nbsp;</td>
					{else}
	                    <td height="24" align="right" class="fieldHeader_order" onClick="document.getElementById('gr_column').value='{$key}'; document.getElementById('gr_direction').value = 1-document.getElementById('gr_direction').value; ticketReload();">{t w=$key}&nbsp;</td>
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
        <table width="100%"  border="0">
          <tr>
            <td align="right" class="field_total">
			&nbsp;
			{$form.gr_column.html}
			{$form.gr_direction.html}
			{$form.gr_page.html}
			</td>
          </tr>
          <tr>
            <td align="right" class="field_total">{t w="total"}: {$recordset->totalRows}&nbsp;&nbsp;</td>
          </tr>
        </table>
