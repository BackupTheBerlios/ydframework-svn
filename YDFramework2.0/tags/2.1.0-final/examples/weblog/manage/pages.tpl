{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_pages"}</p>

{if $form.errors}
    <p><table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowELG">{t w="err_general"}</th>
        </tr>
        <tr>
            <td class="adminRowEL">
                {foreach from=$form.errors item="error"}
                    {$error}<br/>
                {/foreach}
            </td>
        </tr>
    </table></p>
{/if}

{if $YD_ACTION == 'default'}

    <table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th colspan="3" class="adminRowLG">&raquo; {t w="a_pages"}</th>
        <th class="adminRowLGR">
            <a href="{$YD_SELF_SCRIPT}?do=edit"><img src="images/icon_add.gif" border="0" /></a>
            <a href="{$YD_SELF_SCRIPT}?do=edit"><b>{t w="add_page"}</b></a>
        </th>
    </tr>
    {if $pages}
        <tr>
            <th class="adminRowL" width="17%">{t w="date"}</th>
            <th class="adminRowL" width="15%">{t w="author"}</th>
            <th class="adminRowL" width="30%">{t w="title"}</th>
            <th class="adminRowR" width="18%">{t w="actions"}</th>
        </tr>
        <tr><td class="adminRowR" colspan="4">{$pages->getBrowseBar()}</td></tr>
        {foreach from=$pages->set item="page"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);" {if $page.is_draft eq '1'}style="color: gray"{/if}>
                <td class="adminRowL">
                    {if $page.is_draft eq '1'}<i>{/if}
                    {$page.created|date:'%Y/%m/%d %H:%M'}
                    {if $page.is_draft eq '1'}</i>{/if}
                </td>
                <td class="adminRowL">
                    {if $page.is_draft eq '1'}<i>{/if}
                    {$page.user_name}
                    {if $page.is_draft eq '1'}</i>{/if}
                </td>
                <td class="adminRowL">
                    {if $page.is_draft eq '1'}<i>{/if}
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$page.id}">{$page.title}</a>
                    {if $page.is_draft eq '1'}</i>{/if}
                </td>
                <td class="adminRowR">
                    <a href="../page.php?id={$page.id}" target="_blank">{t w="view"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$page.id}">{t w="edit"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$page.id}"
                     onClick="return YDConfirmDelete( '{$page.title|addslashes}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        <tr><td class="adminRowR" colspan="4">{$pages->getBrowseBar()}</td></tr>
        <tr>
            <td class="adminRowLNB" colspan="4">
                <p class="subline">{t w="total"}: {$pages->totalRows}</p>
            </td>
        </tr>
    {else}
        <tr>
            <td class="adminRowL" colspan="4">{t w="no_pages_found"}</td>
        </tr>
    {/if}
    </table>

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG">&raquo; 
                    {if $form.title.value == ''}
                        {t w="add_page"}
                    {else}
                        {t w="change_page_desc"} ({$form.title.value})
                        &nbsp;
                        <a href="../page.php?id={$page.id}" target="_blank"><img src="images/more_details.gif" border="0" /></a>
                    {/if}
                </th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.title.label_html}</td>
                <td class="adminRowC" width="400">{$form.title.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2">
                    {$form.body.label_html}
                    <br/>
                    {$form.body.html}
                </td>
            </tr>
            <tr> 
                <td class="adminRowL">{$form.is_draft.label_html}</td> 
                <td class="adminRowL">{$form.is_draft.html}</td> 
            </tr>
            <tr>
                <td class="adminRowL">{$form.created.label_html}</td>
                <td class="adminRowL">{$form.created.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.modified.label_html}</td>
                <td class="adminRowL">{$form.modified.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2" style="border: 0px;">
                    {$form._cmdSubmit.html}
                    {$form._cmdDelete.html}
                </td>
            </tr>
        </table>
        {$form.id.html}
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
