<html>

<head>

    <title>{$weblog_title}</title>

    <link rel="stylesheet" type="text/css" href="manage.css" />

    <script language="JavaScript">
    <!--

        {literal}function YDConfirmDelete( img ) {{/literal}
            return confirm( '{t w="confirm_delete"}\n\n"' + img + '"?');
        {literal}}{/literal}

        {literal}function addItem( item ) {
            var src = '{/literal}{$uploads_dir}{literal}/' + item;
            window.opener.tinyMCE.themes['advanced']._insertImage(src);
            window.close();
        }{/literal}

        {literal}function YDRowMouseOver( obj ) {{/literal}
            document.getElementById( obj + '_1' ).bgColor = '#EDF3FE';
            document.getElementById( obj + '_2' ).bgColor = '#EDF3FE';
        {literal}}{/literal}

        {literal}function YDRowMouseOut( obj ) {{/literal}
            document.getElementById( obj + '_1' ).bgColor = '#FFFFFF';
            document.getElementById( obj + '_2' ).bgColor = '#FFFFFF';
        {literal}}{/literal}

    //-->
    </script>

</head>

<body marginwidth="10" marginheight="10" bottommargin="10" leftmargin="10" rightmargin="10" topmargin="10"
 bgcolor="#FFFFFF">

    <p class="title">{t w="h_contents"} &raquo; {t w="select_image"}</p>

    {$form.tag}
        {$form.action.html}
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG" colspan="5">{t w="select_image"}</th>
            </tr>
            <tr>
                <td class="adminRowL" colspan="4">{$form.image.html}</td>
                <td class="adminRowR" colspan="1">{$form._cmdSubmit.html}</td>
            </tr>
        {if $images->set}
            <tr><td class="adminRowR" colspan="5">{$images->getBrowseBar()}</td></tr>
            {foreach from=$images->set item="image_row"}
            <tr>
                {foreach from=$image_row item="image"}
                    <td width="20%" class="adminRowC" style="border: 0px solid black;vertical-align: middle;" height="100"
                     {if $image}id="{$image->relative_path}_1" onMouseOver="YDRowMouseOver('{$image->relative_path|addslashes}');" onMouseOut="YDRowMouseOut('{$image->relative_path|addslashes}');"{/if}
                    >
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
                    <td width="20%" class="adminRowC"
                     {if $image}id="{$image->relative_path}_2" onMouseOver="YDRowMouseOver('{$image->relative_path|addslashes}');" onMouseOut="YDRowMouseOut('{$image->relative_path|addslashes}');"{/if}
                    >
                        {if $image}
                            <a class="subline" href="javascript:void( addItem( '{$image->relative_path|addslashes}' ) )">{$image->relative_path}</a>
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                {/foreach}
            </tr>
            {/foreach}
            <tr><td class="adminRowR" colspan="5">{$images->getBrowseBar()}</td></tr>
            <tr>
                <td class="adminRowLNB" colspan="5">
                    <p class="subline">{t w="total"}: {$images->totalRows}</p>
                </td>
            </tr>
        {else}
            <tr>
                <td class="adminRowL" colspan="5">{t w="no_images_found"}</td>
            </tr>
        {/if}

        </table>

    {$form.endtag}

</body>

</html>
