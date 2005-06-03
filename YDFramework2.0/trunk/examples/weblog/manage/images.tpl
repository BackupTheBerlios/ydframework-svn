{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_images"}</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $images->pages}
            <tr>
                <td class="adminRowL" colspan="1">
                    <p class="subline">{t w="total"}: {$images->totalRows}</p>
                </td>
                <td class="adminRowR" colspan="5">
                    <p class="subline">
                    {if ! $images->isFirstPage}
                        <a href="{$images->getPreviousUrl()}" class="subline">&laquo;</a>
                    {else}
                        &laquo;
                    {/if}
                    |
                    {foreach from=$images->pages item="page"}
                        {if $page == $images->page}
                            <b>{$page}</b>
                        {else}
                            <a href="{$images->getPageUrl($page)}" class="subline">{$page}</a>
                        {/if}
                    {/foreach}
                    |
                    {if ! $images->isLastPage}
                        <a href="{$images->getNextUrl()}" class="subline">&raquo;</a>
                    {else}
                        &raquo;
                    {/if}
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    {$form.tag}
        {$form.action.html}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">{t w="upload_image"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="540">{$form.image.html}</td>
                <td class="adminRowR" width="160">{$form._cmdSubmit.html}</td>
            </tr>
        </table>
    {$form.endtag}

    {if $images->set}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="20%">{t w="date"}</th>
            <th class="adminRowL" width="29%">{t w="name"}</th>
            <th class="adminRowR" width="13%">{t w="size"}</th>
            <th class="adminRowC" width="14%">{t w="type"}</th>
            <th class="adminRowL" width="14%">{t w="dimensions"}</th>
            <th class="adminRowR" width="10%">{t w="actions"}</th>
        </tr>
        {$browsebar}
        {foreach from=$images->set item="image"}
            <tr>
                <td class="adminRowL">{$image->getLastModified()|date:'%Y/%m/%d %H:%M'}</td>
                <td class="adminRowL">
                    <a href="{$YD_SELF_SCRIPT}?do=showimage&id={$image->relative_path}" target="_blank">{$image->relative_path}</a>
                </td>
                <td class="adminRowR">{$image->getSize()|fmtfilesize}</td>
                <td class="adminRowC">{$image->getImageType()|upper}</td>
                <td class="adminRowL">{$image->getWidth()} x {$image->getHeight()}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$image->relative_path}"
                     onClick="return YDConfirmDelete( '{$image->relative_path}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        {$browsebar}
        </table>
    {else}
        <p>{t w="no_images_found"}</p>
    {/if}

{/if}

{include file="__mng_footer.tpl"}