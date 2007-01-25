<h1>{t w="please_login"}</h1>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item=error}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{$form.tag}
    <table width="480" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" colspan="2">
                {t w="please_login"}
            </th>
        </tr>
        <tr>
            <td class="adminRowL" width="160">{$form.loginName.label}</td>
            <td class="adminRowL" width="320">{$form.loginName.html}</td>
        </tr>
        <tr>
            <td class="adminRowL">{$form.loginPass.label}</td>
            <td class="adminRowL">{$form.loginPass.html}</td>
        </tr>
        <tr>
            <td class="adminRowL">&nbsp;</td>
            <td class="adminRowL">{$form.loginRememberMe.html} {$form.loginRememberMe.label}</td>
        </tr>
    </table>
    <p>{$form.cmdSubmit.html}</p>
{$form.endtag}
