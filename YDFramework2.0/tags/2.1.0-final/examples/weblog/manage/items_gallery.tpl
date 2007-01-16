{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_items"} &raquo; {$item.title} &raquo; {t w="gallery"}</p>

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
        <th class="adminRowLG" colspan="7">
            &raquo; <a href="items.php?do=edit&id={$item.id}" style="font-weight: bold">{t w="change_item_desc"}</a>
            ({$item.title})
            &nbsp;
            <a href="../item.php?id={$item.id}" target="_blank"><img src="images/more_details.gif" border="0" /></a>
        </th>
    </tr>

    <tr><td colspan="7">&nbsp;</td></tr>
    <tr>
        <th class="adminRowLG" colspan="7">
            &raquo; <a href="comments.php?id={$item.id}" style="font-weight: bold">{t w="a_comments"}</a>
            ({$item|@text_num_comments:true})
        </th>
    </tr>

    <tr><td colspan="7">&nbsp;</td></tr>
    <tr>
        <th class="adminRowLG" colspan="7">
            &raquo; {t w="gallery"}
        </th>
    </tr>
    </table>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
    {$form.tag}
        <tr>
            <td class="adminRowL" colspan="5">{$form.image.html}</td>
            <td class="adminRowR" colspan="2">{$form._cmdSubmit.html}</td>
        </tr>
    {$form.endtag}
    </table>

    <form name="metaDataForm" id="metaDataForm" action="{$YD_SELF_SCRIPT}?do=editMetaData&id={$item.id}" method="POST">
        <table width="700" cellspacing="0" cellpadding="0" border="0">
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
            <tr><td class="adminRowR" colspan="7">{$images->getBrowseBar()}</td></tr>
            {foreach from=$images->set item="image"}
                <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                    <td class="adminRowL" valign="top">{$image->getLastModified()|date:'%Y/%m/%d %H:%M'}</td>
                    <td class="adminRowL" valign="top">
                        <img src="{$uploads_dir}/{$image->relative_path_s}" alt="{$image->getBaseName()}">
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
                        <br/>
                        <a href="#" onClick="YDShowHideElement( 'meta_{$image->getBaseName()|@addslashes}' );return false;">{t w="edit_meta"}</a>
                    </td>
                </tr>
                <tr id="meta_{$image->getBaseName()}" style="display: none;">
                    <td class="adminRowL" colspan="5">
                        <input class="tfM" type="text" name="metadata[{$image->relative_path}][title]" value="{$image->title}"/>
                        <br/>
                        <textarea class="tfMNoMCE" name="metadata[{$image->relative_path}][description]">{$image->description}</textarea>
                    </td>
                    <td class="adminRowR" colspan="2">
                        <input type="submit" class="button" value="{t w="save"}" />
                    </td>
                </tr>
            {/foreach}
            <tr><td class="adminRowR" colspan="7">{$images->getBrowseBar()}</td></tr>
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
    </form>

{/if}

{include file="__mng_footer.tpl"}
