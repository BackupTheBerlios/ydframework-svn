{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_items"} &raquo; {$item.title} &raquo; {t w="gallery"}</p>

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
            <th class="adminRowL" colspan="7">
                {t w="item"}
            </th>
        </tr>
        <tr>
            <td class="adminRowL" colspan="7">
                <a href="items.php?do=edit&id={$item.id}">{$item.title}</a>
            </td>
        </tr>

        <tr>
            <th class="adminRowL" colspan="7">
                &nbsp;<br/>{t w="a_comments"}
            </th>
        </tr>
        <tr>
            <td class="adminRowL" colspan="7">
                <a href="comments.php?id={$item.id}">{if $item.num_comments > 0}{$item|@text_num_comments:false}{else}<span class="disabled">0 {t w="comments" lower=true}</span>{/if}</a>
            </td>
        </tr>

        <tr>
            <th colspan="7" class="adminRowL">
                &nbsp;<br/>{t w="gallery"}
            </th>
        </tr>
        {if $images->set}
            <tr>
                <th class="adminRowL" width="20%">{t w="date"}</th>
                <th class="adminRowL" width="10%">{t w="name"}</th>
                <th class="adminRowL" width="23%">&nbsp;</th>
                <th class="adminRowR" width="11%">{t w="size"}</th>
                <th class="adminRowC" width="12%">{t w="type"}</th>
                <th class="adminRowL" width="14%">{t w="dimensions"}</th>
                <th class="adminRowR" width="10%">{t w="actions"}</th>
            </tr>
            {$browsebar}
            {foreach from=$images->set item="image"}
                <tr>
                    <td class="adminRowL" valign="top">{$image->getLastModified()|date:'%Y/%m/%d %H:%M'}</td>
                    <td class="adminRowL" valign="top">
                        <img src="../{$uploads_dir}/{$image->relative_path_s}" alt="{$image->getBaseName()}">
                    </td>
                    <td class="adminRowL" valign="top">
                        <a href="{$YD_SELF_SCRIPT}?do=showimage&id={$item.id}&img={$image->relative_path}" target="_blank">{$image->getBaseName()}</a>
                    </td>
                    <td class="adminRowR" valign="top">{$image->getSize()|fmtfilesize}</td>
                    <td class="adminRowC" valign="top">{$image->getImageType()|upper}</td>
                    <td class="adminRowL" valign="top">{$image->getWidth()} x {$image->getHeight()}</td>
                    <td class="adminRowR" valign="top">
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$item.id}&img={$image->relative_path}"
                         onClick="return YDConfirmDelete( '{$image->getBaseName()|addslashes}' );">{t w="delete"}</a>
                    </td>
                </tr>
            {/foreach}
            {$browsebar}
            <tr>
                <td class="adminRowL" colspan="5">{$form.image.html}</td>
                <td class="adminRowR" colspan="2">{$form._cmdSubmit.html}</td>
            </tr>
            <tr>
                <td class="adminRowLNB" colspan="5">
                    <p class="subline">{t w="total"}: {$images->totalRows}</p>
                </td>
            </tr>
        {else}
            <tr>
                <td class="adminRowL" colspan="7">{t w="no_images_found"}</td>
            </tr>
        {/if}
        </table>

    {$form.endtag}

{/if}

{include file="__mng_footer.tpl"}
