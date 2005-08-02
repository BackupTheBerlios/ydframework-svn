{include file="__mng_header.tpl"}

<p class="title">{t w="h_maintenance"} &raquo; {t w="a_db_backup"}</p>

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

{$form.tag}
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowLG" colspan="3">&raquo; {t w="a_db_backup"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{$form.bck_name.label_html}</td>
            <td class="adminRowL" width="400">
                {$form.bck_name.html}
                <br/>
                <i>{t w="bck_name_comment"}</i>
            </td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{$form.bck_gzip.label_html}</td>
            <td class="adminRowC" width="400">{$form.bck_gzip.html}</td>
        </tr>
    </table>
    <p>{$form._cmdSubmit.html}</p>
    <p></p>
{$form.endtag}

{include file="__mng_footer.tpl"}
