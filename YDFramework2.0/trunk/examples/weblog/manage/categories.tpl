{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_categories"}</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $categories->pages}
            <tr>
                <td class="adminRowL" colspan="1">
                    <p class="subline">{t w="total"}: {$categories->totalRows}</p>
                </td>
                <td class="adminRowR" colspan="2">
                    <p class="subline">
                    &laquo;
                    {if ! $categories->isFirstPage}
                        <a href="{$categories->getPreviousUrl()}" class="subline">{t w="previous"}</a>
                    {else}
                        {t w="previous"}
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
                        <a href="{$categories->getNextUrl()}" class="subline">{t w="next"}</a>
                    {else}
                        {t w="next"}
                    {/if}
                    &raquo;
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">{t w="add_category"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="540">{$form.title.html}</td>
                <td class="adminRowR" width="160">{$form._cmdSubmit.html}</td>
            </tr>
        </table>
    {$form.endtag}

    {if $categories->set}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="45%">{t w="name"}</th>
            <th class="adminRowR" width="25%">{t w="number_of_items"}</th>
            <th class="adminRowR" width="30%">{t w="actions"}</th>
        </tr>
        {$browsebar}
        {foreach from=$categories->set item="category"}
            <tr>
                <td class="adminRowL">{$category.title}</td>
                <td class="adminRowR">{$category.num_items}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$category.id}">{t w="edit"}</a> |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$category.id}"
                     onClick="return YDConfirmDelete( '{$category.title}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        {$browsebar}
        </table>
    {else}
        <p>{t w="no_categories_found"}</p>
    {/if}

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">
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
