{include file="__mng_header.tpl"}

<p class="title">{t w="h_maintenance"} &raquo; {t w="a_cleanup_cache"}</p>

{if $message}
    <p class="error">{$message}</p>
{/if}

{$form.tag}
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" colspan="3">{t w="a_cleanup_cache"}</th>
        </tr>
        <tr>
            <td class="adminRowL" width="30">{$form.cache_tmb.html}</td>
            <td class="adminRowL" width="670">{$form.cache_tmb.label_html}</td>
        </tr>
        <tr>
            <td class="adminRowL">{$form.cache_web.html}</td>
            <td class="adminRowL">{$form.cache_web.label_html}</td>
        </tr>
        <tr>
            <td class="adminRowL">{$form.cache_tpl.html}</td>
            <td class="adminRowL">{$form.cache_tpl.label_html}</td>
        </tr>
    </table>
    <p>{$form._cmdSubmit.html}</p>
{$form.endtag}


{include file="__mng_footer.tpl"}
