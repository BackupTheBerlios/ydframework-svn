{include file="__mng_header.tpl"}

<p class="title">{t w="h_global_settings"} &raquo; {t w="a_users"}</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    <p><a href="{$YD_SELF_SCRIPT}?do=edit">{t w="add_user"}</a></p>

    {if $users}
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="25%" style="vertical-align: bottom;">{t w="username"}</th>
            <th class="adminRowL" width="30%" style="vertical-align: bottom;">{t w="useremail"}</th>
            <th class="adminRowL" width="20%" style="vertical-align: bottom;">{t w="created"}</th>
            <th class="adminRowR" width="25%" style="vertical-align: bottom;">{t w="actions"}</th>
        </tr>
        {foreach from=$users item="usr"}
            <tr>
                <td class="adminRowL">{$usr.name}</td>
                <td class="adminRowL"><a href="mailto:{$usr.email}">{$usr.email}</a></td>
                <td class="adminRowL">{$usr.created|date|lower}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$usr.id}">{t w="edit"}</a>
                    |
                    {if strtolower( $usr.name ) neq strtolower( $user.name )}
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$usr.id}" onClick="return YDConfirmDelete( '{$usr.name}' );">{t w="delete"}</a>
                    {else}
                        <span class="disabled">{t w="delete"}</span>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </table>
    {/if}

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        {$form.id.html}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">
                    {if $user_data}
                        {t w="change_user_desc"} {$user_data.name}
                    {else}
                        {t w="add_user"}
                    {/if}
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.name.label_html}</td>
                <td class="adminRowC" width="400">
                    {if $user_data}
                        <b>{$user_data.name}</b>
                    {else}
                        {$form.name.html}
                    {/if}
                </td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.email.label_html}</td>
                <td class="adminRowL">{$form.email.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.password.label_html}</td>
                <td class="adminRowL">{$form.password.html}</td>
            </tr>
        </table>
        <p>{$form.cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
