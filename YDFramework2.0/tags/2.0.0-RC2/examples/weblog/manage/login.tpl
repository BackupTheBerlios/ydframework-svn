{include file="__mng_header.tpl"}

<p class="title">{t w="login"}</p>

{if $form.errors}
    <p class="error">
        <b>{t w="err_login"}</b><br/>
        {foreach from=$form.errors item="error"}
            {$error}<br/>
        {/foreach}
    </p>
{/if}

{$form.tag}
    <table width="480" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" colspan="2">{t w="msg_login"}</th>
        </tr>
        <tr>
            <td class="adminRowL" width="180">{$form.loginName.label_html}</td>
            <td class="adminRowL" width="300">{$form.loginName.html}</td>
        </tr>
        <tr>
            <td class="adminRowL">{$form.loginPass.label_html}</td>
            <td class="adminRowL">{$form.loginPass.html}</td>
        </tr>
    </table>
    <p>{$form.cmdSubmit.html}</p>
{$form.endtag}

{include file="__mng_footer.tpl"}
