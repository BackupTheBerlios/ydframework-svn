{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_categories"}</p>

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

    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG" colspan="3">&raquo; {t w="a_categories"}</th>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2">{$form.title.html}</td>
                <td class="adminRowR">{$form._cmdSubmit.html}</td>
            </tr>

        {if $categories->set}
            <tr>
                <th class="adminRowL" width="45%">{t w="name"}</th>
                <th class="adminRowR" width="20%">{t w="number_of_items"}</th>
                <th class="adminRowR" width="35%">{t w="actions"}</th>
            </tr>
            <tr><td class="adminRowR" colspan="3">{$categories->getBrowseBar()}</td></tr>
            {foreach from=$categories->set item="category"}
                <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                    <td class="adminRowL">{$category.title}</td>
                    <td class="adminRowR">{$category.num_items}</td>
                    <td class="adminRowR">
                        <a href="../category.php?id={$category.id}" target="_blank">{t w="view"}</a>  |
                        <a href="{$YD_SELF_SCRIPT}?do=edit&id={$category.id}">{t w="edit"}</a> |
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$category.id}"
                         onClick="return YDConfirmDelete( '{$category.title|addslashes}' );">{t w="delete"}</a>
                    </td>
                </tr>
            {/foreach}
            <tr><td class="adminRowR" colspan="3">{$categories->getBrowseBar()}</td></tr>
            <tr>
                <td class="adminRowLNB" colspan="3">
                    <p class="subline">{t w="total"}: {$categories->totalRows}</p>
                </td>
            </tr>
        {else}
            <tr>
                <td class="adminRowL" colspan="4">{t w="no_categories_found"}</td>
            </tr>
        {/if}

        </table>
    {$form.endtag}

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG">&raquo; 
                    {if $form.title.value == ''}
                        {t w="add_category"}
                    {else}
                        {t w="change_category_desc"} {$category.title}
                    {/if}
                </th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.title.label_html}</td>
                <td class="adminRowC" width="400">{$form.title.html}</td>
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
