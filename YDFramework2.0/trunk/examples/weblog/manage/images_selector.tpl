<html>

<head>

    <title>{$weblog_title}</title>

    <link rel="stylesheet" type="text/css" href="manage.css" />

    <script language="JavaScript">
    <!--

        {literal}function YDConfirmDelete( img ) {{/literal}
            return confirm( '{t w="confirm_delete"}\n\n"' + img + '"?');
        {literal}}{/literal}

        {literal}function addItem( item ) {{/literal}
            window.opener.focus();
            window.opener.AddText( '{$smarty.get.field}', '[img]{$uploads_dir}/' , item, '[/img]' );
            window.close()
            return false;
        {literal}}{/literal}

    //-->
    </script>

</head>

<body marginwidth="10" marginheight="10" bottommargin="10" leftmargin="10" rightmargin="10" topmargin="10"
 bgcolor="#FFFFFF">

    <p class="title">{t w="h_contents"} &raquo; {t w="select_image"}</p>

    {capture assign="browsebar"}
        {if $images->pages}
            <tr>
                <td class="adminRowR" colspan="5" style="border-top: 1px solid #DDDDDD;">
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
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG" colspan="3">{t w="upload_image"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="640">{$form.image.html}</td>
                <td class="adminRowR" width="160">{$form._cmdSubmit.html}</td>
            </tr>
        </table>
    {$form.endtag}

    {if $images->set}
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
        {$browsebar}
        {foreach from=$images->set item="image_row"}
        <tr>
            {foreach from=$image_row item="image"}
                <td width="20%" class="adminRowC" style="border: 0px solid black;vertical-align: middle;" height="100">
                    {if $image}
                        <a href="javascript:void( addItem( '{$image->relative_path|addslashes}' ) )"><img src="{$YD_SELF_SCRIPT}?do=thumbnail&id={$image->relative_path}" border="0"></a>
                    {else}
                        &nbsp;
                    {/if}
                </td>
            {/foreach}
        </tr>
        <tr>
            {foreach from=$image_row item="image"}
                <td width="20%" class="adminRowC">
                    {if $image}
                        <a class="subline" href="javascript:void( addItem( '{$image->relative_path|addslashes}' ) )">{$image->relative_path}</a>
                        {*
                        <br/>
                        <span class="subline">{$image->getLastModified()|date:'%Y/%m/%d %H:%M'}</a>
                        *}
                    {else}
                        &nbsp;
                    {/if}
                </td>
            {/foreach}
        </tr>
        {/foreach}
        {$browsebar}
        <tr>
            <td class="adminRowLNB" colspan="5">
                <p class="subline">{t w="total"}: {$images->totalRows}</p>
            </td>
        </tr>
        </table>
    {else}
        <p>{t w="no_images_found"}</p>
    {/if}


</body>

</html>
