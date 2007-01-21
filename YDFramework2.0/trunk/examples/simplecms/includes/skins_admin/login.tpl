<h1>{t w="please_login"}</h1>

{if $form.errors}
    <p style="color: red">
        {foreach from=$form.errors item=error}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{$form.tag}
    <p>
        {$form.loginName.label}
        <br>
        {$form.loginName.html}
    </p>
    <p>
        {$form.loginPass.label}
        <br>
        {$form.loginPass.html}
    </p>
    <p>
        {$form.loginRememberMe.html}
        {$form.loginRememberMe.label}
    </p>
    <p>
        {$form.cmdSubmit.html}
    </p>
{$form.endtag}
