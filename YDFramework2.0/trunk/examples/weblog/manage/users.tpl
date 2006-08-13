{include file="__mng_header.tpl"}

<p class="title">{t w="h_global_settings"} &raquo; {t w="a_users"}</p>

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

    <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th colspan="2" class="adminRowLG">&raquo; {t w="a_users"}</th>
        <th colspan="2" class="adminRowLGR">
            <a href="{$YD_SELF_SCRIPT}?do=edit"><img src="images/icon_add.gif" border="0" /></a>
            <a href="{$YD_SELF_SCRIPT}?do=edit"><b>{t w="add_user"}</b></a>
        </th>
    </tr>
    {if $users}
        <tr>
            <th class="adminRowL" width="20%" style="vertical-align: bottom;">{t w="username"}</th>
            <th class="adminRowL" width="30%" style="vertical-align: bottom;">{t w="useremail"}</th>
            <th class="adminRowL" width="25%" style="vertical-align: bottom;">{t w="created"}</th>
            <th class="adminRowR" width="25%" style="vertical-align: bottom;">{t w="actions"}</th>
        </tr>
        {foreach from=$users item="usr"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$usr.name}</td>
                <td class="adminRowL"><a href="mailto:{$usr.email}">{$usr.email}</a>&nbsp;</td>
                <td class="adminRowL">{$usr.created|date|lower}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$usr.id}">{t w="edit"}</a>
                    |
                    {if strtolower( $usr.name ) neq strtolower( $user.name )}
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$usr.id}" onClick="return YDConfirmDelete( '{$usr.name|addslashes}' );">{t w="delete"}</a>
                    {else}
                        <span class="disabled">{t w="delete"}</span>
                    {/if}
                </td>
            </tr>
        {/foreach}
    {/if}
    </table>

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        {$form.id.html}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG">&raquo; 
                    {if $user_data}
                        {t w="change_user_desc"} {$user_data.name}
                    {else}
                        {t w="add_user"}
                    {/if}
                </th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.name.label_html}</td>
                <td class="adminRowL" width="400">
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
