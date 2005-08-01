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

    {capture assign="browsebar"}
        {if $categories->pages}
            <tr>
                <td class="adminRowR" colspan="3">
                    <p class="subline">
                    {if ! $categories->isFirstPage}
                        <a href="{$categories->getPreviousUrl()}" class="subline">&laquo;</a>
                    {else}
                        &laquo;
                    {/if}
                    |
                    {foreach from=$categories->pages item="page"}
                        {if $page == $categories->page}
                            <b>{$page}</b>
                        {else}
                            <a href="{$categories->getPageUrl($page)}" class="subline">{$page}</a>
                        {/if}
                    {/foreach}
                    |
                    {if ! $categories->isLastPage}
                        <a href="{$categories->getNextUrl()}" class="subline">&raquo;</a>
                    {else}
                        &raquo;
                    {/if}
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG" colspan="3">{t w="a_categories"}</th>
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
            {$browsebar}
            {foreach from=$categories->set item="category"}
                <tr>
                    <td class="adminRowL">{$category.title}</td>
                    <td class="adminRowR">{$category.num_items}</td>
                    <td class="adminRowR">
                        <a href="../category.php?&id={$category.id}" target="_blank">{t w="view"}</a>  |
                        <a href="{$YD_SELF_SCRIPT}?do=edit&id={$category.id}">{t w="edit"}</a> |
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$category.id}"
                         onClick="return YDConfirmDelete( '{$category.title|addslashes}' );">{t w="delete"}</a>
                    </td>
                </tr>
            {/foreach}
            {$browsebar}
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
                <th class="adminRowLG" colspan="3">
                    {if $form.title.value == ''}
                        {t w="add_category"}
                    {else}
                        {t w="change_category_desc"} {$category.title}
                    {/if}
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.title.label_html}</td>
                <td class="adminRowC" width="400">{$form.title.html}</td>
            </tr>
        </table>
        {$form.id.html}
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
